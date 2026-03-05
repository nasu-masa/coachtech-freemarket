<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレスが未入力の場合はエラーになる()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => '',
            'password' => 'password12345',
        ]);

        $response->assertRedirect('/login');
        $this->get('/login')->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワードが未入力の場合はエラーになる()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $this->get('/login')->assertSee('パスワードを入力してください');
    }

    public function test_認証情報が間違っている場合はエラーになる()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'wrong@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $this->get('/login')->assertSee('ログイン情報が登録されていません');
    }

    public function test_正しい入力の場合はログインに成功する()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password12345'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password12345',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
