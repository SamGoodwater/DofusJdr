<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ItemtypeFilterRequest;
use App\Models\Modules\Itemtype;
use App\Events\NotificationSuperAdminEvent;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ItemtypeController extends Controller
{
    use AuthorizesRequests;

    public function index(ItemtypeFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Itemtype::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $itemtypes = Itemtype::paginate($paginationMaxDisplay);

        return Inertia::render('itemtype.index', [
            'itemtypes' => $itemtypes,
        ]);
    }

    public function show(Itemtype $itemtype, ItemtypeFilterRequest $request): \Inertia\Response
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

    public function store(ItemtypeFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Itemtype::class);

        $data = DataService::extractData($request, new Itemtype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $itemtype = Itemtype::create($data);

        event(new NotificationSuperAdminEvent('itemtype', 'create',  $itemtype));

        return redirect()->route('itemtype.show', ['itemtype' => $itemtype]);
    }

    public function edit(Itemtype $itemtype): \Inertia\Response
    {
        $this->authorize('update', $itemtype);

        return Inertia::render('itemtype.edit', [
            'itemtype' => $itemtype
        ]);
    }

    public function update(Itemtype $itemtype, ItemtypeFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $itemtype);

        $old_itemtype = clone $itemtype;

        $data = DataService::extractData($request, $itemtype());
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $itemtype->update($data);

        event(new NotificationSuperAdminEvent('itemtype', "update", $itemtype, $old_itemtype));

        return redirect()->route('itemtype.show', ['itemtype' => $itemtype]);
    }

    public function delete(Itemtype $itemtype): RedirectResponse
    {
        $this->authorize('delete', $itemtype);
        event(new NotificationSuperAdminEvent('itemtype', "delete", $itemtype));
        $itemtype->delete();

        return redirect()->route('itemtype.index');
    }

    public function forceDelete(Itemtype $itemtype): RedirectResponse
    {
        $this->authorize('forceDelete', $itemtype);
        event(new NotificationSuperAdminEvent('itemtype', "forced_delete", $itemtype));
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
