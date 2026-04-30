<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function purchaseItem(User $user, Item $item, string $paymentMethod)
    {
        return DB::transaction(function () use ($user, $item, $paymentMethod) {
            $item->update(['status' => 'sold']);

            return $user->purchases()->create([
                'item_id'        => $item->id,
                'address_id'     => $user->latestAddress->id,
                'payment_method' => $paymentMethod,
                'purchased_at'   => now(),
            ]);
        });
    }
}