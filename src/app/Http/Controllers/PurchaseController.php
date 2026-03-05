<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Services\StripeService;

class PurchaseController extends Controller
{
    use AuthorizesRequests;

    private function findAndAuthorize($item_id)
    {
        $item = Item::findOrFail($item_id);
        $this->authorize('purchase', $item);
        return $item;
    }

    public function checkout($item_id)
    {
        $item = $this->findAndAuthorize($item_id);

        $user = auth()->user();
        $latestAddress = $user->latestAddress;

        return view('purchase.checkout', compact('item', 'latestAddress'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        $item = $this->findAndAuthorize($item_id);

        return match ($request->payment_method) {
            'convenience' => $this->payByConvenience($item),
            'card'        => $this->payByCard($item, app(StripeService::class)),
            default       => redirect()->route('purchase.checkout', ['item_id' => $item_id])
        };
    }

    private function payByConvenience($item)
    {
        auth()->user()->purchaseItem($item, 'convenience');

        return redirect()
            ->route('items.index');
    }

    private function payByCard($item, StripeService $stripeService)
    {
        $session = $stripeService->createCheckoutSession($item);

        return redirect($session->url);
    }

    public function success(Request $request, $item_id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, '無効なアクセスです');
        }

        $item = $this->findAndAuthorize($item_id);

        auth()->user()->purchaseItem($item, 'card');

        return redirect()
            ->route('items.index');
    }

    public function cancel($item_id)
    {
        return redirect()
            ->route('purchase.checkout', ['item_id' => $item_id]);
    }
}
