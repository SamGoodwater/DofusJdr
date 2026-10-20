<?php

namespace Tests\Feature\Entity;

use App\Http\Middleware\CheckRole;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour ShopController
 *
 * Vérifie que :
 * - Un utilisateur peut modifier une hotel de vente qu'il a créée
 * - Un admin peut modifier n'importe quelle hotel de vente
 * - Les méthodes update* synchronisent correctement les relations avec prix/quantité/commentaire
 * - Les validations fonctionnent correctement
 * - Les policies fonctionnent correctement
 */
class ShopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        // S'assurer que la session est configurée
        $this->withSession(['_token' => 'test-token']);
    }

    /**
     * Test : Un admin peut modifier les champs d'une boutique (enregistrement sheet).
     */
    public function test_admin_can_update_shop_metadata(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create([
            'name' => 'Boutique originale',
            'description' => 'Ancien texte',
            'location' => 'Astrub',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->patch(route('entities.shops.update', $shop), [
                'name' => 'Boutique corrigée',
                'description' => 'Nouveau texte',
                'location' => 'Bonta',
            ]);

        $response->assertRedirect();
        $fresh = $shop->fresh();
        $this->assertSame('Boutique corrigée', $fresh->name);
        $this->assertSame('Nouveau texte', $fresh->description);
        $this->assertSame('Bonta', $fresh->location);
    }

    /**
     * Test : Un joueur ne peut pas modifier une boutique (AdminMutationsOnly).
     */
    public function test_player_cannot_update_shop_metadata(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $shop = Shop::factory()->create([
            'name' => 'Boutique verrouillée',
        ]);

        $response = $this->actingAs($player)
            ->patch(route('entities.shops.update', $shop), [
                'name' => 'Tentative joueur',
            ]);

        $response->assertForbidden();
        $this->assertSame('Boutique verrouillée', $shop->fresh()->name);
    }

    /**
     * Test : Un admin peut ajouter des items à une hotel de vente avec prix/quantité/commentaire
     */
    public function test_admin_can_add_items_to_shop_with_pivot_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => [
                        'quantity' => 5,
                        'price' => 100.50,
                        'comment' => 'En stock',
                    ],
                    $item2->id => [
                        'quantity' => 10,
                        'price' => 200.75,
                        'comment' => 'Limité',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $shop->fresh()->items);

        $pivot1 = $shop->fresh()->items->find($item1->id)->pivot;
        $this->assertEquals(5, $pivot1->quantity);
        $this->assertEquals(100.50, $pivot1->price);
        $this->assertEquals('En stock', $pivot1->comment);

        $pivot2 = $shop->fresh()->items->find($item2->id)->pivot;
        $this->assertEquals(10, $pivot2->quantity);
        $this->assertEquals(200.75, $pivot2->price);
        $this->assertEquals('Limité', $pivot2->comment);
    }

    /**
     * Test : Un admin peut ajouter des consommables avec prix/quantité/commentaire
     */
    public function test_admin_can_add_consumables_to_shop_with_pivot_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $consumable1 = Consumable::factory()->create();
        $consumable2 = Consumable::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateConsumables', $shop), [
                '_method' => 'PATCH',
                'consumables' => [
                    $consumable1->id => [
                        'quantity' => 3,
                        'price' => 50.25,
                        'comment' => 'Populaire',
                    ],
                    $consumable2->id => [
                        'quantity' => 7,
                        'price' => 75.00,
                        'comment' => null,
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $shop->fresh()->consumables);

        $pivot1 = $shop->fresh()->consumables->find($consumable1->id)->pivot;
        $this->assertEquals(3, $pivot1->quantity);
        $this->assertEquals(50.25, $pivot1->price);
        $this->assertEquals('Populaire', $pivot1->comment);
    }

    /**
     * Test : Un admin peut ajouter des ressources avec prix/quantité/commentaire
     */
    public function test_admin_can_add_resources_to_shop_with_pivot_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $resource1 = Resource::factory()->create();
        $resource2 = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateResources', $shop), [
                '_method' => 'PATCH',
                'resources' => [
                    $resource1->id => [
                        'quantity' => 20,
                        'price' => 15.50,
                        'comment' => 'Abondant',
                    ],
                    $resource2->id => [
                        'quantity' => 5,
                        'price' => 30.00,
                        'comment' => 'Rare',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(2, $shop->fresh()->resources);

        $pivot1 = $shop->fresh()->resources->find($resource1->id)->pivot;
        $this->assertEquals(20, $pivot1->quantity);
        $this->assertEquals(15.50, $pivot1->price);
        $this->assertEquals('Abondant', $pivot1->comment);
    }

    /**
     * Test : Un admin peut modifier les données de pivot
     */
    public function test_admin_can_update_pivot_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        // Ajouter initialement avec des données
        $shop->items()->attach($item->id, [
            'quantity' => 5,
            'price' => 100.00,
            'comment' => 'Ancien commentaire',
        ]);

        // Modifier les données
        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => [
                        'quantity' => 15,
                        'price' => 200.00,
                        'comment' => 'Nouveau commentaire',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $pivot = $shop->fresh()->items->find($item->id)->pivot;
        $this->assertEquals(15, $pivot->quantity);
        $this->assertEquals(200.00, $pivot->price);
        $this->assertEquals('Nouveau commentaire', $pivot->comment);
    }

    /**
     * Test : Un admin peut utiliser seulement certains champs de pivot
     */
    public function test_admin_can_use_partial_pivot_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        // Ajouter avec seulement quantity et price
        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => [
                        'quantity' => 10,
                        'price' => 150.00,
                    ],
                ],
            ]);

        $response->assertRedirect();
        $pivot = $shop->fresh()->items->find($item->id)->pivot;
        $this->assertEquals(10, $pivot->quantity);
        $this->assertEquals(150.00, $pivot->price);
        $this->assertNull($pivot->comment);
    }

    /**
     * Test : Un admin peut retirer des items
     */
    public function test_admin_can_remove_items_from_shop(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        $item3 = Item::factory()->create();

        // Ajouter initialement 3 items
        $shop->items()->attach([
            $item1->id => ['quantity' => 5, 'price' => 100.00],
            $item2->id => ['quantity' => 10, 'price' => 200.00],
            $item3->id => ['quantity' => 15, 'price' => 300.00],
        ]);

        // Retirer item2 et item3, garder seulement item1
        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 5, 'price' => 100.00],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $shop->fresh()->items);
        $this->assertTrue($shop->fresh()->items->contains($item1));
        $this->assertFalse($shop->fresh()->items->contains($item2));
        $this->assertFalse($shop->fresh()->items->contains($item3));
    }

    /**
     * Test : Un admin peut vider toutes les relations
     */
    public function test_admin_can_clear_all_relations(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();
        $consumable = Consumable::factory()->create();
        $resource = Resource::factory()->create();

        // Ajouter des relations initialement
        $shop->items()->attach($item->id, ['quantity' => 5, 'price' => 100.00]);
        $shop->consumables()->attach($consumable->id, ['quantity' => 3, 'price' => 50.00]);
        $shop->resources()->attach($resource->id, ['quantity' => 10, 'price' => 15.00]);

        // Vider toutes les relations
        $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [],
            ]);

        $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateConsumables', $shop), [
                '_method' => 'PATCH',
                'consumables' => [],
            ]);

        $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateResources', $shop), [
                '_method' => 'PATCH',
                'resources' => [],
            ]);

        $this->assertCount(0, $shop->fresh()->items);
        $this->assertCount(0, $shop->fresh()->consumables);
        $this->assertCount(0, $shop->fresh()->resources);
    }

    /**
     * Test : Un admin peut modifier les relations de n'importe quelle hotel de vente
     */
    public function test_admin_can_update_relations_of_any_shop(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $shop = Shop::factory()->create([
            'created_by' => $otherUser->id,
        ]);
        $item = Item::factory()->create();
        $consumable = Consumable::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5, 'price' => 100.00],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $shop->fresh()->items);

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateConsumables', $shop), [
                '_method' => 'PATCH',
                'consumables' => [
                    $consumable->id => ['quantity' => 3, 'price' => 50.00],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(1, $shop->fresh()->consumables);
    }

    /**
     * Test : Un utilisateur non-admin ne peut pas modifier les relations d'une hotel de vente
     */
    public function test_user_cannot_update_relations_of_shop(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5, 'price' => 100.00],
                ],
            ]);

        $response->assertForbidden();
        $this->assertCount(0, $shop->fresh()->items);
    }

    /**
     * Test : La validation échoue si items n'est pas un array
     */
    public function test_update_items_fails_if_items_is_not_array(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => 'not-an-array',
            ]);

        $response->assertSessionHasErrors('items');
    }

    /**
     * Test : La validation échoue si un item n'existe pas
     */
    public function test_update_items_fails_if_item_does_not_exist(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        // Supprimer l'item pour qu'il n'existe plus
        $item->delete();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => ['quantity' => 5, 'price' => 100.00],
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test : Un utilisateur non authentifié ne peut pas modifier les relations
     */
    public function test_guest_cannot_update_relations(): void
    {
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        $response = $this->post(route('entities.shops.updateItems', $shop), [
            '_method' => 'PATCH',
            'items' => [
                $item->id => ['quantity' => 5, 'price' => 100.00],
            ],
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Test : La page d'édition charge les entités disponibles
     */
    public function test_edit_page_loads_available_entities(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create(['name' => 'Item 1']);
        $consumable = Consumable::factory()->create(['name' => 'Consumable 1']);
        $shop->items()->attach($item->id, ['quantity' => 5, 'price' => 100.00]);

        $response = $this->actingAs($admin)
            ->get(route('entities.shops.edit', $shop));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/shop/Edit')
            ->has('shop')
            ->has('availableItems')
            ->has('availableConsumables')
            ->has('availableResources')
            ->where('shop.data.items.0.id', $item->id)
        );
    }

    /**
     * Test : Les quantités zéro ou négatives sont ignorées
     */
    public function test_zero_or_negative_quantities_are_ignored(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item1->id => ['quantity' => 0, 'price' => 100.00],
                    $item2->id => ['quantity' => -5, 'price' => 200.00],
                ],
            ]);

        $response->assertRedirect();
        $this->assertCount(0, $shop->fresh()->items);
    }

    /**
     * Test : Le prix peut être null
     */
    public function test_price_can_be_null(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('entities.shops.edit', $shop))
            ->post(route('entities.shops.updateItems', $shop), [
                '_method' => 'PATCH',
                'items' => [
                    $item->id => [
                        'quantity' => 5,
                        'price' => '',
                        'comment' => 'Prix à négocier',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $pivot = $shop->fresh()->items->find($item->id)->pivot;
        $this->assertEquals(5, $pivot->quantity);
        $this->assertNull($pivot->price);
        $this->assertEquals('Prix à négocier', $pivot->comment);
    }
}
