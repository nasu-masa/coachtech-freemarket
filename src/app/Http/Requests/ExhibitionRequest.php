<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => ['required'],
            'description' => ['required', 'max:255'],
            'image'       => ['required', 'mimes:jpeg,png'],
            'categories'  => ['required'],
            'condition'   => ['required'],
            'price'       => ['required', 'numeric', 'min:0'],
        ];
    }

    public function toItemAttributes()
    {
        return [
            'name'        => $this->name,
            'brand'       => $this->brand,
            'description' => $this->description,
            'price'       => (int) $this->price,
            'condition'   => $this->condition,
            'user_id'     => $this->user()->id,
            'image'       => $this->file('image')
        ];
    }

    public function categories()
    {
        return $this->input('categories', []);
    }
}
