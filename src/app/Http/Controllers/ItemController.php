<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request, ItemService $service)
    {
        $indexItems = $service->getIndexItems($request);

        return view('items.index', $indexItems);
    }

    public function show(int $item_id)
    {
        $item = Item::with(['categories'])
                    ->withCount(['comments', 'myListItems'])
                    ->findOrFail($item_id);

        $categories   = $item->categories;
        $isLiked      = $item->isLikedBy(auth()->id());
        $likesCount    = $item->likesCount();
        $contentsCount = $item->commentsCount();

        return view('items.show', compact(
            'item',
            'categories',
            'isLiked',
            'likesCount',
            'contentsCount',
        ));
    }

    public function create()
    {
        $categories = Category::all();

        return view('items.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $item = Item::createFromAttributes($request->toItemAttributes());

        $item->categories()->sync($request->categories());

        return redirect()->route('item.show', $item->id)
            ->with('success', '出品が完了しました');
    }
}

