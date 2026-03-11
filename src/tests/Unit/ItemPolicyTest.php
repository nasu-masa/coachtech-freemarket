<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Policies\ItemPolicy;

class ItemPolicyTest extends TestCase
{
    private function makeUser(int $id)
    {
        return User::factory()->make(['id' => $id]);
    }

    private function makeItem(array $overrides = [])
    {
        return Item::factory()->make($overrides);
    }

    private function policy()
    {
        return new ItemPolicy;
    }

    public function test_自分の商品は購入できない()
    {
        $user = $this->makeUser(1);
        $item = $this->makeItem([
            'user_id' => 1,
            'status'  => 'selling',
        ]);

        $this->assertFalse($this->policy()->purchase($user, $item));
    }

    public function test_売り切れの商品は購入できない()
    {
        $user = $this->makeUser(1);
        $item = $this->makeItem([
            'user_id' => 2,
            'status'  => 'sold',
        ]);

        $this->assertFalse($this->policy()->purchase($user, $item));
    }

    public function test_他人の商品で販売中なら購入できる()
    {
        $user = $this->makeUser(1);
        $item = $this->makeItem([
            'user_id' => 2,
            'status' => 'selling',
        ]);

        $this->assertTrue($this->policy()->purchase($user, $item));
    }
}
