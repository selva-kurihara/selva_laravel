@extends('admin')

@section('title', '商品詳細')

@section('header')
    <div class="header-container">
        <div class="header-left">商品詳細</div>
        <div class="header-right">
            <form action="{{ route('admin.products.index') }}" method="GET" class="inline-form">
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
            <td>{{ $product->id }}</td>
        </tr>
        <tr>
            <th>会員</th>
            <td>
            @if($product->member_id === 0)
                管理者
            @elseif($product->member)
                {{ $product->member->name_sei }} {{ $product->member->name_mei }}
            @endif
            </td>
        </tr>
        <tr>
            <th>商品名</th>
            <td>{{ $product->name }}</td>
        </tr>
        <tr>
            <th>商品カテゴリ</th>
            <td>{{ $categoryName ?: '—' }} > {{ $subcategoryName ?: '—' }}</td>
        </tr>
        <tr>
            <th>商品写真</th>
            <td>
                @if(!empty($imagePaths))
                    @foreach($imagePaths as $idx => $src)
                        <img src="{{ $src }}" alt="商品画像{{ $idx+1 }}" style="max-width:150px; max-height:150px; margin-right:10px;">
                    @endforeach
                @else
                    <p>画像はありません</p>
                @endif
            </td>
        </tr>
        <tr>
            <th>商品説明</th>
            <td><div style="white-space:pre-wrap;">{{ $product->product_content ?: '—' }}</div></td>
        </tr>
    </table>

    <div class="rating-row">
        <span>総合評価
        @if (is_null($reviewAvg))
            <span class="no-evaluation" style="margin-left: 10px;">未評価</span>
        @else
            @for ($i = 0; $i < ceil($reviewAvg); $i++)
                ★
            @endfor
            {{ ceil($reviewAvg) }}
        @endif
        </span>
    </div>
    @if($reviewCount)
        @foreach($reviews as $rev)
            @php
                $revUserName = $rev->member
                ? ($rev->member->nickname ?: trim(($rev->member->name_sei ?? '').' '.($rev->member->name_mei ?? '')))
                : '匿名';
            @endphp

            <div class="review-item-group">
                <div class="review-item">
                    <span class="review-label">商品レビューID</span>
                    <span class="review-id">{{ $rev->id }}</span>
                </div>

                <div class="review-item">
                    <span class="review-user">
                        @if($rev->member)
                        <a href="{{ route('admin.members.show', $rev->member->id) }}">{{ $revUserName }}さん</a>
                        @else
                        {{ $revUserName }}さん
                        @endif
                    </span>
                    <span class="rating-stars">
                        @for ($i = 1; $i <= $rev->evaluation; $i++)
                            ★
                        @endfor
                        {{ $rev->evaluation }}
                    </span>
                </div>

                <div>
                    <span class="review-label">商品コメント</span>
                    <span class="review-comment">{{ $rev->comment }}</span>
                    <span class="review-detail">
                        <a href="{{ route('admin.reviews.show', $rev->id) }}">詳細</a>
                    </span>
                </div>
            </div>
        @endforeach
    @else
        <div class="review-item-group">
            <div class="review-item">
                <span class="review-label">商品レビュー</span>
                <span class="review-comment">まだレビューがありません</span>
            </div>
        </div>
    @endif

  </div>

  @if($reviewCount)
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

    <div class="pagination">
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
  @endif

    <div class="button-group">
        <form action="{{ route('admin.products.edit', $product->id) }}" method="GET" style="display:inline-block;">
            <button type="submit" class="submit-button-back">編集</button>
        </form>

        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="submit-button-back">削除</button>
        </form>
    </div>
</div>
@endsection
