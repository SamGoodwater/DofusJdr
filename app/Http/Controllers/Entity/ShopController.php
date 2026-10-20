<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreShopRequest;
use App\Http\Requests\Entity\UpdateShopRequest;
use App\Http\Resources\Entity\ShopResource;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Shop;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Shop::class);

        $query = Shop::query()
            ->visibleToUser(request()->user())
            ->with([
                'createdBy',
                'npc' => fn ($q) => $q->visibleToUser(request()->user()),
                'items',
            ]);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $shops = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/shop/Index', [
            'shops' => ShopResource::collection($shops),
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShopRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Shop $shop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shop $shop)
    {
        $this->authorize('update', $shop);

        $shop->load([
            'createdBy',
            'npc',
            'items',
            'consumables',
            'resources',
        ]);

        // Charger toutes les entités disponibles pour la recherche
        $availableItems = Item::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableConsumables = Consumable::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        return Inertia::render('Pages/entity/shop/Edit', [
            'shop' => new ShopResource($shop),
            'availableItems' => $availableItems,
            'availableConsumables' => $availableConsumables,
            'availableResources' => $availableResources,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShopRequest $request, Shop $shop): RedirectResponse
    {
        $this->authorize('update', $shop);
        $shop->update($request->validated());

        return back()->with('success', 'Boutique mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Place la boutique en corbeille (soft delete).
     */
    public function delete(Request $request, Shop $shop, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($shop, $actor);

        return redirect()->route('entities.shops.index')
            ->with('success', 'Boutique placée en corbeille.');
    }

    /**
     * Update the items of a shop (avec prix/quantité/commentaire).
     */
    public function updateItems(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $request->validate([
            'items' => 'array',
        ]);

        $syncData = [];
        foreach ($request->items as $itemId => $pivotData) {
            $itemId = (int) $itemId; // S'assurer que l'ID est un entier
            if (is_array($pivotData)) {
                $pivot = [];
                // La quantité est obligatoire et doit être > 0 pour ajouter un item
                if (isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                    $pivot['quantity'] = (int) $pivotData['quantity'];
                } else {
                    // Si la quantité est 0 ou négative, on ignore cet item
                    continue;
                }
                if (isset($pivotData['price'])) {
                    $pivot['price'] = $pivotData['price'] !== '' ? (float) $pivotData['price'] : null;
                }
                if (isset($pivotData['comment'])) {
                    $pivot['comment'] = $pivotData['comment'] ?? null;
                }
                $syncData[$itemId] = $pivot;
            }
        }

        if (! empty($syncData)) {
            $itemIds = array_keys($syncData);
            $existingItems = Item::whereIn('id', $itemIds)->pluck('id')->toArray();
            $invalidIds = array_diff($itemIds, $existingItems);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['items' => 'Certains objets n\'existent pas.'])
                    ->withInput();
            }
        }

        $shop->items()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Objets de la hotel de vente mis à jour avec succès.');
    }

    /**
     * Update the consumables of a shop (avec prix/quantité/commentaire).
     */
    public function updateConsumables(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $request->validate([
            'consumables' => 'array',
        ]);

        $syncData = [];
        foreach ($request->consumables as $consumableId => $pivotData) {
            if (is_array($pivotData)) {
                $pivot = [];
                // La quantité est obligatoire et doit être > 0 pour ajouter un consumable
                if (isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                    $pivot['quantity'] = (int) $pivotData['quantity'];
                } else {
                    // Si la quantité est 0 ou négative, on ignore ce consumable
                    continue;
                }
                if (isset($pivotData['price'])) {
                    $pivot['price'] = $pivotData['price'] !== '' ? (float) $pivotData['price'] : null;
                }
                if (isset($pivotData['comment'])) {
                    $pivot['comment'] = $pivotData['comment'] ?? null;
                }
                $syncData[$consumableId] = $pivot;
            }
        }

        if (! empty($syncData)) {
            $consumableIds = array_keys($syncData);
            $existingConsumables = Consumable::whereIn('id', $consumableIds)->pluck('id')->toArray();
            $invalidIds = array_diff($consumableIds, $existingConsumables);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['consumables' => 'Certains consommables n\'existent pas.'])
                    ->withInput();
            }
        }

        $shop->consumables()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Consommables de la hotel de vente mis à jour avec succès.');
    }

    /**
     * Update the resources of a shop (avec prix/quantité/commentaire).
     */
    public function updateResources(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $request->validate([
            'resources' => 'array',
        ]);

        $syncData = [];
        foreach ($request->resources as $resourceId => $pivotData) {
            if (is_array($pivotData)) {
                $pivot = [];
                // La quantité est obligatoire et doit être > 0 pour ajouter une ressource
                if (isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                    $pivot['quantity'] = (int) $pivotData['quantity'];
                } else {
                    // Si la quantité est 0 ou négative, on ignore cette ressource
                    continue;
                }
                if (isset($pivotData['price'])) {
                    $pivot['price'] = $pivotData['price'] !== '' ? (float) $pivotData['price'] : null;
                }
                if (isset($pivotData['comment'])) {
                    $pivot['comment'] = $pivotData['comment'] ?? null;
                }
                $syncData[$resourceId] = $pivot;
            }
        }

        if (! empty($syncData)) {
            $resourceIds = array_keys($syncData);
            $existingResources = \App\Models\Entity\Resource::whereIn('id', $resourceIds)->pluck('id')->toArray();
            $invalidIds = array_diff($resourceIds, $existingResources);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['resources' => 'Certaines ressources n\'existent pas.'])
                    ->withInput();
            }
        }

        $shop->resources()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Ressources de la hotel de vente mises à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs shops.
     *
     * @param  Shop|null  $shop  Le shop unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Shop $shop = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Shop::class);
                $shops = Shop::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($shops, 'shop');
                $filename = 'shops-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $shop) {
            abort(404);
        }

        $this->authorize('view', $shop);

        $pdf = PdfService::generateForEntity($shop, 'shop');
        $filename = 'shop-'.$shop->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
