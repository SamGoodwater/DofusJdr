<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCampaignRequest;
use App\Http\Requests\Entity\UpdateCampaignRequest;
use App\Http\Resources\Entity\CampaignResource;
use App\Models\Entity\Campaign;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CampaignController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Campaign::class);

        $query = Campaign::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'users', 'scenarios']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtres
        if (request()->has('state') && request()->state !== '') {
            $query->where('state', request()->state);
        }

        if (request()->has('is_public') && request()->is_public !== '') {
            $query->where('is_public', request()->is_public);
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'slug', 'state', 'is_public', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $campaigns = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/campaign/Index', [
            'campaigns' => CampaignResource::collection($campaigns),
            'filters' => request()->only(['search', 'state', 'is_public']),
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
    public function store(StoreCampaignRequest $request): RedirectResponse|Response
    {
        $this->authorize('create', Campaign::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $campaign = Campaign::create($data);

        if ($request->header('X-Inertia')) {
            return $this->redirectAfterEntityStore(
                $request,
                $campaign,
                'entities.campaigns.edit',
                'entities.campaigns.index',
                'Campagne créée avec succès.',
            );
        }

        return response()->json($campaign, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        return response()->json($campaign);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $campaign->load([
            'createdBy',
            'users',
            'scenarios',
            'items',
            'consumables',
            'resources',
            'spells',
            'panoplies',
        ]);

        // Charger toutes les entités disponibles pour la recherche
        $availableUsers = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        $availableScenarios = Scenario::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

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

        return Inertia::render('Pages/entity/campaign/Edit', [
            'campaign' => new CampaignResource($campaign),
            'availableUsers' => $availableUsers,
            'availableScenarios' => $availableScenarios,
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
    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $campaign);
        $campaign->update($request->validated());

        if ($request->header('X-Inertia')) {
            return back()->with('success', 'Campagne mise à jour avec succès.');
        }

        return response()->json($campaign);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Campaign $campaign, EntityDeletionService $deletionService): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($campaign, $actor);

        return response()->json(['message' => 'Entité placée en corbeille.']);
    }

    /**
     * Associe un utilisateur à la campagne.
     */
    public function attachUser(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $campaign->users()->attach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la campagne.
     */
    public function detachUser(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $campaign->users()->detach($request->user_id);

        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la campagne.
     */
    public function syncUsers(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $campaign->users()->sync($request->user_ids);

        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la campagne.
     */
    public function users(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        return response()->json($campaign->users);
    }

    /**
     * Update the users of a campaign.
     */
    public function updateUsers(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $campaign->users()->sync($request->users);

        return redirect()->back()
            ->with('success', 'Utilisateurs de la campagne mis à jour avec succès.');
    }

    /**
     * Update the scenarios of a campaign.
     */
    public function updateScenarios(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);

        $campaign->scenarios()->sync($request->scenarios);

        return redirect()->back()
            ->with('success', 'Scénarios de la campagne mis à jour avec succès.');
    }

    /**
     * Update the items of a campaign.
     */
    public function updateItems(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:items,id',
        ]);

        $campaign->items()->sync($request->items);

        return redirect()->back()
            ->with('success', 'Objets de la campagne mis à jour avec succès.');
    }

    /**
     * Update the consumables of a campaign.
     */
    public function updateConsumables(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'consumables' => 'required|array',
            'consumables.*' => 'exists:consumables,id',
        ]);

        $campaign->consumables()->sync($request->consumables);

        return redirect()->back()
            ->with('success', 'Consommables de la campagne mis à jour avec succès.');
    }

    /**
     * Update the resources of a campaign.
     */
    public function updateResources(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'resources' => 'required|array',
            'resources.*' => 'exists:resources,id',
        ]);

        $campaign->resources()->sync($request->resources);

        return redirect()->back()
            ->with('success', 'Ressources de la campagne mises à jour avec succès.');
    }

    /**
     * Update the spells of a campaign.
     */
    public function updateSpells(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'spells' => 'required|array',
            'spells.*' => 'exists:spells,id',
        ]);

        $campaign->spells()->sync($request->spells);

        return redirect()->back()
            ->with('success', 'Sorts de la campagne mis à jour avec succès.');
    }

    /**
     * Update the panoplies of a campaign.
     */
    public function updatePanoplies(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'panoplies' => 'required|array',
            'panoplies.*' => 'exists:panoplies,id',
        ]);

        $campaign->panoplies()->sync($request->panoplies);

        return redirect()->back()
            ->with('success', 'Panoplies de la campagne mises à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs campaigns.
     *
     * @param  Campaign|null  $campaign  Le campaign unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Campaign $campaign = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Campaign::class);
                $campaigns = Campaign::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($campaigns, 'campaign');
                $filename = 'campaigns-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $campaign) {
            abort(404);
        }

        $this->authorize('view', $campaign);

        $pdf = PdfService::generateForEntity($campaign, 'campaign');
        $filename = 'campaign-'.$campaign->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
