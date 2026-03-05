<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\MyListItem;
use Tests\TestCase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザーが商品にいいねできカウントが増える()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.like', ['item_id' => $item->id]));

        $this->assertDatabaseHas('my_list_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('1');
        $response->assertSee('is-liked');
    }

    public function test_いいね済みの場合は_isLiked_クラスが付与される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('is-liked');
    }

    public function test_ユーザーがいいね解除できカウントが減る()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $this->post(route('item.unlike', ['item_id' => $item->id]));

        $this->assertDatabaseMissing('my_list_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('0');
        $response->assertDontSee('is-liked');
    }
}
