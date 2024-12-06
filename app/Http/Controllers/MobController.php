<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mob;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class MobController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Mob::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $mobs = Mob::paginate($paginationMaxDisplay);

        return Inertia::render('mob.index', [
            'mobs' => $mobs,
        ]);
    }

    public function show(Mob $mob, Request $request): \Inertia\Response
    {
        $this->authorize('view', $mob);

        return Inertia::render('Mobs/Show', [
            'ressources' => $mob->ressources,
            'panoply' => $mob->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Mob::class);

        return Inertia::render('mob.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Mob::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('mobs', 'modules');
        $mob = Mob::create($data);

        return redirect()->route('mob.show', ['mob' => $mob]);
    }

    public function edit(Mob $mob): \Inertia\Response
    {
        $this->authorize('update', $mob);

        return Inertia::render('mob.edit', [
            'mob' => $mob,
            'ressources' => $mob->ressources,
            'panoply' => $mob->panoply,
        ]);
    }

    public function update(Mob $mob, Request $request): RedirectResponse
    {
        $this->authorize('update', $mob);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('mobs', 'modules');
        $mob->update($data);

        return redirect()->route('mob.show', ['mob' => $mob]);
    }

    public function delete(Mob $mob): RedirectResponse
    {
        $this->authorize('delete', $mob);

        $mob->delete();

        return redirect()->route('mob.index');
    }

    public function forceDelete(Mob $mob): RedirectResponse
    {
        $this->authorize('forceDelete', $mob);

        $mob->forceDelete();

        return redirect()->route('mob.index');
    }

    public function restore(Mob $mob): RedirectResponse
    {
        $this->authorize('restore', $mob);

        $mob->restore();

        return redirect()->route('mob.index');
    }
}
