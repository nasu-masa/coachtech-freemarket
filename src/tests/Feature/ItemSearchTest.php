<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_部分一致キーワードで商品が検索できる()
    {
        $user = User::factory()->create()->first();
        $this->actingAs($user);

        Item::factory()->create(['name' => '金の斧']);
        Item::factory()->create(['name' => '銀の斧']);
        Item::factory()->create(['name' => '銅のじょうろ']);

        $response = $this->get('/?keyword=斧');

        $response->assertSeeText('金の斧');
        $response->assertSeeText('銀の斧');
        $response->assertDontSeeText('銅のじょうろ');
    }

    public function test_検索キーワードがマイリストタブでも保持される()
    {
        $this->get('/?keyword=斧')
            ->assertSee('value="斧"', false);

        $response = $this->get('/?tab=myList');

        $response->assertSee('value="斧"', false);
    }
}
