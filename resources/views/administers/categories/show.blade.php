@extends('admin')

@section('title', '商品カテゴリ詳細')

@section('header')
    <div class="header-container">
        <div class="header-left">商品カテゴリ詳細</div>
        <div class="header-right">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="form-wrapper">
    <table class="detail-table">
        <tr>
            <th>ID</th>
            <td>{{ $category->id }}</td>
        </tr>
        <tr>
            <th>商品大カテゴリ</th>
            <td>{{ $category->name }}</td>
        </tr>
        <tr>
            <th>商品小カテゴリ</th>
            <td>
                @foreach($category->subCategories as $sub)
                    @if(!empty($sub->name))
                        <div class="subcategory-item">{{ $sub->name }}</div>
                    @endif
                @endforeach
            </td>
        </tr>
    </table>

    <div class="button-group">
        {{-- 編集ボタン --}}
        <form action="{{ route('admin.categories.edit', $category->id) }}" method="GET" style="display:inline-block;">
            <button type="submit" class="submit-button-back">編集</button>
        </form>

        {{-- 削除ボタン --}}
        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="submit-button-back">削除</button>
        </form>
    </div>
</div>
@endsection
