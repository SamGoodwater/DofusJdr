<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Panoply;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class PanoplyController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Panoply::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $panoplys = Panoply::paginate($paginationMaxDisplay);

        return Inertia::render('panoply.index', [
            'panoplys' => $panoplys,
        ]);
    }

    public function show(Panoply $panoply, Request $request): \Inertia\Response
    {
        $this->authorize('view', $panoply);

        return Inertia::render('Panoplys/Show', [
            'ressources' => $panoply->ressources,
            'panoply' => $panoply->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Panoply::class);

        return Inertia::render('panoply.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Panoply::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('panoplys', 'modules');
        $panoply = Panoply::create($data);

        return redirect()->route('panoply.show', ['panoply' => $panoply]);
    }

    public function edit(Panoply $panoply): \Inertia\Response
    {
        $this->authorize('update', $panoply);

        return Inertia::render('panoply.edit', [
            'panoply' => $panoply,
            'ressources' => $panoply->ressources,
            'panoply' => $panoply->panoply,
        ]);
    }

    public function update(Panoply $panoply, Request $request): RedirectResponse
    {
        $this->authorize('update', $panoply);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('panoplys', 'modules');
        $panoply->update($data);

        return redirect()->route('panoply.show', ['panoply' => $panoply]);
    }

    public function delete(Panoply $panoply): RedirectResponse
    {
        $this->authorize('delete', $panoply);

        $panoply->delete();

        return redirect()->route('panoply.index');
    }

    public function forceDelete(Panoply $panoply): RedirectResponse
    {
        $this->authorize('forceDelete', $panoply);

        $panoply->forceDelete();

        return redirect()->route('panoply.index');
    }

    public function restore(Panoply $panoply): RedirectResponse
    {
        $this->authorize('restore', $panoply);

        $panoply->restore();

        return redirect()->route('panoply.index');
    }
}
