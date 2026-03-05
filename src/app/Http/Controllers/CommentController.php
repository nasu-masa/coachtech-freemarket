<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{

    public function store(CommentRequest $request, $item_id)
    {
        Comment::createFromRequest([
            'user_id' => $request->user()->id,
            'item_id' => $item_id,
            'content' => $request->validated()['content'],
        ]);

        return redirect()->back();
    }
}
