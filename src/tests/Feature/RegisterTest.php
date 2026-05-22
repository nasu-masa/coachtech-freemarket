<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private string $registerUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerUrl = route('register');
    }

    public function test_名前が未入力の場合はエラーになる()
    {
        $this->from($this->registerUrl)->post($this->registerUrl, [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ])->assertRedirect($this->registerUrl)->assertSessionHasErrors(['name']);

        $this->get($this->registerUrl)->assertSee('お名前を入力してください');
    }

    public function test_メールアドレスが未入力の場合はエラーになる()
    {
        $this->from($this->registerUrl)->post($this->registerUrl, [
            'name' => 'test',
            'email' => '',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ])->assertRedirect($this->registerUrl)->assertSessionHasErrors(['email']);

        $this->get($this->registerUrl)->assertSee('メールアドレスを入力してください');
    }

    public function test_パスワードが未入力の場合はエラーになる()
    {
        $this->from($this->registerUrl)->post($this->registerUrl, [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => ''
        ])->assertRedirect($this->registerUrl)->assertSessionHasErrors(['password']);

        $this->get($this->registerUrl)->assertSee('パスワードを入力してください');
    }

    public function test_パスワードが7文字以下の場合はエラーになる()
    {
        $this->from($this->registerUrl)->post($this->registerUrl, [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567'
        ])->assertRedirect($this->registerUrl)->assertSessionHasErrors(['password']);

        $this->get($this->registerUrl)->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_パスワード確認が一致しない場合はエラーになる()
    {
        $this->from($this->registerUrl)->post($this->registerUrl, [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password12345',
            'password_confirmation' => 'confirmation12345'
        ])->assertRedirect($this->registerUrl)->assertSessionHasErrors(['password']);

        $this->get($this->registerUrl)->assertSee('パスワードと一致しません');
    }

    public function test_正しい入力の場合は登録が成功する()
    {
        $response = $this->post($this->registerUrl, [
            'name' => 'test',
            'email' => 'test@gmail.com',
            'password' => 'password12345',
            'password_confirmation' => 'password12345'
        ]);

        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
        ]);
    }
}
