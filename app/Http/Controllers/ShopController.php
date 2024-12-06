<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Inertia\Response
    {
        $this->authorize('viewAny', Shop::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $shops = Shop::paginate($paginationMaxDisplay);

        return Inertia::render('shop.index', [
            'shops' => $shops,
        ]);
    }

    public function show(Shop $shop, Request $request): \Inertia\Response
    {
        $this->authorize('view', $shop);

        return Inertia::render('Shops/Show', [
            'ressources' => $shop->ressources,
            'panoply' => $shop->panoply,
        ]);
    }

    public function create(): \Inertia\Response
    {
        $this->authorize('create', Shop::class);

        return Inertia::render('shop.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Shop::class);

        $data = $request->validated();
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $data['image'] = $request->file('image')?->store('shops', 'modules');
        $shop = Shop::create($data);

        return redirect()->route('shop.show', ['shop' => $shop]);
    }

    public function edit(Shop $shop): \Inertia\Response
    {
        $this->authorize('update', $shop);

        return Inertia::render('shop.edit', [
            'shop' => $shop,
            'ressources' => $shop->ressources,
            'panoply' => $shop->panoply,
        ]);
    }

    public function update(Shop $shop, Request $request): RedirectResponse
    {
        $this->authorize('update', $shop);

        $data = $request->validated();
        $data['image'] = $request->file('image')?->store('shops', 'modules');
        $shop->update($data);

        return redirect()->route('shop.show', ['shop' => $shop]);
    }

    public function delete(Shop $shop): RedirectResponse
    {
        $this->authorize('delete', $shop);

        $shop->delete();

        return redirect()->route('shop.index');
    }

    public function forceDelete(Shop $shop): RedirectResponse
    {
        $this->authorize('forceDelete', $shop);

        $shop->forceDelete();

        return redirect()->route('shop.index');
    }

    public function restore(Shop $shop): RedirectResponse
    {
        $this->authorize('restore', $shop);

        $shop->restore();

        return redirect()->route('shop.index');
    }
}
