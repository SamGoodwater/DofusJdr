<?php

namespace App\Http\Controllers\Api\Table;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\CreatureTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * CreatureTraitTableController
 *
 * @description
 * Endpoint "Table v2" (TanStack Table) pour les traits de créature.
 * Retourne un `TableResponse` avec des cellules typées: `Cell{type,value,params}`.
 */
class CreatureTraitTableController extends Controller
{
    use InterpretsEntityTableFilters;
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    private const STATE_COLORS = [
        'raw' => 'neutral',
        'draft' => 'warning',
        'playable' => 'success',
        'archived' => 'error',
    ];

    private const LEVEL_OPTIONS = [
        ['value' => '0', 'label' => 'Invité'],
        ['value' => '1', 'label' => 'Utilisateur'],
        ['value' => '2', 'label' => 'Joueur'],
        ['value' => '3', 'label' => 'Maître de jeu'],
        ['value' => '4', 'label' => 'Admin'],
        ['value' => '5', 'label' => 'Super admin'],
    ];

    private function stateColor(?string $state): string
    {
        $s = (string) ($state ?? '');

        return self::STATE_COLORS[$s] ?? 'base';
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CreatureTrait::class);

        // Mode de réponse:
        // - (default) "cells" : `rows[]` contient `cells` déjà prêtes à rendre.
        // - "entities" : renvoie `entities[]` (données brutes + meta) pour laisser le frontend générer les `cells`.
        //   Objectif : supporter une architecture "field descriptors" (Option B).
        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';

        $filters = (array) ($request->input('filters', $request->input('filter', [])) ?? []);
        foreach (['state', 'read_level', 'write_level'] as $k) {
            if (! array_key_exists($k, $filters) && $request->has($k)) {
                $filters[$k] = $request->get($k);
            }
        }

        $search = $request->filled('search') ? (string) $request->get('search') : '';

        $sortsPayload = $request->input('sorts');
        $sort = (string) $request->get('sort', 'id');
        $order = (string) $request->get('order', 'desc');
        if (is_array($sortsPayload) && isset($sortsPayload[0]) && is_array($sortsPayload[0])) {
            $sort = (string) ($sortsPayload[0]['field'] ?? $sortsPayload[0]['column'] ?? $sort);
            $order = strtolower((string) ($sortsPayload[0]['dir'] ?? $sortsPayload[0]['order'] ?? $order));
        }
        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $query = CreatureTrait::query()
            ->visibleToUser($request->user())->with(['createdBy']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($this->hasFilterValue($filters, 'state')) {
            $this->applyEqualityFilter($query, 'state', $filters['state']);
        }
        if ($this->hasFilterValue($filters, 'read_level')) {
            $this->applyEqualityFilter($query, 'read_level', $filters['read_level'], 'int');
        }
        if ($this->hasFilterValue($filters, 'write_level')) {
            $this->applyEqualityFilter($query, 'write_level', $filters['write_level'], 'int');
        }

        $allowedSort = ['id', 'name', 'state', 'read_level', 'write_level', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', CreatureTrait::class),
            'createAny' => Gate::allows('createAny', CreatureTrait::class),
            'updateAny' => Gate::allows('updateAny', CreatureTrait::class),
            'deleteAny' => Gate::allows('deleteAny', CreatureTrait::class),
            'manageAny' => Gate::allows('manageAny', CreatureTrait::class),
        ];

        $filterOptions = [
            'state' => EntityState::options(),
            'read_level' => self::LEVEL_OPTIONS,
            'write_level' => self::LEVEL_OPTIONS,
        ];

        // Mode "entities" : retourner les entités brutes
        if ($format === 'entities') {
            $entities = $rows->map(function (CreatureTrait $a) {
                $createdBy = $a->createdBy;

                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'description' => $a->description,
                    'state' => (string) ($a->state ?? 'draft'),
                    'read_level' => (int) ($a->read_level ?? 0),
                    'write_level' => (int) ($a->write_level ?? 0),
                    'image' => $a->image,
                    'created_by' => $a->created_by,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'created_at' => $a->created_at?->toISOString(),
                    'updated_at' => $a->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'creature-traits',
                    'query' => [
                        'search' => $search,
                        'filters' => $filters,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => $filterOptions,
                    'pagination' => $pagination,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (CreatureTrait $a) {
            $showHref = route('entities.creature-traits.show', $a->id);
            $createdBy = $a->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');

            $state = (string) ($a->state ?? 'draft');

            $createdAtLabel = $a->created_at ? $a->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $a->created_at ? $a->created_at->getTimestamp() : 0;
            $updatedAtLabel = $a->updated_at ? $a->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $a->updated_at ? $a->updated_at->getTimestamp() : 0;

            return [
                'id' => $a->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $a->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $a->name,
                            'sortValue' => (string) $a->name,
                        ],
                    ],
                    'description' => [
                        'type' => 'text',
                        'value' => (string) ($a->description ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($a->description ?? ''),
                            'sortValue' => (string) ($a->description ?? ''),
                        ],
                    ],
                    'state' => [
                        'type' => 'badge',
                        'value' => $state,
                        'params' => [
                            'color' => $this->stateColor($state),
                            'filterValue' => $state,
                            'sortValue' => $state,
                        ],
                    ],
                    'read_level' => [
                        'type' => 'badge',
                        'value' => (string) ((int) ($a->read_level ?? 0)),
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) ((int) ($a->read_level ?? 0)),
                            'sortValue' => (int) ($a->read_level ?? 0),
                        ],
                    ],
                    'write_level' => [
                        'type' => 'badge',
                        'value' => (string) ((int) ($a->write_level ?? 0)),
                        'params' => [
                            'color' => 'info',
                            'filterValue' => (string) ((int) ($a->write_level ?? 0)),
                            'sortValue' => (int) ($a->write_level ?? 0),
                        ],
                    ],
                    'created_by' => [
                        'type' => 'text',
                        'value' => $createdByLabel,
                        'params' => [
                            'searchValue' => $createdByLabel,
                            'sortValue' => $createdByLabel,
                        ],
                    ],
                    'created_at' => [
                        'type' => 'text',
                        'value' => $createdAtLabel,
                        'params' => [
                            'sortValue' => $createdAtSort,
                            'searchValue' => $createdAtLabel,
                        ],
                    ],
                    'updated_at' => [
                        'type' => 'text',
                        'value' => $updatedAtLabel,
                        'params' => [
                            'sortValue' => $updatedAtSort,
                            'searchValue' => $updatedAtLabel,
                        ],
                    ],
                ],
                'rowParams' => [
                    'entity' => [
                        'id' => $a->id,
                        'name' => $a->name,
                        'description' => $a->description,
                        'state' => (string) ($a->state ?? 'draft'),
                        'read_level' => (int) ($a->read_level ?? 0),
                        'write_level' => (int) ($a->write_level ?? 0),
                        'image' => $a->image,
                        'created_by' => $a->created_by,
                        'createdBy' => $createdBy ? [
                            'id' => $createdBy->id,
                            'name' => $createdBy->name,
                            'email' => $createdBy->email,
                        ] : null,
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'creature-traits',
                'query' => [
                    'search' => $search,
                    'filters' => $filters,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => $filterOptions,
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }
}
