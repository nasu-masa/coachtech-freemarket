<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{

    public function store(CommentRequest $request, $item_id)
    {
        Comment::createFromAttributes($request->toCommentAttributes($item_id));

        return redirect()->back();
    }
}
