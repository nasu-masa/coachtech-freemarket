<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('keyword')) {
            session(['keyword' => $request->keyword]);
        } elseif ($request->has('keyword')) {
            session()->forget('keyword');
        }

        $keyword = $request->input('keyword', session('keyword'));

        $tab     = $request->query('tab', 'recommend');

        $query = Item::where('user_id', '!=', auth()->id());

        if ($tab === 'myList') {
            if (!auth()->check()) {
                $query->whereRaw('1 = 0');
            }

            $query->whereHas('myListItems', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $items = $query->search($keyword)->paginate(20);

        return view('items.index', compact('items', 'keyword', 'tab'));
    }

    public function show($item_id)
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

        return redirect()->route('item.show', $item->id);
    }
}

