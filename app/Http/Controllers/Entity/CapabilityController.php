<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCapabilityRequest;
use App\Http\Requests\Entity\UpdateCapabilityConditionsRequest;
use App\Http\Requests\Entity\UpdateCapabilityRequest;
use App\Http\Resources\Entity\CapabilityResource;
use App\Http\Resources\Entity\ConditionResource;
use App\Models\Entity\Capability;
use App\Models\Entity\Condition;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CapabilityController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Capability::class);

        $viewer = request()->user();
        $query = Capability::query()
            ->visibleToUser($viewer)
            ->with([
                'createdBy',
                'specializations' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
                'creatures' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
                'conditions' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
            ]);

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $capabilities = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/capability/Index', [
            'capabilities' => CapabilityResource::collection($capabilities),
            'filters' => request()->only(['search']),
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
    public function store(StoreCapabilityRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Capability::class);

        $data = $request->validated();

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        foreach (['element', 'powerful'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === null) {
                unset($data[$key]);
            }
        }

        $data['created_by'] = $request->user()?->id;

        $capability = Capability::create($data);

        return $this->redirectAfterEntityStore(
            $request,
            $capability,
            'entities.capabilities.edit',
            'entities.capabilities.index',
            'Capacité créée avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Capability $capability)
    {
        $this->authorize('view', $capability);

        $viewer = request()->user();
        $capability->load([
            'createdBy',
            'specializations' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
            'creatures' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
            'conditions' => fn ($q) => $q->visibleToUser($viewer)->orderBy('name'),
        ]);

        return Inertia::render('Pages/entity/capability/Show', [
            'capability' => new CapabilityResource($capability),
        ]);
    }

    /**
     * Données partagées page / modal d’édition.
     *
     * @return array{capability: Capability}
     */
    protected function buildCapabilityEditPayload(Capability $capability): array
    {
        $capability->load(['createdBy', 'specializations', 'creatures', 'conditions']);

        return [
            'capability' => $capability,
            'availableConditions' => ConditionResource::collection(
                Condition::query()->orderBy('name')->limit(5000)->get()
            )->toArray(request()),
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Capability $capability)
    {
        $this->authorize('update', $capability);

        $payload = $this->buildCapabilityEditPayload($capability);

        return Inertia::render('Pages/entity/capability/Edit', [
            'capability' => new CapabilityResource($payload['capability']),
            'availableConditions' => $payload['availableConditions'],
        ]);
    }

    /**
     * Charge utile JSON pour l’éditeur en modal (liste des capacités).
     */
    public function editPayload(Capability $capability): JsonResponse
    {
        $this->authorize('update', $capability);

        $payload = $this->buildCapabilityEditPayload($capability);

        return response()->json([
            'capability' => (new CapabilityResource($payload['capability']))->toArray(request()),
            'availableConditions' => $payload['availableConditions'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCapabilityRequest $request, Capability $capability): RedirectResponse
    {
        $this->authorize('update', $capability);

        $data = $request->validated();
        unset($data['redirect_after_update']);

        $capability->update($data);

        $capability->load(['createdBy', 'specializations', 'creatures', 'conditions']);

        return EntityUpdateRedirect::respond(
            $request,
            'Capacité mise à jour avec succès.',
            // Aligné sorts : sans cible explicite → back (modal EntitySurfaceHost).
            fn () => back(),
            editRedirect: fn () => redirect()->route('entities.capabilities.edit', $capability),
            indexRedirect: fn () => redirect()->route('entities.capabilities.index'),
            showRedirect: fn () => redirect()->route('entities.capabilities.show', $capability),
        );
    }

    public function updateConditions(UpdateCapabilityConditionsRequest $request, Capability $capability): RedirectResponse
    {
        $capability->conditions()->sync($request->validatedConditionIds());

        return redirect()->back()
            ->with('success', 'Conditions de la capacité mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Capability $capability, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($capability, $actor);

        return redirect()->route('entities.capabilities.index')
            ->with('success', 'Capacité placée en corbeille.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs capabilities.
     *
     * @param  Capability|null  $capability  La capability unique (si une seule)
     */
    public function downloadPdf(?Capability $capability = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Capability::class);
                $capabilities = Capability::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($capabilities, 'capability');
                $filename = 'capabilities-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $capability) {
            abort(404);
        }

        $this->authorize('view', $capability);

        $pdf = PdfService::generateForEntity($capability, 'capability');
        $filename = 'capability-'.$capability->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
