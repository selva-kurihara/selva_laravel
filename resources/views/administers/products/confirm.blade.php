@extends('admin')

@section('title', '商品登録確認')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($data['id']) ? '商品編集確認' : '商品登録確認' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.products.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="form-wrapper">
    <h1>{{ isset($data['id']) ? '商品編集確認' : '商品登録確認' }}</h1>

    <div>
        <div class="form-row">
            <label>商品ID</label>
            {{ $data['id'] ?? '登録後に自動採番' }}
        </div>

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

        @if(isset($data['id']))
            <form action="{{ route('admin.products.update', $data['id']) }}" method="POST">
                @csrf
                @method('PUT')
        @else
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
        @endif
            @csrf
            <div>
                <button type="submit" class="submit-button">
                    {{ isset($data['id']) ? '更新する' : '登録する' }}
                </button>
            </div>
        </form>

        <form action="{{ route('admin.products.back') }}" method="POST">
            @csrf
            <div>
                <button type="submit" class="submit-button-back">前に戻る</button>
            </div>
        </form>
    </div>
</div>
@endsection
