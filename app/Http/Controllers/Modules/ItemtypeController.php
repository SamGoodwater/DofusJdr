<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use App\Models\Modules\Itemtype;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ItemtypeController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Itemtype::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $itemtypes = Itemtype::paginate($paginationMaxDisplay);

        return Inertia::render('itemtype.index', [
            'itemtypes' => $itemtypes,
        ]);
    }

    public function show(Itemtype $itemtype, Request $request): \Inertia\Response
    {
        $this->authorize('view', $itemtype);

        return Inertia::render('Itemtypes/Show', [
            'items' => $itemtype->items()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Itemtype::class);

        return Inertia::render('itemtype.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Itemtype::class);

        $data = DataService::extractData($request, new Itemtype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $itemtype = Itemtype::create($data);

        return redirect()->route('itemtype.show', ['itemtype' => $itemtype]);
    }

    public function edit(Itemtype $itemtype): \Inertia\Response
    {
        $this->authorize('update', $itemtype);

        return Inertia::render('itemtype.edit', [
            'itemtype' => $itemtype
        ]);
    }

    public function update(Itemtype $itemtype, Request $request): RedirectResponse
    {
        $this->authorize('update', $itemtype);

        $data = DataService::extractData($request, $itemtype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $itemtype->update($data);

        return redirect()->route('itemtype.show', ['itemtype' => $itemtype]);
    }

    public function delete(Itemtype $itemtype): RedirectResponse
    {
        $this->authorize('delete', $itemtype);

        $itemtype->delete();

        return redirect()->route('itemtype.index');
    }

    public function forceDelete(Itemtype $itemtype): RedirectResponse
    {
        $this->authorize('forceDelete', $itemtype);

        $itemtype->forceDelete();

        return redirect()->route('itemtype.index');
    }

    public function restore(Itemtype $itemtype): RedirectResponse
    {
        $this->authorize('restore', $itemtype);

        $itemtype->restore();

        return redirect()->route('itemtype.index');
    }
}
