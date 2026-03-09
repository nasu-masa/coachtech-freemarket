<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MyListItemController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
// 商品一覧（トップ）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// 会員登録
Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// ログイン
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');

/*
|--------------------------------------------------------------------------
| Auth Required
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // いいね
    Route::post('/item/{item_id}/like', [MyListItemController::class, 'store'])
        ->name('item.like');

    // いいね解除
    Route::post('/item/{item_id}/unlike', [MyListItemController::class, 'destroy'])
        ->name('item.unlike');

    // コメント
    Route::post('/item/{item_id}/comments', [CommentController::class, 'store'])
        ->name('item.comments.store');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'checkout'])
        ->name('purchase.checkout');

    // 商品購入処理
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    // Stripe 成功・キャンセル
    Route::get('/success/{item_id}', [PurchaseController::class, 'success'])
        ->name('purchase.success')
        ->middleware('signed');

    Route::get('/cancel/{item_id}', [PurchaseController::class, 'cancel'])
        ->name('purchase.cancel');

    // 住所変更ページ
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'editAddress'])
        ->name('purchase.address.edit');

    // 購入フロー：住所変更
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'storeAddress'])
        ->name('purchase.address.store');

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');
});

/*
|--------------------------------------------------------------------------
| My Page
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage.index');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'store'])->name('mypage.profile.store');
});

/*
|--------------------------------------------------------------------------
| Verification Email
|--------------------------------------------------------------------------
*/

// 誘導画面（ログイン後の未認証チェック）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内リンク → 認証処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('mypage.profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送
Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return redirect()
        ->route('verification.notice');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
