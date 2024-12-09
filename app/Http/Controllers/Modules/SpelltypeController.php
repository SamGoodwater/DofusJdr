<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Spelltype;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class SpelltypeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Spelltype::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $spelltypes = Spelltype::paginate($paginationMaxDisplay);

        return Inertia::render('spelltype.index', [
            'spelltypes' => $spelltypes,
        ]);
    }

    public function show(Spelltype $spelltype, Request $request): \Inertia\Response
    {
        $this->authorize('view', $spelltype);

        return Inertia::render('Spelltypes/Show', [
            'ressources' => $spelltype->ressources,
            'panoply' => $spelltype->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Spelltype::class);

        return Inertia::render('spelltype.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Spelltype::class);

        $data = DataService::extractData($request, new Spelltype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $spelltype = Spelltype::create($data);

        return redirect()->route('spelltype.show', ['spelltype' => $spelltype]);
    }

    public function edit(Spelltype $spelltype): \Inertia\Response
    {
        $this->authorize('update', $spelltype);

        return Inertia::render('spelltype.edit', [
            'spelltype' => $spelltype,
            'ressources' => $spelltype->ressources,
            'panoply' => $spelltype->panoply,
        ]);
    }

    public function update(Spelltype $spelltype, Request $request): RedirectResponse
    {
        $this->authorize('update', $spelltype);

        $data = DataService::extractData($request, $spelltype);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $spelltype->update($data);

        return redirect()->route('spelltype.show', ['spelltype' => $spelltype]);
    }

    public function delete(Spelltype $spelltype): RedirectResponse
    {
        $this->authorize('delete', $spelltype);

        $spelltype->delete();

        return redirect()->route('spelltype.index');
    }

    public function forceDelete(Spelltype $spelltype): RedirectResponse
    {
        $this->authorize('forceDelete', $spelltype);

        $spelltype->forceDelete();

        return redirect()->route('spelltype.index');
    }

    public function restore(Spelltype $spelltype): RedirectResponse
    {
        $this->authorize('restore', $spelltype);

        $spelltype->restore();

        return redirect()->route('spelltype.index');
    }
}
