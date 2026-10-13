<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreMonsterRequest;
use App\Http\Requests\Entity\UpdateMonsterCreatureTraitsRequest;
use App\Http\Requests\Entity\UpdateMonsterLanguagesRequest;
use App\Http\Requests\Entity\UpdateMonsterRequest;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Http\Resources\Entity\LanguageResource;
use App\Http\Resources\Entity\MonsterResource;
use App\Models\Entity\Campaign;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Language;
use App\Models\Entity\Monster;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Creature\CreatureComposableCharacteristicsPersister;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Profile\ProfileMasteryColumns;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MonsterController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Monster::class);

        // Liste chargée par EntityTanStackTable (api.tables.monsters) — pas de collection Inertia.
        return Inertia::render('Pages/entity/monster/Index', [
            'filters' => request()->only(['search', 'size', 'is_boss', 'monster_race_id']),
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
    public function store(StoreMonsterRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Monster::class);

        $data = $request->validated();
        $creatureId = $data['creature_id'] ?? null;

        if ($creatureId === null) {
            // Fiche vide (totaux null = composition). Pas Creature::factory() : Faker écrirait des stats aléatoires.
            $creature = Creature::query()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'level' => (string) ($data['level'] ?? '1'),
                'state' => $data['state'] ?? 'draft',
                'created_by' => $request->user()?->id,
            ]);
            $creatureId = $creature->id;
        }

        $monster = Monster::create([
            'creature_id' => $creatureId,
            'official_id' => $data['official_id'] ?? null,
            'dofusdb_id' => $data['dofusdb_id'] ?? null,
            'dofus_version' => $data['dofus_version'] ?? '3',
            'auto_update' => array_key_exists('auto_update', $data) ? (bool) $data['auto_update'] : false,
            'size' => array_key_exists('size', $data) && $data['size'] !== null ? (int) $data['size'] : 2,
            'is_boss' => array_key_exists('is_boss', $data) ? (int) (bool) $data['is_boss'] : 0,
            'boss_pa' => $data['boss_pa'] ?? '',
            'monster_race_id' => $data['monster_race_id'] ?? null,
            'state' => $data['state'] ?? 'draft',
            'read_level' => array_key_exists('read_level', $data) && $data['read_level'] !== null
                ? (int) $data['read_level'] : User::ROLE_GUEST,
            'write_level' => array_key_exists('write_level', $data) && $data['write_level'] !== null
                ? (int) $data['write_level'] : User::ROLE_GAME_MASTER,
        ]);

        return $this->redirectAfterEntityStore(
            $request,
            $monster,
            'entities.monsters.edit',
            'entities.monsters.index',
            'Monstre créé avec succès.',
            'edit',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Monster $monster): Response
    {
        $this->authorize('view', $monster);

        $monster->load([
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
            'monsterRace',
            'scenarios',
            'campaigns',
            'spellInvocations' => fn ($q) => $q->visibleToUser($request->user()),
            'languages',
        ]);

        return Inertia::render('Pages/entity/monster/Show', [
            'monster' => new MonsterResource($monster),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);

        $monster->load(['creature.creatureTraits', 'monsterRace', 'scenarios', 'campaigns', 'spellInvocations', 'languages']);

        // Catalogues bornés (évite Spell::all / dumps 5k) ; la recherche locale reste utilisable sur ce sous-ensemble.
        $availableScenarios = Scenario::query()
            ->visibleToUser($request->user())
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $availableCampaigns = Campaign::query()
            ->visibleToUser($request->user())
            ->select('id', 'name', 'description')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $availableSpells = Spell::query()
            ->visibleToUser($request->user())
            ->select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->limit(100)
            ->get();

        $availableLanguages = LanguageResource::collection(
            Language::query()->orderBy('name')->limit(500)->get()
        )->toArray(request());

        $availableCreatureTraits = CreatureTraitResource::collection(
            CreatureTrait::query()
                ->visibleToUser($request->user())
                ->orderBy('name')
                ->limit(500)
                ->get()
        )->toArray(request());

        return Inertia::render('Pages/entity/monster/Edit', [
            'monster' => new MonsterResource($monster),
            'availableScenarios' => $availableScenarios,
            'availableCampaigns' => $availableCampaigns,
            'availableSpells' => $availableSpells,
            'availableLanguages' => $availableLanguages,
            'availableCreatureTraits' => $availableCreatureTraits,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateLanguages(UpdateMonsterLanguagesRequest $request, Monster $monster): RedirectResponse
    {
        $sync = [];
        foreach ($request->validatedLanguageIdsOrdered() as $index => $id) {
            $sync[$id] = ['sort_order' => $index];
        }
        $monster->languages()->sync($sync);

        return redirect()->back()
            ->with('success', 'Langues du monstre mises à jour.');
    }

    public function update(
        UpdateMonsterRequest $request,
        Monster $monster,
        CreatureComposableCharacteristicsPersister $composablePersister,
    ) {
        $this->authorize('update', $monster);

        $validated = $request->validated();
        $monsterPayload = array_intersect_key($validated, array_flip([
            'size', 'is_boss', 'boss_pa', 'monster_race_id', 'dofus_version', 'auto_update',
            'state', 'read_level', 'write_level',
            ...ProfileMasteryColumns::all(),
        ]));
        if ($monsterPayload !== []) {
            $monster->update($monsterPayload);
        }

        $creature = $monster->creature;
        if ($creature) {
            $creaturePayload = [];
            if (array_key_exists('level', $validated)) {
                $creaturePayload['level'] = $validated['level'];
            }
            $composablePersister->apply($creature, $composablePersister->extractPayload($validated));
            if ($creaturePayload !== []) {
                $creature->update($creaturePayload);
            }
        }

        $monster->load(['creature', 'monsterRace']);

        return back()->with('success', 'Monstre mis à jour avec succès.');
    }

    public function updateCreatureTraits(UpdateMonsterCreatureTraitsRequest $request, Monster $monster): RedirectResponse
    {
        $this->authorize('update', $monster);
        $creature = $monster->creature;
        if (! $creature) {
            return redirect()->back()
                ->withErrors(['creature_traits' => 'Ce monstre n’a pas de créature associée.']);
        }

        $creature->creatureTraits()->sync($request->validatedCreatureTraitIds());

        return redirect()->back()
            ->with('success', 'Traits du monstre mis à jour.');
    }

    /**
     * Place le monstre en corbeille (soft delete).
     */
    public function delete(Request $request, Monster $monster, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($monster, $actor);

        return redirect()
            ->route('entities.monsters.index')
            ->with('success', 'Monstre placé en corbeille.');
    }

    /**
     * Update the scenarios of a monster.
     */
    public function updateScenarios(Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);

        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);

        $monster->scenarios()->sync($request->scenarios);

        return redirect()->back()
            ->with('success', 'Scénarios du monstre mis à jour avec succès.');
    }

    /**
     * Update the campaigns of a monster.
     */
    public function updateCampaigns(Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);

        $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*' => 'exists:campaigns,id',
        ]);

        $monster->campaigns()->sync($request->campaigns);

        return redirect()->back()
            ->with('success', 'Campagnes du monstre mises à jour avec succès.');
    }

    /**
     * Update the spell invocations of a monster.
     */
    public function updateSpellInvocations(Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);

        $request->validate([
            'spellInvocations' => 'required|array',
            'spellInvocations.*' => 'exists:spells,id',
        ]);

        $monster->spellInvocations()->sync($request->spellInvocations);

        return redirect()->back()
            ->with('success', 'Sorts d\'invocation du monstre mis à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs monsters.
     *
     * @param  Monster|null  $monster  Le monster unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Monster $monster = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Monster::class);
                $monsters = Monster::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($monsters, 'monster');
                $filename = 'monsters-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $monster) {
            abort(404);
        }

        $this->authorize('view', $monster);

        $pdf = PdfService::generateForEntity($monster, 'monster');
        $filename = 'monster-'.$monster->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
