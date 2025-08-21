@extends('app')

@section('title')
    @if(isset($isDelete) && $isDelete)
        商品レビュー削除確認
    @elseif(isset($review))
        商品レビュー更新確認
    @else
        商品レビュー登録確認
    @endif
@endsection

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
      @if(isset($isDelete) && $isDelete)
          商品レビュー削除確認
      @elseif(isset($review))
          商品レビュー更新確認
      @else
          商品レビュー登録確認
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
        <div class="product-info">
            <div class="product-header">
                <h1>{{ $product->name }}</h1>

                <div class="product-evaluations">
                    @if (is_null( $product->reviews_avg_evaluation))
                        <span>未評価</span>
                    @else
                        @for ($i = 0; $i < ceil($product->reviews_avg_evaluation); $i++)
                            ★
                        @endfor
                        {{ ceil($product->reviews_avg_evaluation) }}
                        @endif
                    </div>
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
  
        <div class="form-row">
            <label>商品評価</label>
            {{ $data['evaluation'] }}
        </div>

        <div class="form-row">
            <label>商品コメント</label>
            {{ $data['comment'] }}
        </div>

        <form method="POST" 
              action="{{ isset($isDelete) && $isDelete
                  ? route('reviews.destroy', $review->id)
                  : (isset($review) 
                      ? route('products.reviews.update', ['product' => $product->id, 'review' => $review->id]) 
                      : route('products.reviews.store', ['product' => $product->id])) }}">
            @csrf
            @if(isset($isDelete) && $isDelete)
                @method('DELETE')
            @elseif(isset($review))
                @method('PUT')
            @endif

            <input type="hidden" name="evaluation" value="{{ $data['evaluation'] }}">
            <input type="hidden" name="comment" value="{{ $data['comment'] }}">

            <button type="submit" class="submit-button">
                @if(isset($isDelete) && $isDelete)
                    削除する
                @else
                    {{ isset($review) ? '更新する' : '登録する' }}
                @endif
            </button>
        </form>

  
        <form action="{{ (isset($isDelete) && $isDelete) 
        ? route('products.reviews.management', ['product' => $product->id])
        : (isset($review) 
            ? route('reviews.edit', ['review' => $review->id]) 
            : route('products.reviews.back', ['product' => $product->id])) }}" 
              method="POST">
            @csrf
            <button type="submit" class="submit-button-back">前に戻る</button>
        </form>

    
    </div>
</div>
@endsection