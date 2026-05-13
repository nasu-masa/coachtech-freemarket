<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $tab = $request->query('page', 'sell');

        if (!in_array($tab, ['buy', 'sell'], true)) {
            $tab = 'sell';
        }

        $items = match ($tab) {
            'buy'  => $user->purchasedItems()->paginate(50),
            'sell' => $user->items()->paginate(50),
        };

        return view('mypage.index', compact('user', 'tab', 'items'));
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

        return redirect()->route('items.index')
            ->with('success', 'プロフィール設定が完了しました');
    }
}
