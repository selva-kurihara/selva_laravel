@extends('admin')
@section('title', isset($data['id']) ? '商品カテゴリ編集確認' : '商品カテゴリ登録確認')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($data['id']) ? '商品カテゴリ編集確認' : '商品カテゴリ登録確認' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="form-wrapper">
        <h1>{{ isset($data['id']) ? '商品カテゴリ編集内容の確認' : '商品カテゴリ登録内容の確認' }}</h1>

        {{-- 表示部分 --}}
        <div class="form-row">
            <label>商品大カテゴリID</label>
            <div class="name-inputs">
                {{ $data['id'] ?? '登録後に自動採番' }}
            </div>
        </div>

        <div class="form-row">
            <label>商品大カテゴリ</label>
            <div class="name-inputs">
                {{ $data['name'] }}
            </div>
        </div>

        <div class="form-row">
            <label>商品小カテゴリ</label>
            <div class="subcategory-list">
                @foreach($data['subcategories'] as $subcategory)
                    @if(!empty($subcategory))
                        <div class="subcategory-item">{{ $subcategory }}</div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- 登録／更新ボタン --}}
        @if(isset($data['id']))
            <form method="POST" action="{{ route('admin.categories.update', $data['id']) }}">
                @csrf
                @method('PUT')
        @else
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
        @endif
            {{-- hidden にデータを全部詰める --}}
            <input type="hidden" name="name" value="{{ $data['name'] }}">
            @foreach($data['subcategories'] as $subcategory)
                <input type="hidden" name="subcategories[]" value="{{ $subcategory }}">
            @endforeach
            @if(isset($data['id']))
                <input type="hidden" name="id" value="{{ $data['id'] }}">
            @endif

            <button type="submit" class="submit-button">
                {{ isset($data['id']) ? '更新する' : '登録する' }}
            </button>
        </form>

        {{-- 戻るボタン --}}
        <form method="POST" action="{{ route('admin.categories.back') }}">
            @csrf
            <button type="submit" class="submit-button-back">前に戻る</button>
        </form>
    </div>
@endsection
