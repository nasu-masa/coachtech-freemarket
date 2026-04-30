<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Services\PurchaseService;
use App\Services\StripeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    private PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function checkout(Item $item)
    {
        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('purchase.checkout', compact('item', 'latestAddress'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        return match ($request->payment_method) {
            'convenience' => $this->payByConvenience($item),
            'card'        => $this->payByCard($item, app(StripeService::class)),
            default       => redirect()->route('purchase.checkout', ['item_id' => $item])
        };
    }

    private function payByConvenience(Item $item)
    {
        $this->purchaseService->purchaseItem(auth()->user(), $item, 'convenience');

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    private function payByCard(Item $item, StripeService $stripeService)
    {
        $session = $stripeService->createCheckoutSession($item);

        return redirect($session->url);
    }

    public function success(Item $item)
    {
        $this->purchaseService->purchaseItem(auth()->user(), $item, 'card');

        return redirect()
            ->route('items.index')
            ->with('success', '購入手続きが完了しました');
    }

    public function cancel(Item $item)
    {
        return redirect()
            ->route('purchase.checkout', ['item_id' => $item->id])
            ->with('error', '購入手続きが中断されました');
    }
}
