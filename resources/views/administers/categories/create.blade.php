@extends('admin')
@section('title', isset($category->id) ? '商品カテゴリ編集' : '商品カテゴリ登録')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($category->id) ? '商品カテゴリ編集' : '商品カテゴリ登録' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="form-wrapper">
        <form method="POST" action="{{ route('admin.categories.confirm') }}">
            @csrf
            @if(isset($category->id))
                <input type="hidden" name="id" value="{{ $category->id }}">
            @endif

            <div class="form-row">
                <label>商品大カテゴリID</label>
                <div>
                    @if(isset($category->id))
                        <span>{{ $category->id }}</span>
                    @else
                        <span>登録後に自動採番</span>
                    @endif
                </div>
            </div>

            <div class="form-row">
                <label>商品大カテゴリ</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $category->name ?? '') }}" >
            </div>
            @error('name')
                <div class="error">※{{ $message }}</div>
            @enderror

            <div class="form-row">
              <label>商品小カテゴリ</label>
              <div class="subcategory-list">
                  @for($i = 0; $i < 10; $i++)
                      <div class="subcategory-item">
                          <input type="text" 
                                 name="subcategories[]" 
                                 value="{{ old('subcategories.' . $i, $subcategories[$i]->name ?? '') }}">
                      </div>
                  @endfor
              </div>
            </div>
            @error('subcategories')
                <div class="error">※{{ $message }}</div>
            @enderror
            @error('subcategories.*')
                <div class="error">※{{ $message }}</div>
            @enderror

            <button type="submit" class="submit-button">確認画面へ</button>
        </form>
    </div>
@endsection
