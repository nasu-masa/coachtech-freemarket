<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
    public function editAddress(int $item_id)
    {
        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('purchase.address_edit', [
            'item' => Item::findOrFail($item_id),
            'latestAddress' => $latestAddress
        ]);
    }

    public function storeAddress(AddressRequest $request, int $item_id)
    {
        $user = auth()->user();
        $user->storeAddress($request->toAddressAttributes());

        return redirect()->route('purchase.checkout', ['item_id' => $item_id])
            ->with('success', '住所を更新しました');
    }
}
