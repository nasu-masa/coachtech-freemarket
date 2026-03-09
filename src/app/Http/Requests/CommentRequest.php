<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => ['required', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'content.required' => 'コメントを入力してください。',
            'content.max'      => 'コメントは255文字以内で入力してください。',
        ];
    }

    public function toCommentAttributes($item_id)
    {
        return [
            'user_id' => $this->user()->id,
            'item_id' => $item_id,
            'content' => $this->input('content')
        ];
    }
}
