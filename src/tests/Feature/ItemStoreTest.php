<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品が正常に保存され画像もアップロードされる()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();

        $this->get(route('sell.create'));

        $postData = [
            'categories'  => [$category->id],
            'condition'   => '良好',
            'name'        => '黄金の斧',
            'brand'       => 'golden weapon',
            'description' => '隣村の泉のそばに落ちてました',
            'price'       => 3980,
            'image'       => UploadedFile::fake()->create('test.jpeg', 100, 'image/jpeg'),
        ];

        $response = $this->post(route('sell.store'), $postData);

        $item = Item::first();
        $response->assertRedirect(route('item.show', ['item_id' => $item->id]));

        $this->assertDatabaseHas('items', [
            'condition'   => '良好',
            'name'        => '黄金の斧',
            'brand'       => 'golden weapon',
            'description' => '隣村の泉のそばに落ちてました',
            'price'       => 3980,
            'image_path'  => $item->image_path,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id'     => $item->id,
            'category_id' => $category->id,
        ]);

        Storage::disk('public')->assertExists($item->image_path);
    }
}
