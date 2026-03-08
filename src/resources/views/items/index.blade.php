@extends('layouts.app')

@section('content')

{{-- タブ --}}
<div class="l-container p-page-header">

    <div class="c-tabs">
        <a href="/?tab=recommend&keyword={{ $keyword }}"
            class="c-tabs__item {{ $tab === 'recommend' ? 'is-active' : '' }}">
            おすすめ
        </a>

        <a href="/?tab=myList&keyword={{ $keyword }}"
            class="c-tabs__item {{ $tab === 'myList' ? 'is-active' : '' }}">
            マイリスト
        </a>
    </div>

</div>

<hr class="l-divider">

{{-- 商品一覧 --}}
<div class="c-product-list">
    @foreach ($items as $item)
    <div class="c-product-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="c-product-card__link">
            @imageExists($item->image_path)
            <div class="c-product-card__image-wrapper
                        c-image-wrapper-lg
                        {{ $item->status === 'sold' ? 'is-sold' : '' }}">
                <img src="{{ asset('storage/' . $item->image_path) }}"
                    alt="商品画像"
                    class="c-product-card__image">
            </div>
            @else
            <div class="c-product-card__no-image">商品画像</div>
            @endimageExists

            <p class="c-product-card__name">{{ $item->name }}</p>
        </a>
    </div>
    @endforeach
</div>

@endsection