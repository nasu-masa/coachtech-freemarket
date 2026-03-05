<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_会員登録後に認証メールが送信される()
    {
        Notification::fake();

        $this->post('/register', [
            'name'                  => 'test',
            'email'                 => 'verifytest@example.com',
            'password'              => 'password12345',
            'password_confirmation' => 'password12345',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'verifytest@example.com',
        ]);

        $user = User::where('email', 'verifytest@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
        $this->assertNull($user->email_verified_at);
    }

    public function test_認証画面にはMailHogへのリンクが表示される()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertSee('http://localhost:8025');
    }

    public function test_認証リンクにアクセスするとプロフィール編集ページへリダイレクトされる()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('mypage.profile.edit'));
    }
}
