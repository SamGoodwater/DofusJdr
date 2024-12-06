<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consumable;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ConsumableController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Consumable::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $consumables = Consumable::paginate($paginationMaxDisplay);

        return Inertia::render('consumable.index', [
            'consumables' => $consumables,
        ]);
    }

    public function show(Consumable $consumable, Request $request): \Inertia\Response
    {
        $this->authorize('view', $consumable);

        return Inertia::render('Consumables/Show', [
            'ressources' => $consumable->ressources,
            'panoply' => $consumable->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Consumable::class);

        return Inertia::render('consumable.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Consumable::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('consumables', 'modules');
        $consumable = Consumable::create($data);

        return redirect()->route('consumable.show', ['consumable' => $consumable]);
    }

    public function edit(Consumable $consumable): \Inertia\Response
    {
        $this->authorize('update', $consumable);

        return Inertia::render('consumable.edit', [
            'consumable' => $consumable,
            'ressources' => $consumable->ressources,
            'panoply' => $consumable->panoply,
        ]);
    }

    public function update(Consumable $consumable, Request $request): RedirectResponse
    {
        $this->authorize('update', $consumable);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('consumables', 'modules');
        $consumable->update($data);

        return redirect()->route('consumable.show', ['consumable' => $consumable]);
    }

    public function delete(Consumable $consumable): RedirectResponse
    {
        $this->authorize('delete', $consumable);

        $consumable->delete();

        return redirect()->route('consumable.index');
    }

    public function forceDelete(Consumable $consumable): RedirectResponse
    {
        $this->authorize('forceDelete', $consumable);

        $consumable->forceDelete();

        return redirect()->route('consumable.index');
    }

    public function restore(Consumable $consumable): RedirectResponse
    {
        $this->authorize('restore', $consumable);

        $consumable->restore();

        return redirect()->route('consumable.index');
    }
}
