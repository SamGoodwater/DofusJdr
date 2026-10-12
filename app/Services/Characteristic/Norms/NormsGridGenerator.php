<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Norms;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Formula\FormulaResolutionService;
use App\Services\Characteristic\Formula\SafeExpressionEvaluator;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;

/**
 * Génère norms_grid (5 × 20) à partir de norms_formula.
 *
 * Variables : [level] (1–20), [power] (−2…+2), [clé] = norme de la clé
 * au même niveau et même palier (ex. [life_points_creature]).
 *
 * @example
 * $gen->regenerateAll();
 * $grid = $gen->preview($formula, $dependencyGrids);
 */
final class NormsGridGenerator
{
    /** @var list<string> */
    public const BUILTIN_VARS = ['level', 'power'];

    /** @var array<string, int> */
    public const POWER_OFFSETS = [
        'very_weak' => -2,
        'weak' => -1,
        'neutral' => 0,
        'strong' => 1,
        'very_strong' => 2,
    ];

    public function __construct(
        private readonly FormulaResolutionService $formulas,
    ) {}

    /**
     * Régénère toutes les lignes ayant une norms_formula (ordre topologique).
     *
     * @return int Nombre de grilles écrites
     */
    public function regenerateAll(): int
    {
        $rows = $this->loadFormulaRows();
        $order = $this->topologicalOrder(array_keys($rows));
        $written = 0;
        $cache = $this->seedGridCache();

        foreach ($order as $cacheKey) {
            $row = $rows[$cacheKey] ?? null;
            if ($row === null) {
                continue;
            }
            $grid = $this->evaluateFormulaToGrid(
                (string) $row['model']->norms_formula,
                $cache,
                $cacheKey
            );
            $row['model']->norms_grid = $grid;
            $row['model']->save();
            $cache[$cacheKey] = $grid;
            $written++;
        }

        return $written;
    }

    /**
     * Régénère une clé (et ses dépendants en cascade).
     *
     * @return int Nombre de grilles écrites
     */
    public function regenerateKey(string $characteristicKey, ?string $entity = null): int
    {
        $rows = $this->loadFormulaRows();
        $targets = [];
        foreach ($rows as $cacheKey => $row) {
            if ($row['key'] !== $characteristicKey) {
                continue;
            }
            if ($entity !== null && $row['entity'] !== $entity) {
                continue;
            }
            $targets[] = $cacheKey;
        }
        if ($targets === []) {
            return 0;
        }

        $order = $this->topologicalOrder(array_keys($rows));
        $needed = $this->cascadeKeys($targets, $rows);
        $cache = $this->seedGridCache();
        $written = 0;

        foreach ($order as $cacheKey) {
            if (! isset($needed[$cacheKey])) {
                continue;
            }
            $row = $rows[$cacheKey];
            $grid = $this->evaluateFormulaToGrid(
                (string) $row['model']->norms_formula,
                $cache,
                $cacheKey
            );
            $row['model']->norms_grid = $grid;
            $row['model']->save();
            $cache[$cacheKey] = $grid;
            $written++;
        }

        return $written;
    }

    /**
     * Aperçu d'une grille sans écriture.
     *
     * @param  array<string, array<string, list<int|float|null>>>  $dependencyGrids  cacheKey => grid
     * @return array<string, list<int|float|null>>
     */
    /**
     * @param  array<string, array<string, list<int|float|null>>>  $dependencyGrids
     * @param  bool  $includeDatabase  Fusionne les grilles déjà en base (aperçu admin)
     * @return array<string, list<int|float|null>>
     */
    public function preview(string $formula, array $dependencyGrids = [], bool $includeDatabase = false): array
    {
        $errors = $this->formulas->validateFormula($formula);
        if ($errors !== []) {
            throw new InvalidArgumentException(implode('; ', $errors));
        }

        $cache = $includeDatabase
            ? array_merge($this->seedGridCache(), $dependencyGrids)
            : $dependencyGrids;

        return $this->evaluateFormulaToGrid($formula, $cache, 'preview');
    }

