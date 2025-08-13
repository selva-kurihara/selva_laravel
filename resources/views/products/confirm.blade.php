@extends('app')

@section('title', '商品登録確認')

@section('content')
<div class="form-wrapper">
    <h1>商品登録確認画面</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="form-row">
            <label>商品名</label>
            {{ $data['name'] ?? '' }}
            <input type="hidden" name="name" value="{{ $data['name'] ?? '' }}">
        </div>

        <div class="form-row">
            <label>商品カテゴリ</label>
            {{ $categoryName ?? '' }}
            <input type="hidden" name="product_category_id" value="{{ $data['product_category_id'] ?? '' }}">
        </div>

        <div class="form-row">
            <label>商品サブカテゴリ</label>
            {{ $subcategoryName ?? '' }}
            <input type="hidden" name="product_subcategory_id" value="{{ $data['product_subcategory_id'] ?? '' }}">
        </div>

        <div class="form-row">
            <label>商品写真</label><br>
            @php
                // 空文字を除外
                $paths = collect($data['imagePaths'] ?? [])->filter(function ($p) {
                    return is_string($p) && $p !== '';
                });
            @endphp

            @forelse ($paths as $index => $path)
                @if (Storage::disk('public')->exists($path))
                    <img src="{{ Storage::url($path) }}"
                        alt="商品画像{{ $loop->iteration }}"
                        style="max-width:150px; max-height:150px; margin-right:10px;"
                        onerror="this.style.display='none'">
                    <input type="hidden" name="imagePaths[]" value="{{ $path }}">
                @endif
            @empty
                <p>画像はありません</p>
            @endforelse
        </div>

        <div class="form-row">
            <label>商品説明</label>
            {{ $data['product_content'] ?? '' }}
            <input type="hidden" name="product_content" value="{{ $data['product_content'] ?? '' }}">
        </div>

        <div>
            <button type="submit" class="submit-button">商品を登録する</button>
        </div>
    </form>

    <form action="{{ route('products.back') }}" method="POST">
        @csrf
        <button type="submit" class="submit-button-back">前に戻る</button>
    </form>
</div>
@endsection
