<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    private function 住所登録済みユーザーと商品を準備()
    {

        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->actingAs($user);

        $this->post(route('purchase.address.store', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address'     => '東京都台東区テスト1-2-3',
            'building'    => 'コーポⅡ 101号室',
        ])->assertRedirect();

        $user->refresh();

        return [$user, $item];
    }

    public function test_購入画面に住所が反映される()
    {
        [$user, $item] = $this->住所登録済みユーザーと商品を準備();

        $this->actingAs($user);

        $response = $this->get(route('purchase.checkout', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee('東京都台東区テスト1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }

    public function test_購入した商品に住所が紐づく()
    {
        [$user, $item] = $this->住所登録済みユーザーと商品を準備();

        $this->get(route('purchase.checkout', ['item_id' => $item->id]));

        $user->purchaseItem($item, 'card');

        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->firstOrFail();

        $this->assertEquals($user->latestAddress->id, $purchase->address_id);
    }
}
