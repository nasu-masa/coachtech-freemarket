<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィール編集ページにユーザー情報が表示される()
    {
        $user = User::factory()->create([
            'name'        => 'テスト太郎',
            'avatar_path' => 'test-avatar.png',
        ]);

        Address::factory()->create([
            'user_id'     => $user->id,
            'postal_code' => '123-4567',
            'address'     => '東京都台東区1-2-3',
            'building'    => 'コーポⅡ 101号室',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile.edit'));

        $response->assertSee('テスト太郎');
        $response->assertSee('test-avatar.png');
        $response->assertSee('123-4567');
        $response->assertSee('東京都台東区1-2-3');
        $response->assertSee('コーポⅡ 101号室');
    }
}
