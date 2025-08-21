@extends('app')

@section('title', '商品レビュー一覧')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品レビュー一覧
    </div>
    <div class="header-right">    
      <form action="{{ route('top') }}" method="GET" class="inline-form">
          <button type="submit">トップに戻る</button>
      </form>
  </div>
</div>
@endsection

@section('content')
<div class="form-wrapper">
    <div>
        {{-- 商品情報 --}}
        <div class="product-info">
            @if (!empty($product->image_1))
                <img src="{{ asset('storage/' . $product->image_1) }}" alt="商品画像" style="max-width:150px; max-height:150px;">
            @endif
            <h1>{{ $product->name }}</h1>
            <p>総合評価
            @php
                $evaluations = $product->reviews_avg_evaluation;
            @endphp
            @if (is_null($evaluations))
                <span class="no-evaluation" style="margin-left: 30px;">未評価</span>
            @else
                @for ($i = 0; $i < ceil($evaluations); $i++)
                    ★
                @endfor
                {{ ceil($evaluations) }}
            @endif
            </p>
        </div>

        {{-- レビュー一覧 --}}
        <hr>
        @foreach ($reviews as $review)
            <div class="review-item">
                <strong>{{ $review->member->name_sei ?? '匿名' }} さん</strong>
                <span class="product-evaluations">
                    @for ($i = 1; $i <= $review->evaluation; $i++)
                        ★
                    @endfor
                    {{ $review->evaluation }}
                </span>
                <p>商品コメント: {{ $review->comment }}</p>
            </div>
            <hr>
        @endforeach

        {{-- カスタムページネーション --}}
        @php
            $window = 3; // 常に出したいページ数
            $totalPages  = $reviews->lastPage();
            $currentPage = $reviews->currentPage();

            // 基本は current の1つ前から
            $startPage = $currentPage - 1;

            // 端の調整：開始は [1, totalPages - (window-1)] に収める
            $startPage = max(min($startPage, $totalPages - ($window - 1)), 1);

            // 終了は開始＋(window-1) ただし totalPages を超えない
            $endPage = min($startPage + ($window - 1), $totalPages);
        @endphp

        <div class="pagination flex items-center gap-2">
            {{-- 前ページ --}}
            @unless ($reviews->onFirstPage())
                <a href="{{ $reviews->previousPageUrl() }}" class="px-2 py-1 border rounded text-sm">&lt; 前へ</a>
            @endunless

            {{-- ページ番号 --}}
            @for ($i = $startPage; $i <= $endPage; $i++)
                @if ($i == $currentPage)
                    <span class="px-2 py-1 border rounded bg-purple-600 text-white text-sm">{{ $i }}</span>
                @else
                    <a href="{{ $reviews->url($i) }}" class="px-2 py-1 border rounded text-sm">{{ $i }}</a>
                @endif
            @endfor

            {{-- 次ページ --}}
            @if ($reviews->hasMorePages())
                <a href="{{ $reviews->nextPageUrl() }}" class="px-2 py-1 border rounded text-sm">次へ &gt;</a>
            @endif
        </div>

        {{-- 商品詳細に戻る --}}
        <form action="{{ route('products.detail', ['product' => $product->id]) }}" method="<form action="{{ route('products.detail', ['product' => $product->id]) }}" method="GET">
            <button type="submit" class="submit-button-back">商品詳細に戻る</button>
        </form>
    </div>
</div>
@endsection
