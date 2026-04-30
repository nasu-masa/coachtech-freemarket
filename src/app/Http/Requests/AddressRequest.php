<?php

namespace App\Http\Requests;

use App\Traits\AddressAttributesTrait;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    use AddressAttributesTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'     => ['required']
        ];
    }

}