    /**
     * Valide une formule de normes et ses clés référencées.
     *
     * @return list<string>
     */
    public function validateNormsFormula(?string $formula): array
    {
        if ($formula === null || trim($formula) === '') {
            return [];
        }
        $errors = $this->formulas->validateFormula($formula);
        foreach ($this->formulas->extractVariablePlaceholders($formula) as $placeholder) {
            if (in_array($placeholder, self::BUILTIN_VARS, true)) {
                continue;
            }
            if (! Characteristic::query()->where('key', $placeholder)->exists()) {
                $errors[] = "Caractéristique inconnue dans norms_formula : [{$placeholder}]";
            }
        }

        return $errors;
    }

    /**
     * @param  list<string>  $cacheKeys  clés « group|entity|key »
     * @return list<string>
     */
    public function topologicalOrder(array $cacheKeys): array
    {
        $rows = $this->loadFormulaRows();
        $deps = [];
        foreach ($cacheKeys as $cacheKey) {
            $row = $rows[$cacheKey] ?? null;
            if ($row === null) {
                $deps[$cacheKey] = [];

                continue;
            }
            $placeholders = $this->formulas->extractVariablePlaceholders((string) $row['model']->norms_formula);
            $depKeys = [];
            foreach ($placeholders as $placeholder) {
                if (in_array($placeholder, self::BUILTIN_VARS, true)) {
                    continue;
                }
                foreach ($cacheKeys as $other) {
                    $otherRow = $rows[$other] ?? null;
                    if ($otherRow !== null && $otherRow['key'] === $placeholder) {
                        $depKeys[] = $other;
                    }
                }
            }
            $deps[$cacheKey] = array_values(array_unique($depKeys));
        }

        $sorted = [];
        $visiting = [];
        $visited = [];
        $visit = function (string $node) use (&$visit, &$deps, &$sorted, &$visiting, &$visited): void {
            if (isset($visited[$node])) {
                return;
            }
            if (isset($visiting[$node])) {
                throw new RuntimeException("Cycle détecté dans norms_formula autour de {$node}");
            }
            $visiting[$node] = true;
            foreach ($deps[$node] ?? [] as $dep) {
                $visit($dep);
            }
            unset($visiting[$node]);
            $visited[$node] = true;
            $sorted[] = $node;
        };

        foreach ($cacheKeys as $cacheKey) {
            $visit($cacheKey);
        }

        return $sorted;
    }

    /**
     * @param  array<string, array<string, list<int|float|null>>>  $cache
     * @return array<string, list<int|float|null>>
     */
    private function evaluateFormulaToGrid(string $formula, array $cache, string $selfCacheKey): array
    {
        $placeholders = $this->formulas->extractVariablePlaceholders($formula);
        $grid = [];
        foreach (NormsResolver::POWER_LEVELS as $powerKey) {
            $power = self::POWER_OFFSETS[$powerKey];
            $row = [];
            for ($level = 1; $level <= NormsResolver::MAX_LEVEL; $level++) {
                $variables = [
                    'level' => $level,
                    'power' => $power,
                ];
                foreach ($placeholders as $placeholder) {
                    if (in_array($placeholder, self::BUILTIN_VARS, true)) {
                        continue;
                    }
                    $variables[$placeholder] = $this->resolveDependencyValue(
                        $placeholder,
                        $level,
                        $powerKey,
                        $cache,
                        $selfCacheKey
                    );
                }
                $value = $this->formulas->evaluate(
                    $formula,
                    $variables,
                    SafeExpressionEvaluator::DICE_MODE_AVERAGE
                );
                $row[] = $value === null ? null : (float) $value;
            }
            $grid[$powerKey] = $row;
        }

        return $grid;
    }

    /**
     * @param  array<string, array<string, list<int|float|null>>>  $cache
     */
    private function resolveDependencyValue(
        string $key,
        int $level,
        string $powerKey,
        array $cache,
        string $selfCacheKey
    ): float {
        $candidates = $this->candidateCacheKeys($key, $selfCacheKey);
        foreach ($candidates as $candidate) {
            $grid = $cache[$candidate] ?? null;
            if (! is_array($grid)) {
                continue;
            }
            $row = $grid[$powerKey] ?? null;
            if (! is_array($row)) {
                continue;
            }
            $value = $row[$level - 1] ?? null;
            if ($value !== null && is_numeric($value)) {
                return (float) $value;
            }
        }

        return 0.0;
    }

