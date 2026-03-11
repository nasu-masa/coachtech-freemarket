<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_選択した支払い方法が購入画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'selling']);

        $this->actingAs($user);

        $this->get(route('purchase.checkout', ['item_id' => $item->id]));

        $response = $this->post(route('purchase.store', ['item_id' => $item->id]), [
            'payment_method' => 'convenience',
        ]);

        $response = $this->followRedirects($response);

        $response->assertSee('コンビニ払い');
    }
}
