<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spell;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class SpellController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Spell::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $spells = Spell::paginate($paginationMaxDisplay);

        return Inertia::render('spell.index', [
            'spells' => $spells,
        ]);
    }

    public function show(Spell $spell, Request $request): \Inertia\Response
    {
        $this->authorize('view', $spell);

        return Inertia::render('Spells/Show', [
            'ressources' => $spell->ressources,
            'panoply' => $spell->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Spell::class);

        return Inertia::render('spell.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Spell::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('spells', 'modules');
        $spell = Spell::create($data);

        return redirect()->route('spell.show', ['spell' => $spell]);
    }

    public function edit(Spell $spell): \Inertia\Response
    {
        $this->authorize('update', $spell);

        return Inertia::render('spell.edit', [
            'spell' => $spell,
            'ressources' => $spell->ressources,
            'panoply' => $spell->panoply,
        ]);
    }

    public function update(Spell $spell, Request $request): RedirectResponse
    {
        $this->authorize('update', $spell);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('spells', 'modules');
        $spell->update($data);

        return redirect()->route('spell.show', ['spell' => $spell]);
    }

    public function delete(Spell $spell): RedirectResponse
    {
        $this->authorize('delete', $spell);

        $spell->delete();

        return redirect()->route('spell.index');
    }

    public function forceDelete(Spell $spell): RedirectResponse
    {
        $this->authorize('forceDelete', $spell);

        $spell->forceDelete();

        return redirect()->route('spell.index');
    }

    public function restore(Spell $spell): RedirectResponse
    {
        $this->authorize('restore', $spell);

        $spell->restore();

        return redirect()->route('spell.index');
    }
}
