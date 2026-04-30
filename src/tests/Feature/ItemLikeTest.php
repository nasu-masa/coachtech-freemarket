<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase;

    private function setupUserAndItem(): array
    {
        $user = User::factory()->create()->first();
        $item = Item::factory()->create();

        $this->actingAs($user);

        return [$user, $item];
    }

    public function test_ユーザーが商品にいいねできカウントが増える()
    {
        [$user, $item] = $this->setupUserAndItem();

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
        [$user, $item] = $this->setupUserAndItem();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('is-liked');
    }

    public function test_ユーザーがいいね解除できカウントが減る()
    {
        [$user, $item] = $this->setupUserAndItem();

        MyListItem::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

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
