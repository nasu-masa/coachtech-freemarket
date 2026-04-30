<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\MyListItem;
use Illuminate\Http\Request;

class MyListItemController extends Controller
{
    public function store(Request $request, int $item_id)
    {
        $item = Item::findOrFail($item_id);

        MyListItem::add($request->user(), $item);

        return redirect()->back();
    }

    public function destroy(Request $request, int $item_id)
    {
        $item = Item::findOrFail($item_id);

        MyListItem::remove($request->user(), $item);

        return redirect()->back();
    }
}
