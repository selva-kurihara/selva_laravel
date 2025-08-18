@extends('app')

@section('title', '商品登録確認')

@section('content')
<div class="form-wrapper">
    <h1>商品登録確認画面</h1>

        <div class="form-row">
            <label>商品名</label>
            {{ $data['name'] ?? '' }}
        </div>

        <div class="form-row">
            <label>商品カテゴリ</label>
            {{ $categoryName ?? '' }}
        </div>

        <div class="form-row">
            <label>商品サブカテゴリ</label>
            {{ $subcategoryName ?? '' }}
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
                @endif
            @empty
                <p>画像はありません</p>
            @endforelse
        </div>

        <div class="form-row">
            <label>商品説明</label>
            {{ $data['product_content'] ?? '' }}
        </div>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
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
