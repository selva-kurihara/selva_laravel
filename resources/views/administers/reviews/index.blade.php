@extends('admin')

@section('title', 'レビュー一覧')

@section('header')
    <div class="header-container">
        <div class="header-left">レビュー一覧</div>
        <div class="header-right">
            <form action="{{ route('admin.top') }}" method="GET" class="inline-form">
                <button type="submit">トップへ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.reviews.create') }}" method="GET">
    <button type="submit" class="create-button-list">レビュー登録</button>
</form>
<div class="form-wrapper">
    <form method="GET" action="{{ route('admin.reviews.index') }}">
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
                        <a href="{{ route('admin.reviews.index',
                            ['sort' => 'id', 'direction' => $nextDirId] + request()->except('page')) }}">
                            ID {{ ($currentSort === 'id' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>商品ID</th>
                    <th>評価</th>
                    <th>評価コメント</th>
                    <th>
                        <a href="{{ route('admin.reviews.index',
                            ['sort' => 'created_at', 'direction' => $nextDirCreated] + request()->except('page')) }}">
                            登録日時 {{ ($currentSort === 'created_at' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>編集</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->product_id }}</td>
                        <td>{{ $review->evaluation }}</td>
                        <td><a href="{{ route('admin.reviews.show', $review->id) }}" class="text-blue-600 underline">{{ \Illuminate\Support\Str::limit($review->comment, 50) }}</a></td>
                        <td>{{ $review->created_at->format('Y/m/d') }}</td>
                        <td><a href="{{ route('admin.reviews.edit', $review->id) }}">編集</a></td>
                        <td><a href="{{ route('admin.reviews.show', $review->id) }}">詳細</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- カスタムページネーション --}}
    @php
        $window = 3;
        $totalPages  = $reviews->lastPage();
        $currentPage = $reviews->currentPage();

        // 基本は current の1つ前から
        $startPage = $currentPage - 1;

        // 端の調整：開始は [1, totalPages - (window-1)] に収める
        $startPage = max(min($startPage, $totalPages - ($window - 1)), 1);

        // 終了は開始＋(window-1) ただし totalPages を超えない
        $endPage = min($startPage + ($window - 1), $totalPages);
    @endphp

    <div class="pagination flex items-center gap-2">
        {{-- 前ページ --}}
        @unless ($reviews->onFirstPage())
            <a href="{{ $reviews->previousPageUrl() }}" class="px-2 py-1 border rounded text-sm">&lt; 前へ</a>
        @endunless

        {{-- ページ番号 --}}
        @for ($i = $startPage; $i <= $endPage; $i++)
            @if ($i == $currentPage)
                <span class="px-2 py-1 border rounded bg-purple-600 text-white text-sm">{{ $i }}</span>
            @else
                <a href="{{ $reviews->url($i) }}" class="px-2 py-1 border rounded text-sm">{{ $i }}</a>
            @endif
        @endfor

        {{-- 次ページ --}}
        @if ($reviews->hasMorePages())
            <a href="{{ $reviews->nextPageUrl() }}" class="px-2 py-1 border rounded text-sm">次へ &gt;</a>
        @endif
    </div>

</div>
@endsection
