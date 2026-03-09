<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\AddressAttributesTrait;

class ProfileRequest extends FormRequest
{
    use AddressAttributesTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'avatar'      => ['nullable', 'mimes:jpeg,png'],
            'name'        => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'     => ['required', 'string'],
        ];
    }

    public function toProfileAttributes()
    {
        $attributes = $this->only('name');

        if ($this->hasFile('avatar')) {
            $attributes['avatar_path'] = $this->file('avatar')->store('avatars', 'public');
        }

        return $attributes;
    }
}
