<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    private function 住所登録済みユーザーと商品を準備()
    {

        $user = User::factory()->create()->first();
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

        $response = $this->get(route('purchase.checkout', ['item_id' => $item->id]));
        $response->assertStatus(200);

        $response->assertSee('東京都台東区テスト1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }

    public function test_購入した商品に住所が紐づく()
    {
        [$user, $item] = $this->住所登録済みユーザーと商品を準備();

        $this->get(route('purchase.checkout', ['item_id' => $item->id]));

        $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
            'postal_code'    => $user->latestAddress->postal_code,
            'address'        => $user->latestAddress->address,
            'building'       => $user->latestAddress->building,
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $user->latestAddress->id,
        ]);

        $this->assertEquals('sold', $item->fresh()->status);
    }
}
