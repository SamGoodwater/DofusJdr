<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreConditionRequest;
use App\Http\Requests\Entity\UpdateConditionRequest;
use App\Http\Resources\Entity\ConditionResource;
use App\Models\Entity\Condition;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ConditionController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Condition::class);

        $query = Condition::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'creatures']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtres
        if (request()->has('state') && request()->state !== '') {
            $query->where('state', (string) request()->state);
        } else {
            $query->where('state', '!=', Condition::STATE_RAW);
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'state', 'read_level', 'write_level', 'dissipable', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $conditions = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/condition/Index', [
            'conditions' => ConditionResource::collection($conditions),
            'filters' => request()->only(['search', 'state']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConditionRequest $request): RedirectResponse|Response
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $condition = Condition::create($data);

        if ($request->header('X-Inertia')) {
            return $this->redirectAfterEntityStore(
                $request,
                $condition,
                'entities.conditions.edit',
                'entities.conditions.index',
                'État créé avec succès.',
            );
        }

        return response()->json($condition, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Condition $condition)
    {
        $this->authorize('view', $condition);

        $condition->load(['createdBy', 'creatures']);

        return Inertia::render('Pages/entity/condition/Show', [
            'condition' => new ConditionResource($condition),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Condition $condition)
    {
        $this->authorize('update', $condition);

        $condition->load(['createdBy', 'creatures']);

        return Inertia::render('Pages/entity/condition/Edit', [
            'condition' => new ConditionResource($condition),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConditionRequest $request, Condition $condition)
    {
        $data = $request->validated();
        unset($data['redirect_after_update']);
        $condition->update($data);

        if ($request->header('X-Inertia')) {
            return EntityUpdateRedirect::respond(
                $request,
                'État mis à jour avec succès.',
                fn () => back(),
                editRedirect: fn () => redirect()->route('entities.conditions.edit', $condition),
                indexRedirect: fn () => redirect()->route('entities.conditions.index'),
                showRedirect: fn () => redirect()->route('entities.conditions.show', $condition),
            );
        }

        return response()->json($condition);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Condition $condition, EntityDeletionService $deletionService): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($condition, $actor);

        return response()->json(['message' => 'Entité placée en corbeille.']);
    }

    /**
     * Télécharge un PDF pour un ou plusieurs conditions.
     *
     * @param  Condition|null  $condition  L'condition unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Condition $condition = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Condition::class);
                $conditions = Condition::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($conditions, 'condition');
                $filename = 'conditions-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $condition) {
            abort(404);
        }

        $this->authorize('view', $condition);

        $pdf = PdfService::generateForEntity($condition, 'condition');
        $filename = 'condition-'.$condition->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
