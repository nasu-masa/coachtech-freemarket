<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Services\StripeService;
use Illuminate\Support\Facades\URL;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    private function purchaseItem(User $user, Item $item)
    {
        $this->actingAs($user);

        $mock = \Mockery::mock(StripeService::class);
        $mock->shouldReceive('createCheckoutSession')
            ->andReturn((object)[
                'id'  => 'cs_test_123',
                'url' => '/fake-stripe-redirect',
            ]);

        $this->app->instance(StripeService::class, $mock);

        $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'card',
        ]);

        $this->get(URL::signedRoute('purchase.success', ['item_id' => $item->id]));
    }

    public function test_購入が完了すると商品が_sold_になる()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItem($user, $item);

        $this->assertDatabaseHas('items', [
            'id'     => $item->id,
            'status' => 'sold',
        ]);
    }

    public function test_soldラベルが商品一覧に表示される()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItem($user, $item);

        $this->get(route('items.index'))
            ->assertSee('sold');
    }

    public function test_購入した商品がマイページの購入一覧に表示される()
    {
        $user = User::factory()->withAddress()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->purchaseItem($user, $item);

        $this->get('/mypage?page=buy')
            ->assertSee($item->name);
    }
}
