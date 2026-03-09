<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);

        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('purchase.address_edit', compact('item', 'latestAddress'));
    }

    public function storeAddress(AddressRequest $request, $item_id)
    {
        $user = auth()->user();

        $user->storeAddress($request->toAddressAttributes());

        return redirect()->route('purchase.checkout', ['item_id' => $item_id]);
    }
}
