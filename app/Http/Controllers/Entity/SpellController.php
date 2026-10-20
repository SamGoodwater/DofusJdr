<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\UpdateSpellEffectGroupRequest;
use App\Http\Requests\Entity\StoreSpellRequest;
use App\Http\Requests\Entity\UpdateSpellRequest;
use App\Http\Resources\Entity\SpellResource;
use App\Models\Effect;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Models\User;
use App\Services\Effect\EffectGroupEditorDataService;
use App\Services\Effect\EffectGroupUpdateService;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Services\Spell\SpellDegreeResolver;
use App\Services\Spell\SpellDegreesSerializer;
use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class SpellController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Spell::class);

        // Liste chargée par EntityTanStackTable (api.tables.spells) — pas de collection Inertia.
        $spellTypes = SpellType::query()
            ->select(['id', 'name', 'color', 'icon', 'show_in_catalog'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Pages/entity/spell/Index', [
            'filters' => request()->only(['search', 'level', 'pa', 'spell_type_id']),
            'spellTypes' => $spellTypes,
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
    public function store(StoreSpellRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Spell::class);

        $data = $request->validated();
        $spellTypes = $data['spellTypes'] ?? null;
        unset($data['spellTypes']);

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        foreach (['element', 'category', 'powerful'] as $intKey) {
            if (array_key_exists($intKey, $data) && $data[$intKey] === null) {
                unset($data[$intKey]);
            }
        }

        $data['created_by'] = $request->user()?->id;

        $spell = Spell::create($data);

        if (is_array($spellTypes)) {
            $spell->spellTypes()->sync($spellTypes);
        }

        return $this->redirectAfterEntityStore(
            $request,
            $spell,
            'entities.spells.edit',
            'entities.spells.index',
            'Sort créé avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Spell $spell)
    {
        $this->authorize('view', $spell);

        $spell->load([
            'createdBy',
            'spellTypes',
            'effects.degrees.effectSubEffects.subEffect',
            'degrees.effects.subEffect',
        ]);

        return Inertia::render('Pages/entity/spell/Show', [
            'spell' => new SpellResource($spell),
        ]);
    }

    /**
     * Données partagées page / modal d’édition (évite la duplication).
     *
     * @return array{
     *     spell: Spell,
     *     availableSpellTypes: Collection,
     *     availableEffects: array<int, array<string, mixed>>,
     *     effectEntityType: string,
     *     effectFormOptions: array<string, mixed>,
     *     spellDegrees: array<string, mixed>,
     *     spellEffectGroups: array<int, mixed>,
     *     spellHolders: array<string, list<array<string, mixed>>>
     * }
     */
    protected function buildSpellEditPayload(Spell $spell): array
    {
        $spell->load([
            'createdBy',
            'creatures.npc',
            'creatures.monster',
            'breeds',
            'spellTypes',
            'effects.degrees',
            'degrees.effects.subEffect',
        ]);

        $availableSpellTypes = SpellType::query()
            ->select(['id', 'name', 'description', 'color', 'icon'])
            ->orderBy('name')
            ->get();

        // Liste complète non embarquée : recherche via GET /api/effects/definitions (legacy).
        $availableEffects = [];

        $editorData = app(EffectGroupEditorDataService::class);

        $partialHeader = (string) request()->header('X-Inertia-Partial-Data', '');
        $partialProps = $partialHeader === ''
            ? null
            : array_filter(array_map('trim', explode(',', $partialHeader)));
        $needsFormOptions = $partialProps === null || in_array('effectFormOptions', $partialProps, true);
        $needsGroups = $partialProps === null || in_array('spellEffectGroups', $partialProps, true);
        $needsDegrees = $partialProps === null || in_array('spellDegrees', $partialProps, true);

        return [
            'spell' => $spell,
            'availableSpellTypes' => $availableSpellTypes,
            'availableEffects' => $availableEffects,
            'effectEntityType' => 'spell',
            'effectFormOptions' => $needsFormOptions ? $editorData->formOptions() : [],
            'spellDegrees' => $needsDegrees
                ? app(SpellDegreesSerializer::class)->serialize($spell)
                : ['degrees' => [], 'default_degree_id' => null],
            'spellEffectGroups' => $needsGroups ? $editorData->distinctGroupsForSpell($spell) : [],
            'spellHolders' => $this->serializeSpellHolders($spell),
        ];
    }

    /**
     * Entités qui utilisent le sort, prêtes à afficher dans l’éditeur.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    private function serializeSpellHolders(Spell $spell): array
    {
        $resolver = app(SpellDegreeResolver::class);
        $monsters = [];
        $npcs = [];
        $creatures = [];

        foreach ($spell->creatures as $creature) {
            $level = $this->parseHolderLevel($creature->level);
            $base = [
                'name' => $creature->name,
                'level' => $level,
                'image' => $creature->image,
                'stats' => [
                    'vitality' => $creature->vitality,
                    'pa' => $creature->pa,
                    'pm' => $creature->pm,
                ],
                'accessible_degree' => $this->accessibleDegreeBrief($resolver, $spell, $level),
            ];

            if ($creature->monster !== null) {
                $monster = $creature->monster;
                $monsters[] = array_merge($base, [
                    'id' => $monster->id,
                    'href' => route('entities.monsters.show', $monster),
                    'monster' => [
                        'id' => $monster->id,
                        'creature_id' => $monster->creature_id,
                        'is_boss' => (bool) $monster->is_boss,
                        'state' => $monster->state,
                        'creature' => [
                            'id' => $creature->id,
                            'name' => $creature->name,
                            'level' => $creature->level,
                            'image' => $creature->image,
                            'vitality' => $creature->vitality,
                            'pa' => $creature->pa,
                            'pm' => $creature->pm,
                            'description' => $creature->description,
                        ],
                    ],
                ]);
            } elseif ($creature->npc !== null) {
                $npcs[] = array_merge($base, [
                    'id' => $creature->npc->id,
                    'href' => route('entities.npcs.show', $creature->npc),
                ]);
            } else {
                $creatures[] = array_merge($base, [
                    'id' => $creature->id,
                    'href' => route('entities.creatures.show', $creature),
                ]);
            }
        }

        $breeds = $spell->breeds->map(function ($breed) use ($resolver, $spell): array {
            $level = isset($breed->pivot?->character_level)
                ? (int) $breed->pivot->character_level
                : null;

            return [
                'id' => $breed->id,
                'name' => $breed->name,
                'href' => route('entities.breeds.show', $breed),
                'level' => $level,
                'image' => $breed->image ?? null,
                'stats' => null,
                'accessible_degree' => $this->accessibleDegreeBrief($resolver, $spell, $level),
            ];
        })->values()->all();

        return compact('monsters', 'npcs', 'creatures', 'breeds');
    }

    private function parseHolderLevel(mixed $raw): ?int
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        if (is_numeric($raw)) {
            return (int) $raw;
        }
        if (is_string($raw) && preg_match('/(\d+)/', $raw, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * @return array{id: int, position: int, required_level: int|null, label: string}|null
     */
    private function accessibleDegreeBrief(SpellDegreeResolver $resolver, Spell $spell, ?int $level): ?array
    {
        $degree = $resolver->selectDegree($spell, null, $level);
        if ($degree === null) {
            return null;
        }
        $req = $degree->required_level;
        $label = $req !== null && $req > 0
            ? 'Niveau '.$req
            : 'Degré '.$degree->position;

        return [
            'id' => $degree->id,
            'position' => (int) $degree->position,
            'required_level' => $req,
            'label' => $label,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spell $spell)
    {
        $this->authorize('update', $spell);

        $payload = $this->buildSpellEditPayload($spell);

        return Inertia::render('Pages/entity/spell/Edit', [
            'spell' => new SpellResource($payload['spell']),
            'availableSpellTypes' => $payload['availableSpellTypes'],
            'availableEffects' => $payload['availableEffects'],
            'effectEntityType' => $payload['effectEntityType'],
            'effectFormOptions' => $payload['effectFormOptions'],
            'spellDegrees' => $payload['spellDegrees'],
            'spellEffectGroups' => $payload['spellEffectGroups'],
            'spellHolders' => $payload['spellHolders'],
        ]);
    }

    /**
     * Charge utile JSON pour l’éditeur complet en modal (liste des sorts).
     */
    public function editPayload(Spell $spell): JsonResponse
    {
        $this->authorize('update', $spell);

        $payload = $this->buildSpellEditPayload($spell);

        return response()->json([
            'spell' => (new SpellResource($payload['spell']))->toArray(request()),
            'availableSpellTypes' => $payload['availableSpellTypes']->toArray(),
            'availableEffects' => $payload['availableEffects'],
            'effectEntityType' => $payload['effectEntityType'],
            'effectFormOptions' => $payload['effectFormOptions'],
            'spellDegrees' => $payload['spellDegrees'],
            'spellEffectGroups' => $payload['spellEffectGroups'],
            'spellHolders' => $payload['spellHolders'],
        ]);
    }

    /**
     * Enregistre un groupe d’effets depuis la fiche sort (même charge utile que l’admin).
     */
    public function updateEffectGroup(UpdateSpellEffectGroupRequest $request, Spell $spell, Effect $effect): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $spell);

        app(EffectGroupUpdateService::class)->updateGroup($effect, $request->validated());

        $message = 'Effets du groupe enregistrés.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back(fallback: route('entities.spells.edit', $spell))
            ->with('success', $message);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $data = $request->validated();
        unset($data['redirect_after_update']);

        $spellTypes = $data['spellTypes'] ?? null;
        unset($data['spellTypes']);

        $spell->update($data);

        if (is_array($spellTypes)) {
            $spell->spellTypes()->sync($spellTypes);
        }

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

        return EntityUpdateRedirect::respond(
            $request,
            'Sort mis à jour avec succès.',
            fn () => back(),
            editRedirect: fn () => redirect()->route('entities.spells.edit', $spell),
            indexRedirect: fn () => redirect()->route('entities.spells.index'),
            showRedirect: fn () => redirect()->route('entities.spells.show', $spell),
        );
    }

    /**
     * Synchronise les classes (breeds) liées au sort.
     * L’UI principale pour lier un sort à une classe est la fiche classe ; cette route reste pour tests / usages programmatiques.
     */
    public function updateBreeds(Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $request->validate([
            'breeds' => 'present|array',
            'breeds.*' => 'exists:breeds,id',
        ]);

        /** @var list<int|string> $breedIds */
        $breedIds = $request->input('breeds', []);
        $existingPivots = $spell->breeds()
            ->whereIn('breeds.id', $breedIds)
            ->get()
            ->keyBy('id');

        $syncPayload = [];
        foreach ($breedIds as $breedId) {
            $id = (int) $breedId;
            $existing = $existingPivots->get($id);
            if ($existing !== null) {
                $syncPayload[$id] = [
                    'character_level' => (int) ($existing->pivot->character_level ?? 1),
                    'slot_index' => (int) ($existing->pivot->slot_index ?? 0),
                    'choice_order' => (int) ($existing->pivot->choice_order ?? 0),
                ];
            } else {
                $syncPayload[$id] = [];
            }
        }

        $spell->breeds()->sync($syncPayload);

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

        return redirect()->back()
            ->with('success', 'Classes du sort mises à jour avec succès.');
    }

    /**
     * Update the spell types of a spell.
     */
    public function updateSpellTypes(Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $request->validate([
            'spellTypes' => 'present|array',
            'spellTypes.*' => 'exists:spell_types,id',
        ]);

        $spell->spellTypes()->sync($request->spellTypes);

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

        return redirect()->back()
            ->with('success', 'Types de sort mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Spell $spell, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($spell, $actor);

        return redirect()->route('entities.spells.index')
            ->with('success', 'Sort placé en corbeille.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs spells.
     *
     * @param  Spell|null  $spell  Le spell unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Spell $spell = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Spell::class);
                $spells = Spell::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($spells, 'spell');
                $filename = 'spells-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $spell) {
            abort(404);
        }

        $this->authorize('view', $spell);

        $pdf = PdfService::generateForEntity($spell, 'spell');
        $filename = 'spell-'.$spell->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
