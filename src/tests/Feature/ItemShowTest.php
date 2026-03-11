<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\MyListItem;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要な情報が表示される()
    {
        $item = Item::factory()->create([
            'name'        => '金の斧',
            'brand'       => 'golden weapon',
            'price'       => 1980,
            'description' => '誤って近所の泉に斧を落としたときに水中より現れた泉の精霊にもらった金の斧です。',
            'condition'   => '目立った傷や汚れなし',
            'image_path'  => 'test_profile.jpg',
        ]);

        $commentUser = User::factory()->create([
            'avatar_path' => 'test_profile.jpg',
        ]);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => '本物の金でできてるんですか？',
        ]);

        MyListItem::factory()->count(3)->create([
            'item_id' => $item->id,
        ]);

        $response = $this->get(route('item.show', ['item_id' => $item->id]));

        $response->assertSee($item->image_path);
        $response->assertSee('金の斧');
        $response->assertSee('golden weapon');
        $response->assertSee('1,980');
        $response->assertSee($item->description);
        $response->assertSee('目立った傷や汚れなし');

        $response->assertSee('test_profile.jpg');
        $response->assertSee($commentUser->name);
        $response->assertSee('本物の金でできてるんですか？');

        $response->assertSee('3');
    }

    public function test_複数カテゴリが表示される()
    {
        $item = Item::factory()->create();

        $categories = Category::factory()->count(3)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get(route('item.show', ['item_id' => $item->id]));

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}
