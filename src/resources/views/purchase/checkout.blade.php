@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/purchase.css') }}">
@endsection

@section('content')
<div class="l-container p-purchase">

    {{-- 購入フォーム --}}
    <form
        action="{{ route('purchase.store', ['item_id' => $item->id]) }}"
        method="post"
        class="p-purchase__form">
        @csrf

        <div class="p-purchase__main">

            {{-- 商品情報 --}}
            <div class="p-purchase__item">
                <div class="p-purchase__item-image-wrapper">
                    <img
                        src="{{ asset('storage/' . $item->image_path) }}"
                        alt=""
                        class="c-product-card__image p-purchase__item-image"
                        onerror="
                        this.parentElement.classList.add('c-product-card__no-image');
                        this.parentElement.innerText='商品画像';">
                </div>


                <div class=" p-purchase__item-info">
                    <h2 class="p-purchase__item-name">{{ $item->name }}</h2>
                    <p class="p-purchase__item-price">
                        ¥<span class="p-purchase__item-price--value">
                            {{ number_format($item->price) }}
                        </span>
                    </p>
                </div>
            </div>

            <hr class="l-divider">

            <input type="hidden" id="old-payment" value="{{ old('payment_method') }}">

            {{-- 支払い方法 --}}
            <section class="c-section p-purchase__section">
                <h3 class="c-section__title c-section__title--md">支払い方法</h3>

                <div class="c-select p-purchase__select">

                    <input
                        type="checkbox"
                        id="payment_method"
                        class="c-select__checkbox">

                    <label for="payment_method" class="c-select__label">
                        選択してください
                    </label>

                    <div class="c-select__options">
                        <input
                            type="radio"
                            name="payment_method"
                            id="payment_convenience"
                            value="convenience"
                            class="c-select__radio"
                            {{ old('payment_method') === 'convenience' ? 'checked' : '' }}>
                        <label for="payment_convenience" class="c-select__option">
                            コンビニ払い
                        </label>

                        <input
                            type="radio"
                            name="payment_method"
                            id="payment_card"
                            value="card"
                            class="c-select__radio"
                            {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                        <label for="payment_card" class="c-select__option">
                            カード払い
                        </label>
                    </div>

                    {{-- 支払い方法エラー --}}
                    <div class="c-error--xl">
                        <span class="c-error__text">
                            @error('payment_method')
                            {{ $message }}
                            @enderror
                        </span>
                    </div>

                </div>
            </section>

            <hr class="l-divider">

            {{-- 配送先 --}}
            <section class="c-section p-purchase__section">

                <div class="p-purchase__section-container">
                    <h3 class="c-section__title c-section__title--md">配送先</h3>

                    <a
                        href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}"
                        class="p-purchase__address-edit">
                        変更する
                    </a>

                </div>

                <div class="p-purchase__address">

                    {{-- 郵便番号 --}}
                    <p class="p-purchase__address-postal">
                        〒{{ $latestAddress->postal_code ?? 'XXX-YYYY' }}
                    </p>

                    {{-- 住所表示 --}}
                    <p
                        class="p-purchase__address-detail"
                        id="address-ui"
                        data-address="{{ $latestAddress->address ?? '' }}"
                        data-building="{{ $latestAddress->building ?? '' }}">

                        {{ $latestAddress->address ?? 'ここには住所と建物が入ります'}}<br>
                        {{ $latestAddress->building ?? ''}}
                    </p>

                    {{-- hidden 住所 --}}
                    <input
                        type="hidden"
                        name="postal_code"
                        value="{{ $latestAddress->postal_code ?? ''}}">
                    <input
                        type="hidden"
                        name="address"
                        id="address-hidden"
                        value="{{ optional($latestAddress)->address ?? '' }}">
                    <input
                        type="hidden"
                        name="building"
                        id="building-hidden"
                        value="{{ optional($latestAddress)->building ?? '' }}">

                    {{-- 住所エラー --}}
                    <div class="c-error--xl">
                        <span class="c-error__text">
                            @error('address')
                            {{ $message }}
                            @enderror
                        </span>
                    </div>

                </div>
            </section>

            <hr class="l-divider">

        </div>

        {{-- 購入サマリー --}}
        <div class="p-purchase__summary">

            <table class="p-purchase-checkout__table">
                <tr class="p-purchase-checkout__table-row">
                    <th class="p-purchase-checkout__table-title">商品代金</th>
                    <td class="p-purchase-checkout__table-price">
                        ¥<span class="p-purchase-checkout__table-value">
                            {{ number_format($item->price) }}
                        </span>
                    </td>
                </tr>

                <tr class="p-purchase-checkout__table-row">
                    <th class="p-purchase-checkout__table-title">支払い方法</th>
                    <td class="p-purchase-checkout__table-value" id="summary-payment">
                        @if (old('payment_method') === 'convenience')
                        コンビニ払い
                        @elseif (old('payment_method') === 'card')
                        カード払い
                        @else
                        未選択
                        @endif
                    </td>
                </tr>
            </table>

            <div class="l-button-wrapper">
                <button type="submit" class="c-button c-button--sm c-button--primary">
                    購入する
                </button>
            </div>

        </div>

    </form>

</div>
@endsection

@section('scripts')
<script src="{{ asset('js/payment-method-select.js') }}"></script>
<script src="{{ asset('js/address-sync.js') }}"></script>
@endsection