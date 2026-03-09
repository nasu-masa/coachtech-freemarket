<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $tab = $request->query('page', 'sell');

        if (!in_array($tab, ['buy', 'sell'], true)) {
            $tab = 'sell';
        }

        $items =  match ($tab) {
            'buy'  => $user->purchasedItems(),
            'sell' => $user->items
        };

        return view('mypage.index', compact('tab', 'user', 'items'));
    }

    public function edit()
    {
        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('mypage.profile_edit', compact('user', 'latestAddress'));
    }

    public function store(ProfileRequest $request)
    {
        $user = auth()->user();

        $user->storeProfile($request->toProfileAttributes());

        $user->storeAddress($request->toAddressAttributes());

        return redirect()->route('items.index');
    }
}
