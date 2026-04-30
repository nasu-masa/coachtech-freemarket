<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;
use App\Models\User;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FortifyLoginRequest::class,
            LoginRequest::class,
        );

        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
    }

    public function boot(): void
    {

        Fortify::authenticateUsing(function ($request) {
            $loginRequest = app(LoginRequest::class);

            $loginRequest->merge($request->only('email', 'password'));
            $loginRequest->validateResolved();

            $user = User::where('email', $loginRequest->email)->first();

            $passwordCheck = $user && Hash::check($loginRequest->password, $user->password);

            logger()->info('Login attempt', [
                'email'      => $loginRequest->email,
                'ip'         => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'success'    => $passwordCheck,
            ]);

            if (!$passwordCheck) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません'],
                ]);
            }

            return $user;
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::redirects('login', '/attendance');

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email . '|' . $request->ip());
        });

    }
}
