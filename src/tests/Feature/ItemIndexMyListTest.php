<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\MyListItem;

class ItemIndexMyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_マイリストにはいいねした商品だけが表示され自分の商品は除外される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $likedItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $notLikedItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'selling',
        ]);

        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'status'  => 'selling',
        ]);

        MyListItem::create(['user_id' => $user->id, 'item_id' => $likedItem->id]);
        MyListItem::create(['user_id' => $user->id, 'item_id' => $ownItem->id]);

        $response = $this->get('/?tab=myList');

        $response->assertSee($likedItem->image_path);
        $response->assertSeeText($likedItem->name);
        $response->assertDontSeeText($notLikedItem->name);
        $response->assertDontSeeText($ownItem->name);
    }

    public function test_マイリストの購入済み商品には_sold_ラベルが付与される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $soldItem = Item::factory()->create([
            'user_id' => User::factory(),
            'status'  => 'sold',
        ]);

        MyListItem::create(['user_id' => $user->id, 'item_id' => $soldItem->id]);

        $response = $this->get('/?tab=myList');

        $response->assertSee($soldItem->image_path);
        $response->assertSee('is-sold');
    }

    public function test_未ログイン時はマイリストに何も表示されない()
    {
        $response = $this->get('/?tab=myList');

        $response->assertDontSee('<div class="c-product-card">', false);
    }
}
