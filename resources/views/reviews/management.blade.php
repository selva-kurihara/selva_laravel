@extends('app')

@section('title', '商品レビュー管理')

@section('header')
<div class="header-container">
  <div class="header-left"></div>
  <div class="header-title">商品レビュー管理</div>
  <div class="header-right">
    <form action="{{ route('top') }}" method="GET" class="inline-form">
      <button type="submit">トップに戻る</button>
    </form>
  </div>
</div>
@endsection

@section('content')
<div class="form-wrapper">

  @forelse ($reviews as $review)
    @php
      $product = $review->product;
      $category = $product?->category?->name ?? '';
      $subcategory = $product?->subcategory?->name ?? '';
      $evaluation = (int) ($review->evaluation ?? 0); // 1〜5想定
    @endphp

    <div class="review-card">
      <div class="review-thumb">
        @if ($product->image_1)
          <img src="{{ asset('storage/' . $product->image_1) }}" alt="{{ $product->name }}" style="max-width:150px; max-height:150px;">
        @endif
      </div>

      <div class="review-body">
        <div class="review-meta">
          <span class="cat">{{ $category }}</span>
          @if($subcategory)
            <span class="sep">＞</span>
            <span class="subcat">{{ $subcategory }}</span>
          @endif
        </div>

        <div class="product-name">{{ $product?->name }}</div>

        @php
            // 念のため整数キャスト
            $evaluation = (int) ($review->evaluation ?? 0);
        @endphp

        <div class="rating-row">
          <span class="stars">
            @for ($i = 1; $i <= $evaluation; $i++)
              ★
            @endfor
          </span>
          <span class="rating-num">{{ $evaluation }}</span>
        </div>

        <div class="review-comment">
            {{ mb_strimwidth($review->comment, 0, 32, '…', 'UTF-8') }}
        </div>

        <div class="review-actions">
          <form action="{{ route('reviews.edit', $review->id) }}" method="GET" class="inline-form">
            <button type="submit" class="btn primary">レビュー編集</button>
          </form>
          <form action="{{ route('reviews.delete.confirm', ['review' => $review->id]) }}" method="GET" class="inline-form">
            <button type="submit" class="btn danger">レビュー削除</button>
          </form>
        
        </div>
      </div>
    </div>
    <hr class="divider">
  @empty
    <p>レビューがありません。</p>
  @endforelse

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

  <form action="{{ route('members.mypage') }}" method="GET" class="center">
    <button type="submit" class="submit-button-back">マイページに戻る</button>
  </form>
</div>
@endsection
