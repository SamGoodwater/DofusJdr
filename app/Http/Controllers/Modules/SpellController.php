<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Spell;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

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

        $data = DataService::extractData($request, new Spell, [
            [
                'disk' => 'modules',
                'path_name' => 'spells',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
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

        $data = DataService::extractData($request, $spell, [
            [
                'disk' => 'modules',
                'path_name' => 'spells',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
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

        DataService::deleteFile($spell, 'image');
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
