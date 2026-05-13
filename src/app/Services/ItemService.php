<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ItemService
{
    private function handleKeywordSession(Request $request)
    {
        if ($request->filled('keyword')) {
            session(['keyword' => $request->keyword]);
        }

        if ($request->has('keyword') && $request->keyword === '') {
            session()->forget('keyword');
        }
    }

    private function applyMyListFilter(Builder $query, string $tab)
    {
        if ($tab !== 'myList') {
            return $query;
        }

        if (!auth()->check()) {
                return $query->whereRaw('1 = 0');
            }

        return $query->whereHas('myListItems', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    public function getIndexItems(Request $request)
    {
            $this->handleKeywordSession($request);

            $keyword = $request->input('keyword', session('keyword'));
            $tab   = $request->query('tab', 'recommend');

            $query = Item::when(auth()->check(),function ($q) {
                return $q->where('user_id', '!=', auth()->id());
            });

            $query = $this->applyMyListFilter($query, $tab);

            $items = $query->search($keyword)->paginate(50);

            return compact('items', 'keyword', 'tab');
    }
}