@extends('app')

@section('title', '商品レビュー登録')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品レビュー登録
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

    {{-- 上部：商品情報 --}}
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

    {{-- 下部：レビュー入力フォーム --}}
    <form action="{{ route('products.reviews.confirm', ['product' => $product->id]) }}" method="POST">
        @csrf

        <div class="form-row">
            <label>商品評価</label>
            <select name="evaluation">
                <option value="">選択してください</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('evaluation') == $i ? 'selected' : '' }}>{{ $i }} ★</option>
                @endfor
            </select>
        </div>
        @error('evaluation')
            <div class="required">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>商品コメント</label>
            <textarea name="comment" rows="4">{{ old('comment') }}</textarea>
        </div>
        @error('comment')
            <div class="required">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">商品レビュー登録確認</button>
    </form>

    <form action="{{ route('products.detail', ['product' => $product->id]) }}" method="GET">
        <button type="submit" class="submit-button-back">商品詳細に戻る</button>
    </form>
</div>
@endsection
