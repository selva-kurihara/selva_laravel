@extends('app')

@section('title', '商品レビュー一覧')

@section('content')
<div class="form-wrapper">
    {{-- トップに戻るボタン --}}
    <form action="{{ route('top') }}" method="GET">
        <button type="submit" class="btn-top">トップに戻る</button>
    </form>

    {{-- 商品情報 --}}
    <div class="product-info">
        <img src="{{ asset('storage/' . $product->image_1) }}" alt="商品画像" style="max-width:150px; max-height:150px;">
        <h2>{{ $product->name }}</h2>
        <p>総合評価
          @php
          $rating = ceil($product->reviews_avg_evaluation ?? 0);
      @endphp
          @for ($i = 0; $i < $rating; $i++)
              ★
          @endfor
          {{ $rating }}
        </p>
    </div>

    <hr>

    {{-- レビュー一覧 --}}
    @foreach ($reviews as $review)
        <div class="review-item">
            <strong>{{ $review->member->name_sei ?? '匿名' }} さん</strong>
            <p>
                @for ($i = 1; $i <= $review->evaluation; $i++)
                    ★
                @endfor
                {{ $review->evaluation }}
            </p>
            <p>商品コメント: {{ $review->comment }}</p>
        </div>
        <hr>
    @endforeach

    {{-- ページネーション --}}
    @if($reviews->hasPages())
    <div class="pagination">
        {{-- 前へ --}}
        @if($reviews->onFirstPage() === false)
            <a href="{{ $reviews->previousPageUrl() }}">前へ</a>
        @endif

        @php
            $currentPage = $reviews->currentPage();
            $lastPage = $reviews->lastPage();
            $start = floor(($currentPage - 1) / 3) * 3 + 1; // 3ページずつ
            $end = min($start + 2, $lastPage);
        @endphp

        @for($i = $start; $i <= $end; $i++)
            <a href="{{ $reviews->url($i) }}" @if($i==$currentPage) class="active" @endif>{{ $i }}</a>
        @endfor

        {{-- 次へ --}}
        @if($currentPage < $lastPage)
            <a href="{{ $reviews->nextPageUrl() }}">次へ</a>
        @endif
    </div>
    @endif


    {{-- 商品詳細に戻る --}}
    <form action="{{ route('products.detail', ['product' => $product->id]) }}" method="<form action="{{ route('products.detail', ['product' => $product->id]) }}" method="GET">
        <button type="submit" class="submit-button-back">商品詳細に戻る</button>
    </form>
