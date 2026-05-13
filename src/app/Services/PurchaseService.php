<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    public function purchaseItem(User $user, Item $item, string $paymentMethod)
    {
        Log::info('購入処理開始', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $paymentMethod,
        ]);

        try {
            return DB::transaction(function () use ($user, $item, $paymentMethod) {

                Log::info('商品ステータス更新', [
                    'item_id'       => $item->id,
                    'address_id'     => $user->latestAddress->id,
                    'before_status' => $item->getOriginal('status'),
                    'after_status'  => 'sold',
                ]);

                $item->update(['status' => 'sold']);

                $purchase =  $user->purchases()->create([
                    'item_id'        => $item->id,
                    'address_id'     => $user->latestAddress->id,
                    'payment_method' => $paymentMethod,
                    'purchased_at'   => now(),
                ]);

                Log::info('購入履歴作成', [
                    'purchase_id' => $purchase->id,
                    'user_id'     => $user->id,
                    'address_id'  => $user->latestAddress->id,
                    'item_id'     => $item->id,
                    'price'       => $item->price,
                ]);

                return $purchase;
            });
        } catch (\Exception $e) {
            Log::error('購入処理エラー', [
                'user_id'        => $user->id,
                'item_id'        => $item->id,
                'payment_method' => $paymentMethod,
                'error_message'  => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}