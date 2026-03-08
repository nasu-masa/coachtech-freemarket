<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_マイページにユーザー情報が表示される()
    {
        $user = User::factory()->withAddress()->create([
            'name' => 'テスト太郎',
            'avatar_path' => 'test-avatar.png',
        ]);

        $sellingItems = Item::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
                'status'  => 'selling',
            ]);

        $purchasedItems = Purchase::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
                'address_id' => $user->latestAddress->id,
            ]);

        $this->actingAs($user);

        // プロフィールページ（出品した商品タブ）
        $response = $this->get(route('mypage.index'));

        $response->assertSee('テスト太郎');
        $response->assertSee('test-avatar.png');

        foreach ($sellingItems as $item) {
            $response->assertSee($item->name);
        }

        // 購入済みタブ
        $response = $this->get('/mypage?page=buy');

        foreach ($purchasedItems as $purchase) {
            $response->assertSee($purchase->item->name);
        }
    }
}
