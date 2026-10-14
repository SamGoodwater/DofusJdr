<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Enums\CharacteristicNature;
use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\Entity\Creature;
use App\Services\Characteristic\Domain\LevelDomainResolver;
use App\Services\Characteristic\Formula\FormulaExpressionParser;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\FormulaVariableResolver;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Support\Creature\CreatureComposableColumns;

/**
 * Calcule les caractéristiques d'une créature niveau par niveau : base + objets + contexte,
 * avec priorité au total explicite stocké en colonne.
 *
 * Les termes de nature `buff_debuff` (`*_consumable`, `*_spell`) sont éphémères : la partie permanente
 * est évaluée avec ces termes à 0, bornée si la caractéristique a une limite absolue, puis la part
 * éphémère s'ajoute sans borne.
 *
 * @example $stats->resolve($npc, 'npc', ['initiative_spell' => 2])['levels'][0]['characteristics']['initiative_creature']['ephemeral'] // 2.0
 *
 * @see docs/features/characteristics/COMPUTED_VALUES.md
 */
final class CreatureRuntimeStatsService
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly FormulaResolutionService $formulas,
        private readonly FormulaExpressionParser $expressionParser,
        private readonly LevelDomainResolver $levelDomain,
        private readonly CreatureVariableMapBuilder $variableMapBuilder,
        private readonly CreatureItemBonusAggregator $itemBonusAggregator,
        private readonly CreatureAggregatedItemBonusLimiter $itemBonusLimiter,
        private readonly CreatureObjectBonusToCreatureVariables $objectBonusMerger,
        private readonly ProfileMasteryResolver $profileMasteryResolver,
    ) {}

    /** @var list<string>|null */
    private ?array $ephemeralKeysMemo = null;

    /**
     * @param  array<string, int|float>  $ephemeralValues  Valeurs des buffs actifs (clé buff_debuff => valeur), vide hors combat
     * @return array{
     *   entity: string,
     *   default_level: int,
     *   levels: list<array{level: int, characteristics: array<string, array<string, mixed>>, variables: array<string, float|int>}>,
     *   variables: array<string, float|int>,
     *   computed: array<string, array<string, mixed>>,
     *   unresolved_computed_keys: list<string>,
     *   items: array{aggregated: array<string, int>, lines: list<array{item_id: int, name: string, quantity: int, bonuses: array<string, int>}>}
     * }
     */
    public function resolve(Creature $creature, string $entity = 'monster', array $ephemeralValues = []): array
    {
        if (! in_array($entity, ['monster', 'class', 'npc'], true)) {
            $entity = 'monster';
        }

        $creature->loadMissing('items');

        $levels = $this->levelDomain->resolve($creature->level);
        $itemTotals = $this->itemBonusLimiter->apply(
            $this->itemBonusAggregator->aggregateTotals($creature->items)
        );
        $objectByKey = $this->objectBonusMerger->mapToCharacteristicKeys($entity, $itemTotals);
        $dbColumnByKey = $this->mapCharacteristicKeysToDbColumns($entity);
        $ephemeralKeys = $this->ephemeralCharacteristicKeys();
        $ephemeralValues = array_intersect_key($ephemeralValues, array_fill_keys($ephemeralKeys, true));

        $levelsPayload = [];
        $lastUnresolved = [];

        foreach ($levels as $level) {
            $resolved = $this->resolveAtLevel(
                $creature,
                $entity,
                $level,
                $objectByKey,
                $dbColumnByKey,
                $ephemeralValues
            );
            $levelsPayload[] = [
                'level' => $level,
                'characteristics' => $resolved['characteristics'],
                'variables' => $resolved['variables'],
            ];
            $lastUnresolved = $resolved['unresolved_computed_keys'];
        }

        $first = $levelsPayload[0] ?? [
            'level' => 1,
            'characteristics' => [],
            'variables' => [],
        ];

        $computedBc = [];
        foreach ($first['characteristics'] as $key => $row) {
            $computedBc[$key] = [
                'key' => $key,
                'value' => $row['total'],
                'base' => $row['base'],
                'object' => $row['object'],
                'context' => $row['context'],
                'ephemeral' => $row['ephemeral'],
                'permanent' => $row['permanent'],
                'capped' => $row['capped'],
                'source' => $row['source'],
                'formula' => $row['formula'],
                'formula_display' => $row['formula_display'],
                'substituted' => $row['substituted'],
                'placeholders' => $row['placeholders'],
                'context_raw' => $row['context_raw'],
            ];
        }

        return [
            'entity' => $entity,
            'default_level' => $first['level'],
            'levels' => $levelsPayload,
            'variables' => $first['variables'],
            'computed' => $computedBc,
            'unresolved_computed_keys' => $lastUnresolved,
            'items' => [
                'aggregated' => $itemTotals,
                'lines' => $this->itemBonusAggregator->aggregatePerItemLines($creature->items),
            ],
        ];
    }

    /**
     * @param  array<string, int>  $objectByKey
     * @param  array<string, string>  $dbColumnByKey
     * @param  array<string, int|float>  $ephemeralValues
     * @return array{
     *   characteristics: array<string, array<string, mixed>>,
     *   variables: array<string, float|int>,
     *   unresolved_computed_keys: list<string>
     * }
     */
    private function resolveAtLevel(
        Creature $creature,
        string $entity,
        int $level,
        array $objectByKey,
        array $dbColumnByKey,
        array $ephemeralValues = []
    ): array {
        $variables = $this->variableMapBuilder->buildBaseMap($creature, $entity);
        $variables = $this->stripContextColumnsFromVariables($variables);
        $variables['level_creature'] = $level;
        $variables['level'] = $level;
        $variables = FormulaVariableResolver::withShortNames('creature', $variables);

        foreach ($objectByKey as $key => $amount) {
            $variables[$key.'_object'] = (float) $amount;
        }

        foreach ($this->profileMasteryResolver->resolveForCreature($creature) as $profileKey => $tier) {
            $variables[$profileKey] = (float) $tier;
        }

        // Deux jeux : permanent (éphémères à 0, bornes absolues) et complet (buffs actifs inclus).
        $permanentVars = $variables;
        foreach ($ephemeralValues as $ephemeralKey => $value) {
            $variables[$ephemeralKey] = (float) $value;
            $permanentVars[$ephemeralKey] = 0.0;
        }
        $hasEphemeral = $ephemeralValues !== [];

        $characteristics = [];
        $resolvedKeys = [];

        // 1) Totaux explicites (colonnes non nulles) — priorité d'affichage.
        foreach ($dbColumnByKey as $key => $column) {
            if (! CreatureComposableColumns::isComposable($column)) {
                continue;
            }
            if (! $creature->hasExplicitTotal($column)) {
                continue;
            }
            $explicit = $this->parseNumeric($creature->getAttribute($column));
            $object = (float) ($objectByKey[$key] ?? 0);
            $contextRaw = $creature->contextBonusRaw($column);
            $context = $this->evaluateContext($contextRaw, $permanentVars);
            $basePermanent = $this->evaluateBaseFormula($key, $entity, $permanentVars) ?? 0.0;
            $ephemeral = $hasEphemeral
                ? ($this->evaluateBaseFormula($key, $entity, $variables) ?? 0.0) - $basePermanent
                : 0.0;
            [$permanent, $capped] = $this->applyAbsoluteLimit($key, $entity, $explicit, $permanentVars);
            $total = $permanent + $ephemeral;
            $this->storeResolvedCharacteristic(
                $characteristics,
                $variables,
                $permanentVars,
                $key,
                $entity,
                compact('basePermanent', 'object', 'context', 'ephemeral', 'permanent', 'capped', 'total'),
                'total_column',
                $contextRaw
            );
            $resolvedKeys[$key] = true;
        }
        $variables = FormulaVariableResolver::withShortNames('creature', $variables);
        $permanentVars = FormulaVariableResolver::withShortNames('creature', $permanentVars);

        // 2) Caractéristiques calculées / composées (passes itératives).
        $computedKeys = $this->listComposableAndComputedKeys($entity, $dbColumnByKey);
        $computedKeySet = array_fill_keys($computedKeys, true);
        $maxPasses = max(32, count($computedKeys) + 5);
        for ($pass = 0; $pass < $maxPasses; $pass++) {
            $this->injectIntimidationAbilityModifier($creature, $variables);
            $this->injectIntimidationAbilityModifier($creature, $permanentVars);
            $variables = FormulaVariableResolver::withShortNames('creature', $variables);
            $permanentVars = FormulaVariableResolver::withShortNames('creature', $permanentVars);
            $progress = false;
            foreach ($computedKeys as $key) {
                if (isset($resolvedKeys[$key])) {
                    continue;
                }

                $def = $this->getter->getDefinition($key, $entity);
                $hasFormula = is_string($def['formula'] ?? null) && trim((string) $def['formula']) !== '';
                // Ne pas figer une formule tant que ses dépendances calculées (mods, bonus maîtrise…)
                // ne sont pas résolues — sinon les placeholders manquants valent 0 trop tôt.
                if (
                    $hasFormula
                    && $this->formulaHasUnresolvedComputedDependency(
                        (string) $def['formula'],
                        $computedKeySet,
                        $resolvedKeys
                    )
                ) {
                    continue;
                }

                $column = $dbColumnByKey[$key] ?? null;
                $object = (float) ($objectByKey[$key] ?? 0);
                $contextRaw = is_string($column) ? $creature->contextBonusRaw($column) : null;
                $context = $this->evaluateContext($contextRaw, $permanentVars);
                $base = $this->evaluateBaseFormula($key, $entity, $permanentVars);

                // Si pas de formule de base et pas de contexte/objet, ignorer (sauf si formule pure sans colonne).
                if ($base === null && ! $hasFormula && $object === 0.0 && ($contextRaw === null || $context === 0.0)) {
                    if ($column === null) {
                        continue;
                    }
                    // Colonne composable sans total ni contexte → default_value seed, sinon 0
                    $default = $def['default_value'] ?? null;
                    $base = ($default !== null && $default !== '' && is_numeric($default))
                        ? (float) $default
                        : 0.0;
                }
                if ($base === null && $hasFormula) {
                    // Formule non encore résolue (dépendances manquantes) → attendre une autre passe.
                    continue;
                }

                $baseValue = $base ?? 0.0;
                $ephemeral = ($hasEphemeral && $hasFormula)
                    ? ($this->evaluateBaseFormula($key, $entity, $variables) ?? $baseValue) - $baseValue
                    : 0.0;
                // Si la formule inclut déjà les clés *_object, ne pas recompter la couche objets.
                $objectAlreadyInFormula = $hasFormula && str_contains((string) $def['formula'], '_object]');
                $rawPermanent = $objectAlreadyInFormula
                    ? $baseValue + $context
                    : $baseValue + $object + $context;
                [$permanent, $capped] = $this->applyAbsoluteLimit($key, $entity, $rawPermanent, $permanentVars);
                $total = $permanent + $ephemeral;
                $this->storeResolvedCharacteristic(
                    $characteristics,
                    $variables,
                    $permanentVars,
                    $key,
                    $entity,
                    [
                        'basePermanent' => $objectAlreadyInFormula ? ($baseValue - $object) : $baseValue,
                        'object' => $object,
                        'context' => $context,
                        'ephemeral' => $ephemeral,
                        'permanent' => $permanent,
                        'capped' => $capped,
                        'total' => $total,
                    ],
                    'composed',
                    $contextRaw
                );
                $variables = FormulaVariableResolver::withShortNames('creature', $variables);
                $permanentVars = FormulaVariableResolver::withShortNames('creature', $permanentVars);
                $resolvedKeys[$key] = true;
                $progress = true;
            }
            if (! $progress) {
                break;
            }
        }

        // Bonus objet sans formule / hors liste composable : exposer quand même la couche object.
        foreach ($objectByKey as $key => $amount) {
            if (isset($resolvedKeys[$key]) || (float) $amount === 0.0) {
                continue;
            }
            $def = $this->getter->getDefinition($key, $entity);
            if ($def === null) {
                continue;
            }
            $column = $dbColumnByKey[$key] ?? null;
            $contextRaw = is_string($column) ? $creature->contextBonusRaw($column) : null;
            $context = $this->evaluateContext($contextRaw, $permanentVars);
            $base = $this->evaluateBaseFormula($key, $entity, $permanentVars) ?? 0.0;
            $object = (float) $amount;
            $formula = is_string($def['formula'] ?? null) ? (string) $def['formula'] : '';
            $objectAlreadyInFormula = $formula !== '' && str_contains($formula, '_object]');
            $rawPermanent = $objectAlreadyInFormula ? $base + $context : $base + $object + $context;
            [$permanent, $capped] = $this->applyAbsoluteLimit($key, $entity, $rawPermanent, $permanentVars);
            $this->storeResolvedCharacteristic(
                $characteristics,
                $variables,
                $permanentVars,
                $key,
                $entity,
                [
                    'basePermanent' => $objectAlreadyInFormula ? ($base - $object) : $base,
                    'object' => $object,
                    'context' => $context,
                    'ephemeral' => 0.0,
                    'permanent' => $permanent,
                    'capped' => $capped,
                    'total' => $permanent,
                ],
                'composed',
                $contextRaw
            );
            $resolvedKeys[$key] = true;
        }
        $variables = FormulaVariableResolver::withShortNames('creature', $variables);

        $unresolved = [];
        foreach ($computedKeys as $key) {
            if (! isset($resolvedKeys[$key])) {
                $def = $this->getter->getDefinition($key, $entity);
                if ($def !== null && ! empty($def['formula'])) {
                    $unresolved[] = $key;
                }
            }
        }

        $this->applyRuntimeTotalCaps($characteristics, $variables, $entity);

        return [
            'characteristics' => $characteristics,
            'variables' => $this->normalizeNumericMap($variables),
            'unresolved_computed_keys' => $unresolved,
        ];
    }

    /**
     * Borne la partie permanente si la caractéristique porte une limite absolue.
     *
     * @param  array<string, float|int>  $permanentVars
     * @return array{0: float, 1: bool} [valeur bornée, plafonnée ?]
     */
    private function applyAbsoluteLimit(string $key, string $entity, float $value, array $permanentVars): array
    {
        $def = $this->getter->getDefinition($key, $entity);
        if ($def === null || empty($def['limit_absolute'])) {
            return [$value, false];
        }
        $limits = $this->getter->getLimits($key, $entity, $permanentVars);
        if ($limits === null) {
            return [$value, false];
        }
        $bounded = max((float) $limits['min'], min((float) $limits['max'], $value));

        return [$bounded, abs($bounded - $value) > 1e-9];
    }

    /**
     * Construit une ligne runtime puis propage le total et sa partie permanente.
     *
     * @param  array<string, array<string, mixed>>  $characteristics
     * @param  array<string, float|int>  $variables
     * @param  array<string, float|int>  $permanentVars
     * @param  array{
     *   basePermanent: float,
     *   object: float,
     *   context: float,
     *   ephemeral: float,
     *   permanent: float,
     *   capped: bool,
     *   total: float
     * }  $layers
     */
    private function storeResolvedCharacteristic(
        array &$characteristics,
        array &$variables,
        array &$permanentVars,
        string $key,
        string $entity,
        array $layers,
        string $source,
        ?string $contextRaw
    ): void {
        $characteristics[$key] = $this->buildCharacteristicRow(
            $key,
            $entity,
            $layers['basePermanent'],
            $layers['object'],
            $layers['context'],
            $layers['ephemeral'],
            $layers['permanent'],
            $layers['capped'],
            $layers['total'],
            $source,
            $contextRaw,
            $variables
        );
        $variables[$key] = $layers['total'];
        $permanentVars[$key] = $layers['permanent'];
    }

    /**
     * Retire les termes éphémères additifs ou soustractifs (`±[clé_buff]`) d'une formule, hors combat.
     */
    private function stripEphemeralTerms(?string $formula): ?string
    {
        if ($formula === null || $formula === '') {
            return $formula;
        }
        $keys = $this->ephemeralCharacteristicKeys();
        if ($keys === []) {
            return $formula;
        }
        $alts = implode('|', array_map(static fn (string $k): string => preg_quote($k, '/'), $keys));
        $pattern = '/\\s*[+\\-]\\s*\\[(?:'.$alts.')\\]/';

        return (string) preg_replace($pattern, '', $formula);
    }

    /**
     * Clés de nature buff_debuff (termes éphémères des formules).
     *
     * @return list<string>
     */
    private function ephemeralCharacteristicKeys(): array
    {
        if ($this->ephemeralKeysMemo !== null) {
            return $this->ephemeralKeysMemo;
        }

        /** @var list<string> $keys */
        $keys = Characteristic::query()
            ->where('nature', CharacteristicNature::BuffDebuff->value)
            ->pluck('key')
            ->all();

        return $this->ephemeralKeysMemo = $keys;
    }

    /**
     * Plafonds métier après composition : bornes lues depuis min/max (Wakfu 1–9),
     * plus plafond dynamique des invocations par le bonus de maîtrise.
     *
     * @param  array<string, array<string, mixed>>  $characteristics
     * @param  array<string, float|int>  $variables
     */
    private function applyRuntimeTotalCaps(array &$characteristics, array &$variables, string $entity): void
    {
        if (isset($characteristics['wakfu_reserve_creature'])) {
            $limits = $this->getter->getLimits('wakfu_reserve_creature', $entity, $variables);
            if ($limits !== null) {
                $row = &$characteristics['wakfu_reserve_creature'];
                $total = max((float) $limits['min'], min((float) $limits['max'], (float) ($row['total'] ?? 0)));
                $row['total'] = $total;
                $variables['wakfu_reserve_creature'] = $total;
            }
        }

        if (isset($characteristics['summoning_creature'])) {
            $row = &$characteristics['summoning_creature'];
            $total = (float) ($row['total'] ?? 0);
            $limits = $this->getter->getLimits('summoning_creature', $entity, $variables);
            if ($limits !== null) {
                $total = max((float) $limits['min'], min((float) $limits['max'], $total));
            }
            // Plafond dynamique métier : ne jamais dépasser le bonus de maîtrise actif.
            $mastery = (float) ($variables['mastery_bonus_creature'] ?? 0);
            if ($mastery > 0) {
                $total = min($total, $mastery);
            }
            $row['total'] = $total;
            $variables['summoning_creature'] = $total;
        }
    }

    /**
     * @param  array<string, float|int>  $variables
     * @return array<string, mixed>
     */
    private function buildCharacteristicRow(
        string $key,
        string $entity,
        float $base,
        float $object,
        float $context,
        float $ephemeral,
        float $permanent,
        bool $capped,
        float $total,
        string $source,
        ?string $contextRaw,
        array $variables
    ): array {
        $def = $this->getter->getDefinition($key, $entity);
        $formula = is_string($def['formula'] ?? null) ? $def['formula'] : null;
        $shownFormula = $ephemeral === 0.0 ? $this->stripEphemeralTerms($formula) : $formula;
        $ids = $this->formulas->extractVariablePlaceholders($shownFormula);
        $placeholders = [];
        foreach ($ids as $id) {
            $placeholders[] = [
                'id' => $id,
                'value' => isset($variables[$id]) ? (float) $variables[$id] : 0.0,
            ];
        }

        return [
            'key' => $key,
            'base' => $base,
            'object' => $object,
            'context' => $context,
            'ephemeral' => $ephemeral,
            'permanent' => $permanent,
            'capped' => $capped,
            'total' => $total,
            'source' => $source,
            'context_raw' => $contextRaw,
            'formula' => $formula,
            'formula_display' => isset($def['formula_display']) && is_string($def['formula_display'])
                ? $def['formula_display']
                : null,
            'substituted' => $this->formulas->substitutePlaceholdersForDisplay($shownFormula, $variables),
            'placeholders' => $placeholders,
        ];
    }

    /**
     * @param  array<string, true>  $computedKeySet
     * @param  array<string, true>  $resolvedKeys
     */
    private function formulaHasUnresolvedComputedDependency(
        string $formula,
        array $computedKeySet,
        array $resolvedKeys
    ): bool {
        foreach ($this->formulas->extractVariablePlaceholders($formula) as $id) {
            if ($id === '' || ! isset($computedKeySet[$id])) {
                continue;
            }
            if (! isset($resolvedKeys[$id])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, float|int>  $variables
     */
    private function evaluateBaseFormula(string $key, string $entity, array $variables): ?float
    {
        $def = $this->getter->getDefinition($key, $entity);
        if ($def === null) {
            return null;
        }
        $formula = $def['formula'] ?? null;
        if (! is_string($formula) || trim($formula) === '') {
            return null;
        }

        return $this->formulas->evaluate($formula, $variables, SafeExpressionEvaluator::DICE_MODE_MIN);
    }

    /**
     * @param  array<string, float|int>  $variables
     */
    private function evaluateContext(?string $raw, array $variables): float
    {
        if ($raw === null || trim($raw) === '') {
            return 0.0;
        }
        $value = $this->expressionParser->evaluate($raw, $variables, SafeExpressionEvaluator::DICE_MODE_MIN);

        return $value ?? 0.0;
    }

    /**
     * @return array<string, string> characteristic_key => db_column
     */
    private function mapCharacteristicKeysToDbColumns(string $entity): array
    {
        $map = [];
        $rows = CharacteristicCreature::query()
            ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, $entity])
            ->whereNotNull('db_column')
            ->with('characteristic')
            ->get()
            ->groupBy('characteristic_id');

        foreach ($rows as $group) {
            $base = $group->firstWhere('entity', CharacteristicCreature::ENTITY_ALL);
            $overlay = $group->firstWhere('entity', $entity);
            $row = $overlay ?? $base;
            if ($row === null || $row->characteristic === null) {
                continue;
            }
            $col = (string) $row->db_column;
            if ($col === '') {
                continue;
            }
            $map[$row->characteristic->key] = $col;
        }

        return $map;
    }

    /**
     * @param  array<string, string>  $dbColumnByKey
     * @return list<string>
     */
    private function listComposableAndComputedKeys(string $entity, array $dbColumnByKey): array
    {
        $keys = [];

        foreach ($dbColumnByKey as $key => $column) {
            if (CreatureComposableColumns::isComposable($column)) {
                $keys[$key] = true;
            }
        }

        $ids = CharacteristicCreature::query()
            ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, $entity])
            ->whereNotNull('formula')
            ->distinct()
            ->pluck('characteristic_id');

        foreach (Characteristic::query()->whereIn('id', $ids)->orderBy('id')->pluck('key') as $key) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            $def = $this->getter->getDefinition($key, $entity);
            $formula = $def['formula'] ?? null;
            if ($def === null || ! is_string($formula) || trim($formula) === '') {
                continue;
            }
            $keys[$key] = true;
        }

        return array_keys($keys);
    }

    /**
     * @param  array<string, float|int>  $variables
     * @return array<string, float|int>
     */
    private function stripContextColumnsFromVariables(array $variables): array
    {
        foreach (CreatureComposableColumns::contextColumns() as $contextCol) {
            unset($variables[$contextCol]);
        }

        return $variables;
    }

    /**
     * @param  array<string, int|float>  $map
     * @return array<string, float|int>
     */
    private function normalizeNumericMap(array $map): array
    {
        $out = [];
        foreach ($map as $k => $v) {
            $out[$k] = is_float($v) && floor($v) === $v ? (int) $v : (float) $v;
        }

        return $out;
    }

    private function parseNumeric(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        return 0.0;
    }

    /**
     * Modificateur Force ou Chance pour Intimidation (choix fiche créature).
     *
     * @param  array<string, float|int>  $variables
     */
    private function injectIntimidationAbilityModifier(Creature $creature, array &$variables): void
    {
        $ability = (string) ($creature->intimidation_ability ?? 'strength');
        $modKey = $ability === 'chance'
            ? 'modifier_chance_creature'
            : 'modifier_strength_creature';

        if (! array_key_exists($modKey, $variables)) {
            return;
        }

        $value = (float) $variables[$modKey];
        $variables['intimidation_ability_modifier_creature'] = $value;
        $variables['intimidation_ability_modifier'] = $value;
    }
}
