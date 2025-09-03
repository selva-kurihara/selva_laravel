@extends('admin')

@section('title', '商品レビュー詳細')

@section('header')
    <div class="header-container">
        <div class="header-left">商品レビュー詳細</div>
        <div class="header-right">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="form-wrapper">
    <div class="product-container">
      {{-- 商品写真（1枚目のみ） --}}
      <div class="product-image">
          @php
              $firstImage = $firstImagePath;
          @endphp

          @if ($firstImage && Storage::disk('public')->exists($firstImage))
              <img src="{{ Storage::url($firstImage) }}"
                  alt="商品画像1"
                  style="max-width:150px; max-height:150px; margin-right:10px;"
                  onerror="this.style.display='none'">
          @else
              <p>画像はありません</p>
          @endif
      </div>

      <div class="product-info">
        {{-- 商品ID --}}
        <div class="form-row">
            <label>商品ID</label>
            {{ $product->id ?? '' }}
        </div>

        {{-- 会員名 --}}
        <div class="form-row">
            <label>会員</label>
            {{ $member->name_sei ?? '' }}{{ $member->name_mei ?? '' }}
        </div>

        {{-- 商品名 --}}
        <div class="form-row">
            <label>商品名</label>
            {{ $product->name ?? '' }}
        </div>

        {{-- 総合評価 --}}
        <div class="form-row">
            <label>総合評価</label>
            @for ($i = 1; $i <= $product->reviews_avg_evaluation; $i++)
                ★
            @endfor
        </div>
      </div>
    </div>

    <div class="review-container">
      {{-- 評価ID --}}
      <div class="form-row">
          <label>ID</label>
          {{ $review->id ?? '登録後に自動採番' }}
      </div>

      {{-- 評価 --}}
      <div class="form-row">
          <label>評価</label>
          <span>{{ $review->evaluation ?? 0 }}</span>
      </div>

      {{-- 商品コメント --}}
      <div class="form-row">
          <label>商品コメント</label>
          {{ $review->comment ?? '' }}
      </div>

      <div class="button-group">
        {{-- 編集ボタン --}}
        <form action="{{ route('admin.reviews.edit', $review->id) }}" method="GET">
            @csrf
            <button type="submit" class="submit-button-back">編集</button>
        </form>

        {{-- 削除 --}}
        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST">
            @csrf
            <button type="submit" class="submit-button-back">削除</button>
        </form>
      </div>
    </div>
</div>
@endsection