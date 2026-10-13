<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreNpcRequest;
use App\Http\Requests\Entity\UpdateNpcCreatureTraitsRequest;
use App\Http\Requests\Entity\UpdateNpcLanguagesRequest;
use App\Http\Requests\Entity\UpdateNpcRequest;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Http\Resources\Entity\LanguageResource;
use App\Http\Resources\Entity\NpcResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Campaign;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Language;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Scenario;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Creature\CreatureComposableCharacteristicsPersister;
use App\Services\Entity\EntityDeletionService;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Services\PdfService;
use App\Support\Creature\CreatureSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class NpcController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Npc::class);

        $viewer = request()->user();
        $query = Npc::query()
            ->visibleToUser($viewer)
            ->with([
                'creature',
                'breed' => fn ($q) => $q->visibleToUser($viewer),
                'specialization' => fn ($q) => $q->visibleToUser($viewer),
            ]);

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->whereHas('creature', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (request()->has('breed_id') && request()->breed_id !== '') {
            $query->where('breed_id', request()->breed_id);
        }

        if (request()->has('specialization_id') && request()->specialization_id !== '') {
            $query->where('specialization_id', request()->specialization_id);
        }

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $npcs = $query->paginate(20)->withQueryString();

        $breeds = Breed::query()
            ->visibleToUser(request()->user())
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(200)
            ->get();

        return Inertia::render('Pages/entity/npc/Index', [
            'npcs' => NpcResource::collection($npcs),
            'filters' => request()->only(['search', 'breed_id', 'specialization_id', 'npc_role', 'size']),
            'breeds' => $breeds,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Npc::class);

        return redirect()->route('entities.npcs.index');
    }

    public function store(StoreNpcRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Npc::class);

        $data = $request->validated();
        $creatureId = $data['creature_id'] ?? null;
        $state = $data['state'] ?? 'draft';

        if ($creatureId === null) {
            // Fiche vide (totaux null = composition). Pas Creature::factory() : Faker écrirait des stats aléatoires.
            $creature = Creature::query()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'level' => (string) ($data['level'] ?? '1'),
                'location' => $data['location'] ?? null,
                'hostility' => array_key_exists('hostility', $data) && $data['hostility'] !== null
                    ? (int) $data['hostility'] : 2,
                'state' => $state,
                'created_by' => $request->user()?->id,
            ]);
            $creatureId = $creature->id;
        }

        $npc = Npc::create([
            'creature_id' => $creatureId,
            'story' => $data['story'] ?? null,
            'historical' => $data['historical'] ?? null,
            'age' => $data['age'] ?? null,
            'size' => array_key_exists('size', $data) && $data['size'] !== null
                ? (int) $data['size'] : CreatureSize::MOYEN,
            'npc_role' => $data['npc_role'] ?? null,
            'breed_id' => $data['breed_id'] ?? null,
            'specialization_id' => $data['specialization_id'] ?? null,
            'state' => $state,
            'read_level' => array_key_exists('read_level', $data) && $data['read_level'] !== null
                ? (int) $data['read_level'] : User::ROLE_GUEST,
            'write_level' => array_key_exists('write_level', $data) && $data['write_level'] !== null
                ? (int) $data['write_level'] : User::ROLE_GAME_MASTER,
        ]);

        return $this->redirectAfterEntityStore(
            $request,
            $npc,
            'entities.npcs.edit',
            'entities.npcs.index',
            'PNJ créé avec succès.',
            'edit',
        );
    }

    public function show(Request $request, Npc $npc): InertiaResponse
    {
        $this->authorize('view', $npc);

        $npc->load($this->npcShowRelations($request));

        return Inertia::render('Pages/entity/npc/Show', [
            'npc' => new NpcResource($npc),
        ]);
    }

    public function edit(Request $request, Npc $npc): InertiaResponse
    {
        $this->authorize('update', $npc);

        $npc->load([
            'creature.creatureTraits',
            'breed',
            'specialization',
            'panoplies',
            'scenarios',
            'campaigns',
            'languages',
            'shop',
            'creature.spells',
            'creature.items.itemType',
        ]);

        $viewer = $request->user();

        $availablePanoplies = Panoply::query()
            ->visibleToUser($viewer)
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $availableScenarios = Scenario::query()
            ->visibleToUser($viewer)
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $availableCampaigns = Campaign::query()
            ->visibleToUser($viewer)
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $spellQuery = Spell::query()
            ->visibleToUser($viewer)
            ->select('id', 'name', 'description', 'level')
            ->orderBy('name');
        if ($npc->breed_id) {
            $spellQuery->whereHas('breeds', fn ($q) => $q->where('breeds.id', $npc->breed_id));
        }
        $availableSpells = $spellQuery->limit(200)->get();

        $availableItems = Item::query()
            ->visibleToUser($viewer)
            ->whereHas('itemType', fn ($q) => $q->where('show_in_catalog', true))
            ->select('id', 'name', 'description', 'level', 'item_type_id')
            ->orderBy('name')
            ->limit(100)
            ->get();

        $availableLanguages = LanguageResource::collection(
            Language::query()->orderBy('name')->limit(500)->get()
        )->toArray($request);

        $availableCreatureTraits = CreatureTraitResource::collection(
            CreatureTrait::query()
                ->visibleToUser($viewer)
                ->orderBy('name')
                ->limit(500)
                ->get()
        )->toArray($request);

        $availableBreeds = Breed::query()
            ->visibleToUser($viewer)
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $availableSpecializations = Specialization::query()
            ->visibleToUser($viewer)
            ->select('id', 'name')
            ->orderBy('name')
            ->limit(200)
            ->get();

        return Inertia::render('Pages/entity/npc/Edit', [
            'npc' => new NpcResource($npc),
            'availablePanoplies' => $availablePanoplies,
            'availableScenarios' => $availableScenarios,
            'availableCampaigns' => $availableCampaigns,
            'availableSpells' => $availableSpells,
            'availableItems' => $availableItems,
            'availableLanguages' => $availableLanguages,
            'availableCreatureTraits' => $availableCreatureTraits,
            'availableBreeds' => $availableBreeds,
            'availableSpecializations' => $availableSpecializations,
        ]);
    }

    public function update(
        UpdateNpcRequest $request,
        Npc $npc,
        CreatureComposableCharacteristicsPersister $composablePersister,
    ): RedirectResponse {
        $this->authorize('update', $npc);

        $data = $request->validated();
        $npcPayload = [];
        foreach ([
            'creature_id', 'story', 'historical', 'age', 'size', 'npc_role',
            'breed_id', 'specialization_id', 'state', 'read_level', 'write_level',
        ] as $key) {
            if (array_key_exists($key, $data)) {
                $npcPayload[$key] = $data[$key];
            }
        }
        if ($npcPayload !== []) {
            $npc->update($npcPayload);
        }

        $creature = $npc->creature;
        if ($creature) {
            $creaturePayload = [];
            foreach (['name', 'description', 'level', 'location', 'hostility'] as $key) {
                if (array_key_exists($key, $data)) {
                    $creaturePayload[$key] = $data[$key];
                }
            }
            if (array_key_exists('state', $data) && $data['state'] !== null) {
                $creaturePayload['state'] = $data['state'];
            }
            $composablePersister->apply($creature, $composablePersister->extractPayload($data));
            if ($creaturePayload !== []) {
                $creature->update($creaturePayload);
            }
        }

        return back()->with('success', 'PNJ mis à jour avec succès.');
    }

    public function updateLanguages(UpdateNpcLanguagesRequest $request, Npc $npc): RedirectResponse
    {
        $sync = [];
        foreach ($request->validatedLanguageIdsOrdered() as $index => $id) {
            $sync[$id] = ['sort_order' => $index];
        }
        $npc->languages()->sync($sync);

        return redirect()->back()
            ->with('success', 'Langues du PNJ mises à jour.');
    }

    public function updateCreatureTraits(UpdateNpcCreatureTraitsRequest $request, Npc $npc): RedirectResponse
    {
        $this->authorize('update', $npc);
        $creature = $npc->creature;
        if (! $creature) {
            return redirect()->back()
                ->withErrors(['creature_traits' => 'Ce PNJ n’a pas de créature associée.']);
        }

        $creature->creatureTraits()->sync($request->validatedCreatureTraitIds());

        return redirect()->back()
            ->with('success', 'Traits du PNJ mis à jour.');
    }

    public function updateSpells(Request $request, Npc $npc): RedirectResponse
    {
        $this->authorize('update', $npc);
        $creature = $npc->creature;
        if (! $creature) {
            return redirect()->back()
                ->withErrors(['spells' => 'Ce PNJ n’a pas de créature associée.']);
        }

        $request->validate([
            'spells' => 'array',
            'spells.*' => 'exists:spells,id',
        ]);

        $creature->spells()->sync($request->spells ?? []);

        return redirect()->back()
            ->with('success', 'Sorts du PNJ mis à jour.');
    }

    public function updateItems(Request $request, Npc $npc, NpcEquipmentSlotValidator $slotValidator): RedirectResponse
    {
        $this->authorize('update', $npc);
        $creature = $npc->creature;
        if (! $creature) {
            return redirect()->back()
                ->withErrors(['items' => 'Ce PNJ n’a pas de créature associée.']);
        }

        $request->validate([
            'items' => 'array',
            'items.*' => 'integer|exists:items,id',
        ]);

        $ids = array_values(array_unique(array_map('intval', $request->input('items', []))));
        $items = Item::query()
            ->with('itemType')
            ->whereIn('id', $ids)
            ->get();

        $slotValidator->assertWornKit($items);

        $sync = [];
        foreach ($ids as $id) {
            $sync[$id] = ['quantity' => 1];
        }
        $creature->items()->sync($sync);

        return redirect()->back()
            ->with('success', 'Équipement du PNJ mis à jour.');
    }

    public function delete(Request $request, Npc $npc, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($npc, $actor);

        return redirect()
            ->route('entities.npcs.index')
            ->with('success', 'PNJ placé en corbeille.');
    }

    public function updatePanoplies(Request $request, Npc $npc): RedirectResponse
    {
        $this->authorize('update', $npc);

        $request->validate([
            'panoplies' => 'required|array',
            'panoplies.*' => 'exists:panoplies,id',
        ]);

        $npc->panoplies()->sync($request->panoplies);

        return redirect()->back()
            ->with('success', 'Panoplies du PNJ mises à jour avec succès.');
    }

    public function updateScenarios(Request $request, Npc $npc): RedirectResponse
    {
        $this->authorize('update', $npc);

        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);

        $npc->scenarios()->sync($request->scenarios);

        return redirect()->back()
            ->with('success', 'Scénarios du PNJ mis à jour avec succès.');
    }

    public function updateCampaigns(Request $request, Npc $npc): RedirectResponse
    {
        $this->authorize('update', $npc);

        $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*' => 'exists:campaigns,id',
        ]);

        $npc->campaigns()->sync($request->campaigns);

        return redirect()->back()
            ->with('success', 'Campagnes du PNJ mises à jour avec succès.');
    }

    public function downloadPdf(?Npc $npc = null): Response
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Npc::class);
                $npcs = Npc::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($npcs, 'npc');
                $filename = 'npcs-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $npc) {
            abort(404);
        }

        $this->authorize('view', $npc);

        $pdf = PdfService::generateForEntity($npc, 'npc');
        $filename = 'npc-'.$npc->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Relations de la fiche lecture : classe / spé / sorts / stuff / traits filtrés `visibleToUser`.
     *
     * @return array<string, mixed>
     */
    private function npcShowRelations(Request $request): array
    {
        return [
            'creature' => fn ($q) => $q->with([
                'creatureTraits' => fn ($tq) => $tq
                    ->visibleToUser($request->user())
                    ->orderBy('name'),
                'spells' => fn ($sq) => $sq
                    ->visibleToUser($request->user())
                    ->orderBy('name')
                    ->with([
                        'spellTypes',
                        'effects.degrees.effectSubEffects.subEffect',
                    ]),
                'items' => fn ($iq) => $iq
                    ->visibleToUser($request->user())
                    ->orderBy('name')
                    ->with(['itemType:id,name']),
            ]),
            'breed' => fn ($q) => $q->visibleToUser($request->user()),
            'specialization' => fn ($q) => $q->visibleToUser($request->user()),
            'languages',
            'panoplies' => fn ($q) => $q
                ->visibleToUser($request->user())
                ->with([
                    'items' => fn ($iq) => $iq->visibleToUser($request->user()),
                ]),
            'shop' => fn ($q) => $q->visibleToUser($request->user()),
            'scenarios' => fn ($q) => $q->visibleToUser($request->user()),
            'campaigns' => fn ($q) => $q->visibleToUser($request->user()),
        ];
    }
}
