@extends('admin')

@section('title', '商品カテゴリ一覧')

@section('header')
    <div class="header-container">
        <div class="header-left">商品カテゴリ一覧</div>
        <div class="header-right">
            <form action="{{ route('admin.top') }}" method="GET" class="inline-form">
                <button type="submit">トップへ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.categories.create') }}" method="GET">
    <button type="submit" class="create-button-list">商品カテゴリ登録</button>
</form>
<div class="form-wrapper">
    <form method="GET" action="{{ route('admin.categories.index') }}">
        <div class="form-row">
            <label>ID</label>
            <input type="text" name="id" value="{{ request('id') }}">
        </div>
        <div class="form-row">
            <label>フリーワード</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}">
        </div>
        <button type="submit" class="search-button">検索する</button>
    </form>

    <div class="member-list">
        @php
            $currentSort = request('sort', 'id');
            $currentDir  = request('direction', 'desc');

            $nextDirId = ($currentSort === 'id')
                ? ($currentDir === 'asc' ? 'desc' : 'asc')
                : 'asc';

            $nextDirCreated = ($currentSort === 'created_at')
                ? ($currentDir === 'asc' ? 'desc' : 'asc')
                : 'asc';
        @endphp

        <table class="member-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ route('admin.categories.index',
                            ['sort' => 'id', 'direction' => $nextDirId] + request()->except('page')) }}">
                            ID {{ ($currentSort === 'id' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>商品大カテゴリ</th>
                    <th>
                        <a href="{{ route('admin.categories.index',
                            ['sort' => 'created_at', 'direction' => $nextDirCreated] + request()->except('page')) }}">
                            登録日時 {{ ($currentSort === 'created_at' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>編集</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td><a href="{{ route('admin.categories.show', $category->id) }}">{{ $category->name }}</a></td>
                        <td>{{ $category->created_at->format('Y/m/d') }}</td>
                        <td><a href="{{ route('admin.categories.edit', $category->id) }}">編集</a></td>
                        <td><a href="{{ route('admin.categories.show', $category->id) }}">詳細</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- カスタムページネーション --}}
    @php
        $window = 3;
        $totalPages  = $categories->lastPage();
        $currentPage = $categories->currentPage();

        // 基本は current の1つ前から
        $startPage = $currentPage - 1;

        // 端の調整：開始は [1, totalPages - (window-1)] に収める
        $startPage = max(min($startPage, $totalPages - ($window - 1)), 1);

        // 終了は開始＋(window-1) ただし totalPages を超えない
        $endPage = min($startPage + ($window - 1), $totalPages);
    @endphp

    <div class="pagination flex items-center gap-2">
        {{-- 前ページ --}}
        @unless ($categories->onFirstPage())
            <a href="{{ $categories->previousPageUrl() }}" class="px-2 py-1 border rounded text-sm">&lt; 前へ</a>
        @endunless

        {{-- ページ番号 --}}
        @for ($i = $startPage; $i <= $endPage; $i++)
            @if ($i == $currentPage)
                <span class="px-2 py-1 border rounded bg-purple-600 text-white text-sm">{{ $i }}</span>
            @else
                <a href="{{ $categories->url($i) }}" class="px-2 py-1 border rounded text-sm">{{ $i }}</a>
            @endif
        @endfor

        {{-- 次ページ --}}
        @if ($categories->hasMorePages())
            <a href="{{ $categories->nextPageUrl() }}" class="px-2 py-1 border rounded text-sm">次へ &gt;</a>
        @endif
    </div>

</div>
@endsection
