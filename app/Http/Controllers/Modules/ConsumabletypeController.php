<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use App\Models\Modules\Consumabletype;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ConsumabletypeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Consumabletype::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $consumabletypes = Consumabletype::paginate($paginationMaxDisplay);

        return Inertia::render('consumabletype.index', [
            'consumabletypes' => $consumabletypes,
        ]);
    }

    public function show(Consumabletype $consumabletype, Request $request): \Inertia\Response
    {
        $this->authorize('view', $consumabletype);

        return Inertia::render('Consumabletypes/Show', [
            'consumables' => $consumabletype->consumables()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Consumabletype::class);

        return Inertia::render('consumabletype.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Consumabletype::class);

        $data = DataService::extractData($request, new Consumabletype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $consumabletype = Consumabletype::create($data);

        return redirect()->route('consumabletype.show', ['consumabletype' => $consumabletype]);
    }

    public function edit(Consumabletype $consumabletype): \Inertia\Response
    {
        $this->authorize('update', $consumabletype);

        return Inertia::render('consumabletype.edit', [
            'consumabletype' => $consumabletype
        ]);
    }

    public function update(Consumabletype $consumabletype, Request $request): RedirectResponse
    {
        $this->authorize('update', $consumabletype);

        $data = DataService::extractData($request, $consumabletype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $consumabletype->update($data);

        return redirect()->route('consumabletype.show', ['consumabletype' => $consumabletype]);
    }

    public function delete(Consumabletype $consumabletype): RedirectResponse
    {
        $this->authorize('delete', $consumabletype);

        $consumabletype->delete();

        return redirect()->route('consumabletype.index');
    }

    public function forceDelete(Consumabletype $consumabletype): RedirectResponse
    {
        $this->authorize('forceDelete', $consumabletype);

        $consumabletype->forceDelete();

        return redirect()->route('consumabletype.index');
    }

    public function restore(Consumabletype $consumabletype): RedirectResponse
    {
        $this->authorize('restore', $consumabletype);

        $consumabletype->restore();

        return redirect()->route('consumabletype.index');
    }
}
