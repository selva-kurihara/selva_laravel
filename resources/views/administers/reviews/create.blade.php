@extends('admin')

@section('title', isset($review) ? '商品レビュー編集' : '商品レビュー登録')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($review->id) ? '商品レビュー編集' : '商品レビュー登録' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
<div class="form-wrapper">
    <h1>{{ isset($review) ? '商品レビュー編集' : '商品レビュー登録' }}</h1>

    <form action="{{ route('admin.reviews.confirm') }}" method="POST">
        @csrf
        @if(isset($review))
            <input type="hidden" name="id" value="{{ $review->id }}">
        @endif

        <div class="form-row">
            <label>商品</label>
            <select name="product_id">
                <option value="">選択してください</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ old('product_id', $review->product_id ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('product_id')
            <div class="error">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>会員</label>
            <select name="member_id">
                <option value="">選択してください</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}"
                        {{ old('member_id', $review->member_id ?? '') == $member->id ? 'selected' : '' }}>
                        {{ $member->name_sei }}{{ $member->name_mei }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('member_id')
            <div class="error">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>ID</label>
            @if(isset($review))
                <span>{{ $review->id }}</span>
            @else
                <span>登録後に自動採番</span>
            @endif
        </div>

        <div class="form-row">
            <label>商品評価</label>
            <select name="evaluation">
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}"
                        {{ old('evaluation', $review->evaluation ?? 5) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        @error('evaluation')
            <div class="error">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>商品コメント</label>
            <textarea name="comment" rows="4">{{ old('comment', $review->comment ?? '') }}</textarea>
        </div>
        @error('comment')
            <div class="error">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">確認画面へ</button>
    </form>
</div>
@endsection
