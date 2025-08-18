@extends('app')

@section('title', '商品レビュー登録完了')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品レビュー登録完了
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
    <p>商品レビューの登録が完了しました。</p>

    <form action="{{ route('products.reviews.index', ['product' => $product->id]) }}" method="GET">
        <button type="submit" class="submit-button-back">商品レビュー一覧へ</button>
    </form>

    <form action="{{ route('products.detail', ['product' => $product->id]) }}" method="GET">
        <button type="submit" class="submit-button-back">商品詳細に戻る</button>
    </form>
</div>
@endsection