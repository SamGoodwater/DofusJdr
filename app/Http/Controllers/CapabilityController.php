<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capability;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CapabilityController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Capability::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $items = Capability::paginate($paginationMaxDisplay);

        return Inertia::render('item.index', [
            'items' => $items,
        ]);
    }

    public function show(Capability $item, Request $request): \Inertia\Response
    {
        $this->authorize('view', $item);

        return Inertia::render('Capabilitys/Show', [
            'ressources' => $item->ressources,
            'panoply' => $item->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Capability::class);

        return Inertia::render('item.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Capability::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $image = $request->validated('image');
        if ($image !== null && !$image->getError()) {
            $data['image'] = $image->store('capabilities', 'modules');
        }
        $item = Capability::create($data);

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function edit(Capability $item): \Inertia\Response
    {
        $this->authorize('update', $item);

        return Inertia::render('item.edit', [
            'item' => $item,
            'ressources' => $item->ressources,
            'panoply' => $item->panoply,
        ]);
    }

    public function update(Capability $item, Request $request): RedirectResponse
    {
        $this->authorize('update', $item);

        $data = $request->validated();
        $image = $request->validated('image');
        if ($image !== null && !$image->getError()) {
            $data['image'] = $image->store('capabilites', 'modules');
        }
        $item->update($data);

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function delete(Capability $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $item->delete();

        return redirect()->route('item.index');
    }

    public function forceDelete(Capability $item): RedirectResponse
    {
        $this->authorize('forceDelete', $item);

        self::deleteFile($item, 'image');
        $item->forceDelete();

        return redirect()->route('item.index');
    }

    public function restore(Capability $item): RedirectResponse
    {
        $this->authorize('restore', $item);

        $item->restore();

        return redirect()->route('item.index');
    }
}
