@extends('admin')

@section('title', '商品レビュー確認')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($data['id']) ? '商品レビュー編集確認' : '商品レビュー登録確認' }}</div>
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
          {{ $data['id'] ?? '登録後に自動採番' }}
      </div>

      {{-- 評価 --}}
      <div class="form-row">
          <label>評価</label>
          <span>{{ $data['evaluation'] ?? 0 }}</span>
      </div>

      {{-- 商品コメント --}}
      <div class="form-row">
          <label>商品コメント</label>
          {{ $data['comment'] ?? '' }}
      </div>

      {{-- 確定ボタン --}}
      @if(isset($data['id']))
          <form action="{{ route('admin.reviews.update', $data['id']) }}" method="POST">
              @csrf
              @method('PUT')
      @else
          <form action="{{ route('admin.reviews.store') }}" method="POST">
              @csrf
      @endif
          <div>
              <button type="submit" class="submit-button">
                  {{ isset($data['id']) ? '更新完了' : '登録完了' }}
              </button>
          </div>
      </form>

      {{-- 戻る --}}
      <form action="{{ route('admin.reviews.back') }}" method="POST">
          @csrf
          <div>
              <button type="submit" class="submit-button-back">前に戻る</button>
          </div>
      </form>
    </div>
</div>
@endsection