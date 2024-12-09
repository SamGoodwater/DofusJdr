<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\ShopFilterRequest;
use App\Events\NotificationSuperAdminEvent;
use App\Models\Modules\Shop;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Services\DataService;

class ShopController extends Controller
{
    use AuthorizesRequests;

    public function index(ShopFilterRequest $request): \Inertia\Response
    {
        $this->authorize('viewAny', Shop::class);

        // Récupère la valeur de 'paginationMaxDisplay' depuis la requête, avec une valeur par défaut de 25
        $paginationMaxDisplay = max(1, min(500, (int) $request->input('paginationMaxDisplay', 25)));

        $shops = Shop::paginate($paginationMaxDisplay);

        return Inertia::render('shop.index', [
            'shops' => $shops,
        ]);
    }

    public function show(Shop $shop, ShopFilterRequest $request): \Inertia\Response
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

    public function store(ShopFilterRequest $request): RedirectResponse
    {
        $this->authorize('create', Shop::class);

        $data = DataService::extractData($request, new Shop(), [
            [
                'disk' => 'modules',
                'path_name' => 'shops',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $data['created_by'] = Auth::user()?->id ?? "-1";
        $shop = Shop::create($data);
        $shop->items()->sync($request->input('items'));
        $shop->ressources()->sync($request->input('ressources'));
        $shop->consumables()->sync($request->input('consumables'));

        event(new NotificationSuperAdminEvent('shop', 'create',  $shop));

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

    public function update(Shop $shop, ShopFilterRequest $request): RedirectResponse
    {
        $this->authorize('update', $shop);
        $old_shop = $shop;

        $data = DataService::extractData($request, $shop, [
            [
                'disk' => 'modules',
                'path_name' => 'shops',
                'name_bd' => 'image',
                'is_multiple_files' => false,
                'compress' => true
            ]
        ]);
        if ($data === []) {
            return redirect()->back()->withInput();
        }
        $shop->update($data);
        $shop->items()->sync($request->input('items'));
        $shop->ressources()->sync($request->input('ressources'));
        $shop->consumables()->sync($request->input('consumables'));

        event(new NotificationSuperAdminEvent('shop', "update", $shop, $old_shop));

        return redirect()->route('shop.show', ['shop' => $shop]);
    }

    public function delete(Shop $shop): RedirectResponse
    {
        $this->authorize('delete', $shop);
        event(new NotificationSuperAdminEvent('shop', "delete", $shop));
        $shop->delete();

        return redirect()->route('shop.index');
    }

    public function forceDelete(Shop $shop): RedirectResponse
    {
        $this->authorize('forceDelete', $shop);

        $shop->items()->detach();
        $shop->ressources()->detach();
        $shop->consumables()->detach();

        DataService::deleteFile($shop, 'image');
        event(new NotificationSuperAdminEvent('shop', "forced_delete", $shop));
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
