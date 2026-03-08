@extends('layouts.app')

@section('css')

<link rel="stylesheet" href="{{ asset('css/app.css') }}">

@endsection

@section('content')

<div class="l-container">

    <div class="c-profile">
        <div class="c-profile__left">

            {{-- プロフィール --}}
            <img src="{{ asset('storage/' .$user->avatar_path) }}"
                alt=""
                id="preview"
                class="c-profile-image">

            <h2 class="c-profile__name">{{ $user->name }}</h2>
        </div>

        <a href="/mypage/profile" class="c-image-button c-image-button--lg">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="c-tabs">
        <a href="/mypage?page=sell" class="c-tabs__item {{ $tab === 'sell' ? 'is-active' : '' }}">
            出品した商品
        </a>

        <a href="/mypage?page=buy" class="c-tabs__item {{ $tab === 'buy' ? 'is-active' : '' }}">
            購入した商品
        </a>
    </div>

</div>

<hr class="l-divider p-mypage__divider">
</hr>


{{-- 商品一覧 --}}
<div class="c-product-list">
    @foreach ($items as $item)
    <div class="c-product-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}"
            class="c-product-card__link">
            <div class="c-product-card__image-wrapper
                {{ $tab === 'buy' ? '' : ($item->status === 'sold' ? 'is-sold' : '') }}">
                <img src="{{ asset('storage/' . $item->image_path) }}"
                    alt=""
                    class="c-product-card__image"
                    onerror="
                    this.style.display='none';
                    this.parentElement.classList.add('c-product-card__no-image');
                    this.parentElement.innerText='商品画像';">
            </div>

            <p class="c-product-card__name">{{ $item->name }}</p>
        </a>
    </div>
    @endforeach
</div>


@endsection