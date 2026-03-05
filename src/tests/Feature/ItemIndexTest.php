<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品が一覧に表示される()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        foreach ($items as $item) {
            $response->assertSee($item->image_path);
            $response->assertSee($item->name);
        }
    }

    public function test_sold商品には_sold_ラベルが付与される()
    {
        $item = Item::factory()->create([
            'status' => 'sold',
        ]);

        $response = $this->get('/');
        $response->assertSee($item->image_path);
        $response->assertSee('is-sold');
    }

    public function test_recommendタブでは自分の商品が除外される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'status'  => 'selling',
        ]);

        $otherItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $response = $this->get('/?tab=recommend');

        $response->assertDontSeeText($ownItem->name);
        $response->assertSeeText($otherItem->name);
    }
}
