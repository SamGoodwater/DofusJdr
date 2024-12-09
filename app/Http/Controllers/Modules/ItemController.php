<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Modules\Item;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Item::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $items = Item::paginate($paginationMaxDisplay);

        return Inertia::render('item.index', [
            'items' => $items,
        ]);
    }

    public function show(Item $item, Request $request): \Inertia\Response
    {
        $this->authorize('view', $item);

        return Inertia::render('Items/Show', [
            'ressources' => $item->ressources,
            'panoply' => $item->panoply,
            'type' => $item->type()
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Item::class);

        return Inertia::render('item.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        $data = DataService::extractData($request, new Item(), [
            [
                'disk' => 'modules',
                'path_name' => 'items',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $item = Item::create($data);

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function edit(Item $item): \Inertia\Response
    {
        $this->authorize('update', $item);

        return Inertia::render('item.edit', [
            'item' => $item,
            'ressources' => $item->ressources,
            'panoply' => $item->panoply,
            'type' => $item->type()
        ]);
    }

    public function update(Item $item, Request $request): RedirectResponse
    {
        $this->authorize('update', $item);

        $data = DataService::extractData($request, $item, [
            [
                'disk' => 'modules',
                'path_name' => 'items',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $item->update($data);

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function delete(Item $item): RedirectResponse
    {
        $this->authorize('delete', $item);

        $item->delete();

        return redirect()->route('item.index');
    }

    public function forceDelete(Item $item): RedirectResponse
    {
        $this->authorize('forceDelete', $item);

        $item->forceDelete();

        return redirect()->route('item.index');
    }

    public function restore(Item $item): RedirectResponse
    {
        $this->authorize('restore', $item);

        $item->restore();

        return redirect()->route('item.index');
    }
}
