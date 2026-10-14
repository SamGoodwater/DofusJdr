<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Characteristics\CharacteristicGroupRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Liste les caractéristiques d’un groupe (ou de tous) ayant une grille de normes définie.
 * Sert au template de section « characteristic_norms_catalog » et à l’outil Barème.
 */
class CharacteristicNormsCatalogController extends Controller
{
    public function show(Request $request, string $group, string $entity = '*'): JsonResponse
    {
        $groups = $this->resolveGroups($group);
        if ($groups === null) {
            return response()->json([
                'error' => 'Groupe invalide. Utiliser all, creature, object, spell, consumable ou profile.',
            ], 404);
        }

        $entity = $entity === '' ? '*' : $entity;

        $items = collect();
        foreach ($groups as $resolvedGroup) {
            $rows = $this->queryPivotRows($resolvedGroup, $entity);
            if ($rows->isEmpty() && $entity !== '*') {
                $rows = $this->queryPivotRows($resolvedGroup, '*');
            }

            $items = $items->merge(
                $rows
                    ->filter(fn ($row) => $row->characteristic !== null && is_array($row->norms_grid))
                    ->map(function ($row) use ($resolvedGroup) {
                        $effective = $row->characteristic->effectiveCharacteristic();

                        return [
                            'key' => $effective->key,
                            'name' => $effective->name ?? $effective->key,
                            'group' => $resolvedGroup,
                            'icon' => $effective->icon,
                            'color' => $effective->color,
                        ];
                    })
            );
        }

        $items = $items
            ->unique(fn (array $item) => $item['group'].'|'.$item['key'])
            ->sortBy([
                ['group', 'asc'],
                ['name', 'asc'],
            ], SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $filterKeys = $this->parseKeysFilter($request->query('keys'));
        if ($filterKeys !== []) {
            $set = array_flip($filterKeys);
            $items = array_values(array_filter($items, fn (array $item) => isset($set[$item['key']])));
        }

        return response()->json([
            'group' => $group,
            'entity' => $entity,
            'items' => $items,
        ]);
    }

    /**
     * @return list<string>|null
     */
    private function resolveGroups(string $group): ?array
    {
        if ($group === 'all') {
            return CharacteristicGroupRegistry::groups();
        }
        if (! CharacteristicGroupRegistry::isValid($group)) {
            return null;
        }

        return [$group];
    }

    /**
     * @return Collection<int, mixed>
     */
    private function queryPivotRows(string $group, string $entity): Collection
    {
        return CharacteristicGroupRegistry::query($group)
            ->where('entity', $entity)
            ->whereNotNull('norms_grid')
            ->with(['characteristic'])
            ->get();
    }

    /**
     * @return list<string>
     */
    private function parseKeysFilter(mixed $keysParam): array
    {
        if ($keysParam === null || $keysParam === '') {
            return [];
        }
        if (is_string($keysParam)) {
            $parts = preg_split('/[,\s]+/', $keysParam, -1, PREG_SPLIT_NO_EMPTY);

            return $parts === false ? [] : array_values(array_filter(array_map('trim', $parts)));
        }
        if (is_array($keysParam)) {
            return array_values(array_filter(array_map(static fn ($k) => is_string($k) ? trim($k) : '', $keysParam)));
        }

        return [];
    }
}
