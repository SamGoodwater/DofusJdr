<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = (int) $request->input('paginationMaxDisplay', 25);

        // Limite la valeur maximale à 500
        if ($paginationMaxDisplay > 500) {
            $paginationMaxDisplay = 500;
        } else if ($paginationMaxDisplay < 1) {
            $paginationMaxDisplay = 1;
        } else {
            $paginationMaxDisplay = (int) $paginationMaxDisplay;
        }

        $items = Item::paginate($paginationMaxDisplay);

        return Inertia::render('item.index', [
            'items' => $items
        ]);
    }

    public function show(Item $item, Request $request): Item | RedirectResponse | Inertia
    {
        $getView = (bool) $request->input('getView', false);

        if ($getView) {
            return Inertia::render('item.show', [
                'item' => $item
            ]);
        }
        return $item;
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('item.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $item = Item::create($request->all());

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function edit(Item $item): \Inertia\Response
    {
        return Inertia::render('item.edit', [
            'item' => $item
        ]);
    }

    public function update(Item $item, Request $request): RedirectResponse
    {
        $item->update($request->all());

        return redirect()->route('item.show', ['item' => $item]);
    }

    public function delete(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('item.index');
    }
}
