@extends('app')

@section('title')
    @if(isset($review))
        商品レビュー更新
    @else
        商品レビュー登録
    @endif
@endsection

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        @if(isset($review))
            商品レビュー更新
        @else
            商品レビュー登録
        @endif
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
    <div>
        {{-- 上部：商品情報 --}}
        <div class="product-info">
            <div class="product-header">
                <h1>{{ $product->name }}</h1>

                @php
                    $evaluations = $product->reviews_avg_evaluation;
                @endphp
                <div class="product-rating">
                    <span>総合評価
                    @if (is_null($evaluations))
                        <span class="no-evaluation" style="margin-left: 10px;">未評価</span>
                    @else
                        @for ($i = 0; $i < ceil($evaluations); $i++)
                            ★
                        @endfor
                        {{ ceil($evaluations) }}
                    @endif
                    </span>
                </div>
            </div>

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
        {{-- Blade --}}

        <form 
        action="{{ isset($review) 
        ? route('products.reviews.edit.confirm', ['product' => $product->id, 'review' => $review->id]) 
        : route('products.reviews.confirm', ['product' => $product->id]) }}" 
        method="POST"
        >
        @csrf

        <div class="form-row">
            <label>商品評価</label>
            <select name="evaluation">
                <option value="">選択してください</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" 
                        {{ old('evaluation', $review->evaluation ?? '') == $i ? 'selected' : '' }}>
                        {{ $i }} ★
                    </option>
                @endfor
            </select>
        </div>
        @error('evaluation')
            <div class="required">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>商品コメント</label>
            <textarea name="comment" rows="4">{{ old('comment', $review->comment ?? '') }}</textarea>
        </div>
        @error('comment')
            <div class="required">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">
            {{ isset($review) ? '商品レビュー更新' : '商品レビュー登録確認' }}
        </button>
        </form>


        <form action="{{ isset($review) 
        ? route('products.reviews.management', ['product' => $product->id]) 
        : route('products.detail', ['product' => $product->id]) }}" method="GET">
            <button type="submit" class="submit-button-back">
                @if(isset($review))
                    レビュー管理に戻る
                @else
                    商品詳細に戻る
                @endif
            </button>
        </form>
    </div>
</div>
@endsection
