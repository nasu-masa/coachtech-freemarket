<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyListItem;

class MyListItemController extends Controller
{
    public function store(Request $request, $item_id)
    {
        MyListItem::add($request->user(), $item_id);

        return redirect()->back();
    }

    public function destroy(Request $request, $item_id)
    {
        MyListItem::remove($request->user(), $item_id);

        return redirect()->back();
    }
}
