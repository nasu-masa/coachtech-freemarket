<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザーはコメントを投稿できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => 'これはテストコメントです。',
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです。',
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('これはテストコメントです。');
        $response->assertSee('1'); // コメント数
    }

    public function test_ゲストはコメントを投稿できない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'ゲストコメント',
        ]);
    }

    public function test_コメントは必須である()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => '',
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('コメントを入力してください');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => '',
        ]);
    }

    public function test_コメントは255文字以内である必要がある()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $longText = str_repeat('あ', 256);

        $response = $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => $longText,
        ]);

        $response->assertSessionHasErrors(['content']);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));
        $response->assertSee('コメントは255文字以内で入力してください');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $longText,
        ]);
    }
}
