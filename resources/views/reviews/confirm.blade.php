@extends('app')

@section('title', '商品レビュー登録確認')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品レビュー登録確認
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

    <div class="product-info">
        <h2>{{ $product->name }}</h2>

        <div class="product-images">
            @for ($i = 1; $i <= 4; $i++)
                @php $img = $product->{'image_' . $i}; @endphp
                @if($img)
                    <img src="{{ asset('storage/' . $img) }}" alt="商品画像" style="max-width:150px; max-height:150px; margin-right:10px;">
                @endif
            @endfor
        </div>
    </div>
  
      <div class="form-row">
          <label>評価</label>
          <p>{{ $data['evaluation'] }}</p>
      </div>
  
      <div class="form-row">
          <label>コメント</label>
          <p>{{ $data['comment'] }}</p>
      </div>

      <form method="POST" action="{{ route('products.reviews.store', ['product' => $product->id]) }}">
        @csrf
      <button type="submit" class="submit-button">登録する</button>
      </form>  
  
  <form action="{{ route('products.reviews.back', ['product' => $product->id]) }}" method="POST">
      @csrf
      <button type="submit" class="submit-button-back">前に戻る</button>
  </form>
  
</div>
@endsection