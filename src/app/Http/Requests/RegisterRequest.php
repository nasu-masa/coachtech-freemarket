<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'      => ['required', 'string', 'max:20'],
            'email'     => ['required', 'string', 'email', 'unique:users,email'],
            'password'  => ['required', 'string','confirmed', Password::min(8)],
            'password_confirmation' => ['required', 'string', 'min:8']
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => 'お名前を入力してください',
            'name.max'            => 'お名前は:max文字以下で入力してください',

            'email.required'      => 'メールアドレスを入力してください',
            'email.email'         => 'メールアドレスはメールの形式で入力してください',

            'password.required'   => 'パスワードを入力してください',
            'password.min'        => 'パスワードは:min文字以上で入力してください',
            'password.confirmed'  => 'パスワードと一致しません',

            'password_confirmation.required' => '確認用パスワードを入力してください',
            'password_confirmation.min'      => '確認用パスワードは:min文字以上で入力してください'
        ];

    }

    public function toRegisterAttributes(){
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password)
        ];
    }

}
