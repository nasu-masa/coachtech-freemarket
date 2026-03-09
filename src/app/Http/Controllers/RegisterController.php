<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::storeRegister($request->toRegisterAttributes());

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        return view('auth.verify-email');
    }
}

