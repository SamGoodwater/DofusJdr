<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Http\Resources\Entity\CapabilityResource;
use App\Http\Resources\Entity\LanguageResource;
use App\Http\Resources\Entity\SpellResource;
use App\Models\Entity\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Endpoint "Table v2" (TanStack Table) pour les breeds (affichées « Classes »).
 */
class BreedTableController extends Controller
{
    use InterpretsEntityTableSort;
    use PaginatesEntityTable;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Breed::class);

        $format = $request->filled('format') ? (string) $request->get('format') : 'cells';
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

        $viewer = $request->user();
        $query = Breed::query()
            ->visibleToUser($viewer)
            ->with(['createdBy', 'elementOrientations', 'languages'])
            ->withCount(['spells' => fn ($q) => $q->visibleToUser($viewer)]);

        if ($format === 'entities') {
            $query->with([
                'capabilities' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
                'spells' => fn ($q) => $q
                    ->visibleToUser($viewer)
                    ->orderBy('breed_spell.character_level')
                    ->orderBy('breed_spell.slot_index')
                    ->orderBy('breed_spell.choice_order')
                    ->orderBy('spells.name'),
            ]);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('specificity', 'like', "%{$search}%")
                    ->orWhere('mechanics', 'like', "%{$search}%");
            });
        }

        $allowedSort = ['id', 'name', 'life_dice', 'dofusdb_id', 'created_at', 'updated_at'];
        $this->applyEntityTableSort($query, $request, $allowedSort, 'id', 'desc');

        $pageResult = $this->paginateEntityTable($query, $request);
        $rows = $pageResult['rows'];
        $limit = $pageResult['limit'];
        $page = $pageResult['page'];
        $pagination = $pageResult['pagination'];

        $capabilities = [
            'viewAny' => Gate::allows('viewAny', Breed::class),
            'createAny' => Gate::allows('createAny', Breed::class),
            'updateAny' => Gate::allows('updateAny', Breed::class),
            'deleteAny' => Gate::allows('deleteAny', Breed::class),
            'manageAny' => Gate::allows('manageAny', Breed::class),
        ];

        if ($format === 'entities') {
            $entities = $rows->map(function (Breed $c) use ($request) {
                $createdBy = $c->createdBy;

                return [
                    'id' => $c->id,
                    'official_id' => $c->official_id,
                    'dofusdb_id' => $c->dofusdb_id,
                    'name' => $c->name,
                    'description_fast' => $c->description_fast,
                    'description' => $c->description,
                    'evolution' => $c->evolution,
                    'life_dice' => $c->life_dice,
                    'specificity' => $c->specificity,
                    'dofus_version' => $c->dofus_version,
                    'state' => (string) ($c->state ?? 'draft'),
                    'read_level' => (int) ($c->read_level ?? 0),
                    'write_level' => (int) ($c->write_level ?? 0),
                    'image' => $c->image,
                    'icon' => $c->icon,
                    'symbol_full' => $c->symbol_full,
                    'symbol_bw' => $c->symbol_bw,
                    'logo_male' => $c->logo_male,
                    'logo_female' => $c->logo_female,
                    'image_full_male' => $c->image_full_male,
                    'image_full_female' => $c->image_full_female,
                    'auto_update' => (bool) $c->auto_update,
                    'created_by' => $c->created_by,
                    'createdBy' => $createdBy ? [
                        'id' => $createdBy->id,
                        'name' => $createdBy->name,
                        'email' => $createdBy->email,
                    ] : null,
                    'spells_count' => (int) ($c->spells_count ?? 0),
                    'spells' => SpellResource::collection($c->spells)->resolve($request),
                    'capabilities' => CapabilityResource::collection($c->capabilities)->resolve($request),
                    'languages' => LanguageResource::collection($c->languages)->resolve($request),
                    'element_orientations' => $c->elementOrientationsMap(),
                    'created_at' => $c->created_at?->toISOString(),
                    'updated_at' => $c->updated_at?->toISOString(),
                ];
            })->values()->all();

            return response()->json([
                'meta' => [
                    'entityType' => 'breeds',
                    'query' => [
                        'search' => $search,
                        'sort' => $sort,
                        'order' => $order,
                        'limit' => $limit,
                        'page' => $page,
                    ],
                    'capabilities' => $capabilities,
                    'filterOptions' => [],
                    'pagination' => $pagination,
                    'format' => 'entities',
                ],
                'entities' => $entities,
            ]);
        }

        $tableRows = $rows->map(function (Breed $c) use ($request) {
            $showHref = route('entities.breeds.show', $c->id);
            $createdBy = $c->createdBy;
            $createdByLabel = $createdBy?->name ?: ($createdBy?->email ?: '-');
            $createdAtLabel = $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-';
            $createdAtSort = $c->created_at ? $c->created_at->getTimestamp() : 0;
            $updatedAtLabel = $c->updated_at ? $c->updated_at->format('d/m/Y H:i') : '-';
            $updatedAtSort = $c->updated_at ? $c->updated_at->getTimestamp() : 0;

            return [
                'id' => $c->id,
                'cells' => [
                    'name' => [
                        'type' => 'route',
                        'value' => (string) $c->name,
                        'params' => [
                            'href' => $showHref,
                            'searchValue' => (string) $c->name,
                            'sortValue' => (string) $c->name,
                        ],
                    ],
                    'life_dice' => [
                        'type' => 'text',
                        'value' => $c->life_dice ?: '-',
                        'params' => [
                            'sortValue' => (string) ($c->life_dice ?? ''),
                        ],
                    ],
                    'specificity' => [
                        'type' => 'text',
                        'value' => (string) ($c->specificity ?? '-'),
                        'params' => [
                            'searchValue' => (string) ($c->specificity ?? ''),
                            'sortValue' => (string) ($c->specificity ?? ''),
                        ],
                    ],
                    'dofusdb_id' => [
                        'type' => 'text',
                        'value' => $c->dofusdb_id ?: '-',
                        'params' => [
                            'sortValue' => $c->dofusdb_id ?? 0,
                            'searchValue' => (string) ($c->dofusdb_id ?? ''),
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
                        'id' => $c->id,
                        'official_id' => $c->official_id,
                        'dofusdb_id' => $c->dofusdb_id,
                        'name' => $c->name,
                        'description_fast' => $c->description_fast,
                        'description' => $c->description,
                        'evolution' => $c->evolution,
                        'life_dice' => $c->life_dice,
                        'specificity' => $c->specificity,
                        'dofus_version' => $c->dofus_version,
                        'state' => (string) ($c->state ?? 'draft'),
                        'read_level' => (int) ($c->read_level ?? 0),
                        'write_level' => (int) ($c->write_level ?? 0),
                        'image' => $c->image,
                        'icon' => $c->icon,
                        'symbol_full' => $c->symbol_full,
                        'symbol_bw' => $c->symbol_bw,
                        'logo_male' => $c->logo_male,
                        'logo_female' => $c->logo_female,
                        'image_full_male' => $c->image_full_male,
                        'image_full_female' => $c->image_full_female,
                        'auto_update' => (bool) $c->auto_update,
                        'created_by' => $c->created_by,
                        'createdBy' => $createdBy ? [
                            'id' => $createdBy->id,
                            'name' => $createdBy->name,
                            'email' => $createdBy->email,
                        ] : null,
                        'spells_count' => (int) ($c->spells_count ?? 0),
                        'languages' => LanguageResource::collection($c->languages)->resolve($request),
                    ],
                ],
            ];
        })->values()->all();

        return response()->json([
            'meta' => [
                'entityType' => 'breeds',
                'query' => [
                    'search' => $search,
                    'sort' => $sort,
                    'order' => $order,
                    'limit' => $limit,
                    'page' => $page,
                ],
                'capabilities' => $capabilities,
                'filterOptions' => [],
                'pagination' => $pagination,
            ],
            'rows' => $tableRows,
        ]);
    }
}
