<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StorePanoplyRequest;
use App\Http\Requests\Entity\UpdatePanoplyRequest;
use App\Http\Resources\Entity\PanoplyResource;
use App\Models\Entity\Panoply;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use App\Support\Entity\ObjectEffectEditOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PanoplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Panoply::class);

        $query = Panoply::query()
            ->visibleToUser(request()->user())
            ->with($this->viewerRelations());

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('bonus', 'like', "%{$search}%");
            });
        }

        // Filtres
        if (request()->has('state') && request()->state !== '') {
            $query->where('state', (string) request()->state);
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'dofusdb_id', 'state', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $panoplies = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/panoply/Index', [
            'panoplies' => PanoplyResource::collection($panoplies),
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
    public function store(StorePanoplyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Panoply $panoply)
    {
        $this->authorize('view', $panoply);

        $panoply->load($this->viewerRelations());

        return Inertia::render('Pages/entity/panoply/Show', [
            'panoply' => new PanoplyResource($panoply),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panoply $panoply)
    {
        $this->authorize('update', $panoply);

        $panoply->load(['createdBy', 'items']);

        return Inertia::render('Pages/entity/panoply/Edit', [
            'panoply' => new PanoplyResource($panoply),
            'bonusCharacteristics' => ObjectEffectEditOptions::toArray()['objectEffectCharacteristics'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePanoplyRequest $request, Panoply $panoply)
    {
        $this->authorize('update', $panoply);

        $panoply->update($request->validated());

        $panoply->load(['createdBy', 'items']);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'bonus' => $panoply->bonus,
            ]);
        }

        return EntityUpdateRedirect::respond(
            $request,
            'Panoplie mise à jour avec succès.',
            fn () => back(),
            editRedirect: fn () => redirect()->route('entities.panoplies.edit', $panoply),
            indexRedirect: fn () => redirect()->route('entities.panoplies.index'),
            showRedirect: fn () => redirect()->route('entities.panoplies.show', $panoply),
        );
    }

    /**
     * Update the items of a panoply.
     */
    public function updateItems(Request $request, Panoply $panoply)
    {
        $this->authorize('update', $panoply);

        $request->validate([
            'items' => 'present|array',
            'items.*' => 'exists:items,id',
        ]);

        $panoply->items()->sync($request->items);

        $panoply->load(['createdBy', 'items']);

        return redirect()->back()
            ->with('success', 'Items de la panoplie mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Place la panoplie en corbeille (soft delete).
     */
    public function delete(Request $request, Panoply $panoply, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($panoply, $actor);

        return redirect()->route('entities.panoplies.index')
            ->with('success', 'Panoplie placée en corbeille.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs panoplies.
     *
     * @param  Panoply|null  $panoply  La panoply unique (si une seule)
     * @return Response
     */
    public function downloadPdf(?Panoply $panoply = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Panoply::class);
                $panoplies = Panoply::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($panoplies, 'panoply');
                $filename = 'panoplies-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $panoply) {
            abort(404);
        }

        $this->authorize('view', $panoply);

        $pdf = PdfService::generateForEntity($panoply, 'panoply');
        $filename = 'panoply-'.$panoply->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Relations de lecture : pièces filtrées par `visibleToUser` du visiteur.
     *
     * L’édition charge `items` sans ce filtre pour ne pas masquer un brouillon
     * déjà lié (un sync suivant le détacherait).
     *
     * @return array<string, mixed>
     *
     * @example $panoply->load($this->viewerRelations());
     */
    private function viewerRelations(): array
    {
        $user = request()->user();

        return [
            'createdBy',
            'items' => fn ($q) => $q->visibleToUser($user),
        ];
    }
}
