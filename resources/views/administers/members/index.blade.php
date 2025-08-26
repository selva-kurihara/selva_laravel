@extends('admin')

@section('title', '会員一覧')

@section('header')
    <div class="header-container">
        <div class="header-left">会員一覧</div>
        <div class="header-right">
            <form action="{{ route('admin.top') }}" method="GET" class="inline-form">
                <button type="submit">トップへ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<form action="{{ route('admin.members.create') }}" method="GET">
    <button type="submit" class="create-button-list">会員登録</button>
</form>
<div class="form-wrapper">
    <form method="GET" action="{{ route('admin.members.index') }}">
        <div class="form-row">
            <label>ID</label>
            <input type="text" name="id" value="{{ request('id') }}">
        </div>
        <div class="form-row">
            <label>性別</label>
            <label>
                <input type="checkbox" name="gender[]" value="1"
                    {{ in_array('1', (array)request('gender', []), true) ? 'checked' : '' }}>
                女性
            </label>
            <label>
                <input type="checkbox" name="gender[]" value="2"
                    {{ in_array('2', (array)request('gender', []), true) ? 'checked' : '' }}>
                男性
            </label>
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
                        <a href="{{ route('admin.members.index',
                            ['sort' => 'id', 'direction' => $nextDirId] + request()->except('page')) }}">
                            ID {{ ($currentSort === 'id' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>氏名</th>
                    <th>メールアドレス</th>
                    <th>性別</th>
                    <th>
                        <a href="{{ route('admin.members.index',
                            ['sort' => 'created_at', 'direction' => $nextDirCreated] + request()->except('page')) }}">
                            登録日時 {{ ($currentSort === 'created_at' && $currentDir === 'asc') ? '▲' : '▼' }}
                        </a>
                    </th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                    <tr>
                        <td>{{ $member->id }}</td>
                        <td>{{ $member->name_sei }} {{ $member->name_mei }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            @if($member->gender == 1)
                                女性
                            @elseif($member->gender == 2)
                                男性
                            @else
                                不明
                            @endif
                        </td>
                        <td>{{ $member->created_at->format('Y/m/d') }}</td>
                        <td><a href="{{ route('admin.members.edit', $member->id) }}">編集</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- カスタムページネーション --}}
    @php
        $window = 3;
        $totalPages  = $members->lastPage();
        $currentPage = $members->currentPage();

        // 基本は current の1つ前から
        $startPage = $currentPage - 1;

        // 端の調整：開始は [1, totalPages - (window-1)] に収める
        $startPage = max(min($startPage, $totalPages - ($window - 1)), 1);

        // 終了は開始＋(window-1) ただし totalPages を超えない
        $endPage = min($startPage + ($window - 1), $totalPages);
    @endphp

    <div class="pagination flex items-center gap-2">
        {{-- 前ページ --}}
        @unless ($members->onFirstPage())
            <a href="{{ $members->previousPageUrl() }}" class="px-2 py-1 border rounded text-sm">&lt; 前へ</a>
        @endunless

        {{-- ページ番号 --}}
        @for ($i = $startPage; $i <= $endPage; $i++)
            @if ($i == $currentPage)
                <span class="px-2 py-1 border rounded bg-purple-600 text-white text-sm">{{ $i }}</span>
            @else
                <a href="{{ $members->url($i) }}" class="px-2 py-1 border rounded text-sm">{{ $i }}</a>
            @endif
        @endfor

        {{-- 次ページ --}}
        @if ($members->hasMorePages())
            <a href="{{ $members->nextPageUrl() }}" class="px-2 py-1 border rounded text-sm">次へ &gt;</a>
        @endif
    </div>

</div>
@endsection
