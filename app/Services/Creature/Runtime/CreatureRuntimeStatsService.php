<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

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

    /**
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
    public function resolve(Creature $creature, string $entity = 'monster'): array
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

        $levelsPayload = [];
        $lastUnresolved = [];

        foreach ($levels as $level) {
            $resolved = $this->resolveAtLevel(
                $creature,
                $entity,
                $level,
                $objectByKey,
                $dbColumnByKey
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
        array $dbColumnByKey
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
            $total = $this->parseNumeric($creature->getAttribute($column));
            $object = (float) ($objectByKey[$key] ?? 0);
            $contextRaw = $creature->contextBonusRaw($column);
            $context = $this->evaluateContext($contextRaw, $variables);
            $base = $this->evaluateBaseFormula($key, $entity, $variables) ?? 0.0;
            $characteristics[$key] = $this->buildCharacteristicRow(
                $key,
                $entity,
                $base,
                $object,
                $context,
                $total,
                'total_column',
                $contextRaw,
                $variables
            );
            $variables[$key] = $total;
            $resolvedKeys[$key] = true;
        }
        $variables = FormulaVariableResolver::withShortNames('creature', $variables);

        // 2) Caractéristiques calculées / composées (passes itératives).
        $computedKeys = $this->listComposableAndComputedKeys($entity, $dbColumnByKey);
        $computedKeySet = array_fill_keys($computedKeys, true);
        $maxPasses = max(32, count($computedKeys) + 5);
        for ($pass = 0; $pass < $maxPasses; $pass++) {
            $this->injectIntimidationAbilityModifier($creature, $variables);
            $variables = FormulaVariableResolver::withShortNames('creature', $variables);
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
                $context = $this->evaluateContext($contextRaw, $variables);
                $base = $this->evaluateBaseFormula($key, $entity, $variables);

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
                // Si la formule inclut déjà les clés *_object, ne pas recompter la couche objets.
                $objectAlreadyInFormula = $hasFormula && str_contains((string) $def['formula'], '_object]');
                $total = $objectAlreadyInFormula
                    ? $baseValue + $context
                    : $baseValue + $object + $context;
                $characteristics[$key] = $this->buildCharacteristicRow(
                    $key,
                    $entity,
                    $objectAlreadyInFormula ? ($baseValue - $object) : $baseValue,
                    $object,
                    $context,
                    $total,
                    'composed',
                    $contextRaw,
                    $variables
                );
                $variables[$key] = $total;
                $variables = FormulaVariableResolver::withShortNames('creature', $variables);
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
            $context = $this->evaluateContext($contextRaw, $variables);
            $base = $this->evaluateBaseFormula($key, $entity, $variables) ?? 0.0;
            $object = (float) $amount;
            $formula = is_string($def['formula'] ?? null) ? (string) $def['formula'] : '';
            $objectAlreadyInFormula = $formula !== '' && str_contains($formula, '_object]');
            $total = $objectAlreadyInFormula ? $base + $context : $base + $object + $context;
            $characteristics[$key] = $this->buildCharacteristicRow(
                $key,
                $entity,
                $objectAlreadyInFormula ? ($base - $object) : $base,
                $object,
                $context,
                $total,
                'composed',
                $contextRaw,
                $variables
            );
            $variables[$key] = $total;
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

        $this->applyRuntimeTotalCaps($characteristics, $variables);

        return [
            'characteristics' => $characteristics,
            'variables' => $this->normalizeNumericMap($variables),
            'unresolved_computed_keys' => $unresolved,
        ];
    }

    /**
     * Plafonds métier après composition base + objet + contexte.
     *
     * @param  array<string, array<string, mixed>>  $characteristics
     * @param  array<string, float|int>  $variables
     */
    private function applyRuntimeTotalCaps(array &$characteristics, array &$variables): void
    {
        if (isset($characteristics['wakfu_reserve_creature'])) {
            $row = &$characteristics['wakfu_reserve_creature'];
            $total = max(1.0, min(9.0, (float) ($row['total'] ?? 0)));
            $row['total'] = $total;
            $variables['wakfu_reserve_creature'] = $total;
        }

        if (isset($characteristics['summoning_creature'])) {
            $mastery = (float) ($variables['mastery_bonus_creature'] ?? 0);
            if ($mastery > 0) {
                $row = &$characteristics['summoning_creature'];
                $total = min((float) ($row['total'] ?? 0), $mastery);
                $row['total'] = $total;
                $variables['summoning_creature'] = $total;
            }
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
        float $total,
        string $source,
        ?string $contextRaw,
        array $variables
    ): array {
        $def = $this->getter->getDefinition($key, $entity);
        $formula = is_string($def['formula'] ?? null) ? $def['formula'] : null;
        $ids = $this->formulas->extractVariablePlaceholders($formula);
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
            'total' => $total,
            'source' => $source,
            'context_raw' => $contextRaw,
            'formula' => $formula,
            'formula_display' => isset($def['formula_display']) && is_string($def['formula_display'])
                ? $def['formula_display']
                : null,
            'substituted' => $this->formulas->substitutePlaceholdersForDisplay($formula, $variables),
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