    /**
     * @return list<string>
     */
    private function candidateCacheKeys(string $key, string $selfCacheKey): array
    {
        [$selfGroup, $selfEntity] = array_pad(explode('|', $selfCacheKey, 3), 2, '*');
        $candidates = [
            "{$selfGroup}|{$selfEntity}|{$key}",
            "{$selfGroup}|*|{$key}",
            "creature|*|{$key}",
            "spell|spell|{$key}",
            "spell|*|{$key}",
            "object|*|{$key}",
            "object|consumable|{$key}",
        ];

        return array_values(array_unique($candidates));
    }

    /**
     * @return array<string, array{model: Model, key: string, entity: string, group: string}>
     */
    private function loadFormulaRows(): array
    {
        $out = [];
        $maps = [
            'creature' => CharacteristicCreature::query()->with('characteristic')->whereNotNull('norms_formula')->get(),
            'object' => CharacteristicObject::query()->with('characteristic')->whereNotNull('norms_formula')->get(),
            'spell' => CharacteristicSpell::query()->with('characteristic')->whereNotNull('norms_formula')->get(),
        ];
        foreach ($maps as $group => $collection) {
            foreach ($collection as $model) {
                $formula = trim((string) $model->norms_formula);
                if ($formula === '') {
                    continue;
                }
                $key = (string) ($model->characteristic?->key ?? '');
                if ($key === '') {
                    continue;
                }
                $cacheKey = $this->cacheKey($group, (string) $model->entity, $key);
                $out[$cacheKey] = [
                    'model' => $model,
                    'key' => $key,
                    'entity' => (string) $model->entity,
                    'group' => $group,
                ];
            }
        }

        return $out;
    }

    /**
     * @return array<string, array<string, list<int|float|null>>>
     */
    private function seedGridCache(): array
    {
        $cache = [];
        $maps = [
            'creature' => CharacteristicCreature::query()->with('characteristic')->get(),
            'object' => CharacteristicObject::query()->with('characteristic')->get(),
            'spell' => CharacteristicSpell::query()->with('characteristic')->get(),
        ];
        foreach ($maps as $group => $collection) {
            foreach ($collection as $model) {
                $key = (string) ($model->characteristic?->key ?? '');
                if ($key === '' || ! is_array($model->norms_grid)) {
                    continue;
                }
                $cache[$this->cacheKey($group, (string) $model->entity, $key)] = $model->norms_grid;
            }
        }

        return $cache;
    }

    /**
     * @param  list<string>  $targets
     * @param  array<string, array{model: Model, key: string, entity: string, group: string}>  $rows
     * @return array<string, true>
     */
    private function cascadeKeys(array $targets, array $rows): array
    {
        $needed = [];
        foreach ($targets as $t) {
            $needed[$t] = true;
        }
        $changed = true;
        while ($changed) {
            $changed = false;
            foreach ($rows as $cacheKey => $row) {
                if (isset($needed[$cacheKey])) {
                    continue;
                }
                $placeholders = $this->formulas->extractVariablePlaceholders((string) $row['model']->norms_formula);
                foreach ($placeholders as $placeholder) {
                    if (in_array($placeholder, self::BUILTIN_VARS, true)) {
                        continue;
                    }
                    foreach ($needed as $neededKey => $_) {
                        $neededRow = $rows[$neededKey] ?? null;
                        if ($neededRow !== null && $neededRow['key'] === $placeholder) {
                            $needed[$cacheKey] = true;
                            $changed = true;
                            break 2;
                        }
                    }
                }
            }
        }

        return $needed;
    }

    private function cacheKey(string $group, string $entity, string $key): string
    {
        return "{$group}|{$entity}|{$key}";
    }
}
