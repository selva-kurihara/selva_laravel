@extends('app')

@section('title', '商品詳細')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品詳細
    </div>
    <div class="header-right">    
        <form action="{{ url('/top') }}" method="get" class="inline-form">
            <button type="submit">トップに戻る</button>
        </form>
    </div>
</div>
@endsection

@section('content')
<div class="form-wrapper">

    {{-- カテゴリ・サブカテゴリ --}}
    <div class="product-category">
        {{ $product->category->name ?? 'カテゴリなし' }} ＞ {{ $product->subcategory->name ?? 'サブカテゴリなし' }}
    </div>

    {{-- 商品名・更新日時 --}}
    <div class="product-header">
        <h2 class="product-name">{{ $product->name }}</h2>
        <p class="product-updated">更新日時: {{ $product->updated_at->format('Y-m-d H:i') }}</p>
    </div>

    {{-- 写真 --}}
    <div class="product-detail-images">
        @if($product->image_1)
            <img src="{{ asset('storage/' . $product->image_1) }}" alt="商品画像">
        @endif
        @if($product->image_2)
            <img src="{{ asset('storage/' . $product->image_2) }}" alt="商品画像2">
        @endif
    </div>

    {{-- 商品説明 --}}
    <div class="product-content">
        <p>■商品説明</p>
        <p>{{ $product->product_content ?? '説明なし' }}</p>
    </div>
    {{-- 総合評価 --}}
    @php
        $rating = ceil($product->reviews_avg_evaluation ?? 0);
    @endphp
    <div class="product-rating">
      <p>■商品レビュー</p>
      <span>総合評価
        @for ($i = 0; $i < $rating; $i++)
            ★
        @endfor
        {{ $rating }}
      </span>
    </div>
    <p>
      <a href="{{ route('products.reviews.index', ['product' => $product->id]) }}">>>レビューを見る</a>
    </p>
</div>

@auth
<div class="product-actions">
  <form action="{{ route('products.reviews.create', ['product' => $product->id]) }}" method="GET">
    <button type="submit" class="submit-button">
      この商品についてのレビューを登録
    </button>
  </form>
</div>
@endauth

{{-- 一覧に戻る --}}
<form action="{{ route('products.list') }}" method="GET">
  <input type="hidden" name="page" value="{{ request()->get('page', 1) }}">
  <button type="submit" class="submit-button-back">一覧に戻る</button>
</form>
@endsection
