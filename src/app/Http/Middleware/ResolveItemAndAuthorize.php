<?php

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ResolveItemAndAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Request received', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'body' => $request->all(),
        ]);

        $itemId = $request->route('item_id');

        $item = Item::findOrFail($itemId);
        Gate::authorize('purchase', $item);

        $request->route()->setParameter('item_id', $item);

        return $next($request);
    }
}
