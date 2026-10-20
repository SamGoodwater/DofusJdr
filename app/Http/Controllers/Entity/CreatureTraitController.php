<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCreatureTraitRequest;
use App\Http\Requests\Entity\UpdateCreatureTraitRequest;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Models\Entity\CreatureTrait;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CreatureTraitController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', CreatureTrait::class);

        $query = CreatureTrait::query()
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
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'state', 'read_level', 'write_level', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $creatureTraits = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/creature-trait/Index', [
            'creatureTraits' => CreatureTraitResource::collection($creatureTraits),
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
    public function store(StoreCreatureTraitRequest $request): RedirectResponse|Response
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $creatureTrait = CreatureTrait::create($data);

        if ($request->header('X-Inertia')) {
            return $this->redirectAfterEntityStore(
                $request,
                $creatureTrait,
                'entities.creature-traits.edit',
                'entities.creature-traits.index',
                'Trait créé avec succès.',
            );
        }

        return response()->json($creatureTrait, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CreatureTrait $creatureTrait)
    {
        $this->authorize('view', $creatureTrait);

        $creatureTrait->load(['createdBy', 'creatures']);

        return Inertia::render('Pages/entity/creature-trait/Show', [
            'creatureTrait' => new CreatureTraitResource($creatureTrait),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CreatureTrait $creatureTrait)
    {
        $this->authorize('update', $creatureTrait);

        $creatureTrait->load(['createdBy', 'creatures']);

        return Inertia::render('Pages/entity/creature-trait/Edit', [
            'creatureTrait' => new CreatureTraitResource($creatureTrait),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreatureTraitRequest $request, CreatureTrait $creatureTrait)
    {
        $data = $request->validated();
        unset($data['redirect_after_update']);
        $creatureTrait->update($data);

        if ($request->header('X-Inertia')) {
            return EntityUpdateRedirect::respond(
                $request,
                'Trait mis à jour avec succès.',
                fn () => back(),
                editRedirect: fn () => redirect()->route('entities.creature-traits.edit', $creatureTrait),
                indexRedirect: fn () => redirect()->route('entities.creature-traits.index'),
                showRedirect: fn () => redirect()->route('entities.creature-traits.show', $creatureTrait),
            );
        }

        return response()->json($creatureTrait);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, CreatureTrait $creatureTrait, EntityDeletionService $deletionService): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($creatureTrait, $actor);

        return response()->json(['message' => 'Entité placée en corbeille.']);
    }

    /**
     * Télécharge un PDF pour un ou plusieurs creatureTraits.
     *
     * @param  CreatureTrait|null  $creatureTrait  L'creatureTrait unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?CreatureTrait $creatureTrait = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', CreatureTrait::class);
                $creatureTraits = CreatureTrait::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($creatureTraits, 'creatureTrait');
                $filename = 'creatureTraits-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $creatureTrait) {
            abort(404);
        }

        $this->authorize('view', $creatureTrait);

        $pdf = PdfService::generateForEntity($creatureTrait, 'creatureTrait');
        $filename = 'creatureTrait-'.$creatureTrait->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
