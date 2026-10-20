<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreScenarioRequest;
use App\Http\Requests\Entity\UpdateScenarioRequest;
use App\Http\Resources\Entity\ScenarioResource;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ScenarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Scenario::class);

        $query = Scenario::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'users', 'campaigns']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $scenarios = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/scenario/Index', [
            'scenarios' => ScenarioResource::collection($scenarios),
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
    public function store(StoreScenarioRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scenario $scenario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $scenario->load([
            'createdBy',
            'items',
            'consumables',
            'resources',
            'spells',
            'panoplies',
        ]);

        // Charger toutes les entités disponibles pour la recherche
        $availableItems = Item::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableConsumables = Consumable::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableSpells = Spell::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availablePanoplies = Panoply::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return Inertia::render('Pages/entity/scenario/Edit', [
            'scenario' => new ScenarioResource($scenario),
            'availableItems' => $availableItems,
            'availableConsumables' => $availableConsumables,
            'availableResources' => $availableResources,
            'availableSpells' => $availableSpells,
            'availablePanoplies' => $availablePanoplies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScenarioRequest $request, Scenario $scenario): RedirectResponse
    {
        $this->authorize('update', $scenario);
        $scenario->update($request->validated());

        return back()->with('success', 'Scénario mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Place le scénario en corbeille (soft delete).
     */
    public function delete(Request $request, Scenario $scenario, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($scenario, $actor);

        return redirect()->route('entities.scenarios.index')
            ->with('success', 'Scénario placé en corbeille.');
    }

    /**
     * Associe un utilisateur au scénario.
     */
    public function attachUser(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $scenario->users()->attach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur du scénario.
     */
    public function detachUser(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $scenario->users()->detach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés au scénario.
     */
    public function syncUsers(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $scenario->users()->sync($request->user_ids);

        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés au scénario.
     */
    public function users(Scenario $scenario)
    {
        $this->authorize('view', $scenario);

        return response()->json($scenario->users);
    }

    /**
     * Update the items of a scenario.
     */
    public function updateItems(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:items,id',
        ]);

        $scenario->items()->sync($request->items);

        return redirect()->back()
            ->with('success', 'Objets du scénario mis à jour avec succès.');
    }

    /**
     * Update the consumables of a scenario.
     */
    public function updateConsumables(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $request->validate([
            'consumables' => 'required|array',
            'consumables.*' => 'exists:consumables,id',
        ]);

        $scenario->consumables()->sync($request->consumables);

        return redirect()->back()
            ->with('success', 'Consommables du scénario mis à jour avec succès.');
    }

    /**
     * Update the resources of a scenario.
     */
    public function updateResources(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $request->validate([
            'resources' => 'required|array',
            'resources.*' => 'exists:resources,id',
        ]);

        $scenario->resources()->sync($request->resources);

        return redirect()->back()
            ->with('success', 'Ressources du scénario mises à jour avec succès.');
    }

    /**
     * Update the spells of a scenario.
     */
    public function updateSpells(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $request->validate([
            'spells' => 'required|array',
            'spells.*' => 'exists:spells,id',
        ]);

        $scenario->spells()->sync($request->spells);

        return redirect()->back()
            ->with('success', 'Sorts du scénario mis à jour avec succès.');
    }

    /**
     * Update the panoplies of a scenario.
     */
    public function updatePanoplies(Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);

        $request->validate([
            'panoplies' => 'required|array',
            'panoplies.*' => 'exists:panoplies,id',
        ]);

        $scenario->panoplies()->sync($request->panoplies);

        return redirect()->back()
            ->with('success', 'Panoplies du scénario mises à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs scenarios.
     *
     * @param  Scenario|null  $scenario  Le scenario unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Scenario $scenario = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Scenario::class);
                $scenarios = Scenario::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($scenarios, 'scenario');
                $filename = 'scenarios-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $scenario) {
            abort(404);
        }

        $this->authorize('view', $scenario);

        $pdf = PdfService::generateForEntity($scenario, 'scenario');
        $filename = 'scenario-'.$scenario->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
