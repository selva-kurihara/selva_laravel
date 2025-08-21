@extends('app')

@section('title', '商品一覧')

@section('header')
<div class="header-container">
    <div class="header-left"></div>
    <div class="header-title">
        商品一覧
    </div>
    <div class="header-right">
        @auth
            {{-- ログイン時のみ表示 --}}
            <form action="{{ route('products.create') }}" method="get" class="inline-form">
                <button type="submit">新規商品登録</button>
            </form>
        @endauth
    </div>
</div>
@endsection

@section('content')
<div class="form-wrapper">

    {{-- 検索フォーム --}}
    <form action="{{ route('products.list') }}" method="GET">
        <div class="form-row">
            <label>カテゴリ</label>
            <select name="product_category_id" id="category">
                <option value="">カテゴリ</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ request('product_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="product_subcategory_id" id="subcategory">
                <option value="">サブカテゴリ</option>
            </select>
        </div>

        <div class="form-row">
            <label>フリーワード</label>
            <input type="text" name="free_word" value="{{ request('free_word') }}">
        </div>

        <button type="submit" class="search-button">商品検索</button>
    </form>

    {{-- 商品一覧 --}}
    <div class="product-list">
        @forelse($products as $product)
        <article class="product-item">
            <div class="product-thumb">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
            @else
                <div class="no-image">画像なし</div>
            @endif
            </div>
            <div class="product-body">
            {{-- カテゴリ > サブカテゴリ --}}
            <div class="product-breadcrumb">
                {{ $product->category->name ?? '' }}
                @if(!empty($product->subcategory))
                ＞ {{ $product->subcategory->name }}
                @endif
            </div>
            {{-- 商品名 --}}
            <h3 class="product-name">
                <a href="{{ route('products.detail', $product->id) }}">{{ $product->name }}</a>
            </h3>

            {{-- 総合評価 --}}
            @php
                $evaluations = $product->reviews_avg_evaluation;
            @endphp

            <div class="product-evaluations">
                @if (is_null($evaluations))
                    <span class="no-evaluation">未評価</span>
                @else
                    @for ($i = 0; $i < ceil($evaluations); $i++)
                        ★
                    @endfor
                    {{ ceil($evaluations) }}
                @endif
            </div>

            <div class="product-actions">
                <a href="{{ route('products.detail', ['product' => $product->id, 'page' => request()->get('page', 1)]) }}" class="detail-button">詳細</a>
            </div>
            </div>
        </article>
        @empty
        <p class="empty">該当する商品がありません</p>
        @endforelse
    </div>

    {{-- カスタムページネーション --}}
    @php
        $window = 3; // 常に出したいページ数
        $totalPages  = $products->lastPage();
        $currentPage = $products->currentPage();

        // 基本は current の1つ前から
        $startPage = $currentPage - 1;

        // 端の調整：開始は [1, totalPages - (window-1)] に収める
        $startPage = max(min($startPage, $totalPages - ($window - 1)), 1);

        // 終了は開始＋(window-1) ただし totalPages を超えない
        $endPage = min($startPage + ($window - 1), $totalPages);
    @endphp

    <div class="pagination flex items-center gap-2">
        {{-- 前ページ --}}
        @unless ($products->onFirstPage())
            <a href="{{ $products->previousPageUrl() }}" class="px-2 py-1 border rounded text-sm">&lt; 前へ</a>
        @endunless

        {{-- ページ番号 --}}
        @for ($i = $startPage; $i <= $endPage; $i++)
            @if ($i == $currentPage)
                <span class="px-2 py-1 border rounded bg-purple-600 text-white text-sm">{{ $i }}</span>
            @else
                <a href="{{ $products->url($i) }}" class="px-2 py-1 border rounded text-sm">{{ $i }}</a>
            @endif
        @endfor

        {{-- 次ページ --}}
        @if ($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="px-2 py-1 border rounded text-sm">次へ &gt;</a>
        @endif
    </div>

</div>

{{-- トップに戻る --}}
<form action="{{ url('/top') }}" method="GET">
    <button type="submit" class="submit-button-back">トップに戻る</button>
</form>

{{-- ajaxによるサブカテゴリ切替 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {
        const oldCategoryId = "{{ request('product_category_id') }}";
        const oldSubcategoryId = "{{ request('product_subcategory_id') }}";

        if (oldCategoryId) {
            $('#category').val(oldCategoryId);
            loadSubcategories(oldSubcategoryId);
        }

        $('#category').on('change', function() {
            loadSubcategories();
        });
    });

    function loadSubcategories(selectedSubcategoryId = null) {
        const categoryId = $('#category').val();
        const $sub = $('#subcategory');
        $sub.empty().append('<option value="">サブカテゴリ</option>');

        if (categoryId) {
            $.getJSON(`/subcategories/${categoryId}`, function(data) {
                $.each(data, function (_, sc) {
                    $sub.append(new Option(sc.name, sc.id));
                });

                if (selectedSubcategoryId) {
                    $sub.val(String(selectedSubcategoryId));
                }
            });
        }
    }
</script>
@endsection
