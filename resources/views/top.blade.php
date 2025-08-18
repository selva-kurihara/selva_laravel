@extends('app')

@section('title', 'トップページ')

@section('header')
    <div class="header-container">
        <div class="header-left">
            @auth
                {{-- ログイン時 --}}
                {{ Auth::user()->name_sei }} {{ Auth::user()->name_mei }} 様
            @endauth
        </div>
        <div class="header-right">
            <form action="{{ route('products.list') }}" method="GET" class="inline-form">
                <button type="submit">商品一覧</button>
            </form>
            @guest
                {{-- ログアウト時 --}}
                <form action="{{ route('members.create') }}" method="get" class="inline-form">
                    <button type="submit">新規会員登録</button>
                </form>
                <form action="{{ route('login') }}" method="get" class="inline-form">
                    <button type="submit">ログイン</button>
                </form>
            @else
                {{-- ログイン時 --}}
                <form action="{{ route('products.create') }}" method="get" class="inline-form">
                  <button type="submit">新規商品登録</button>
                </form>

                <form action="{{ route('members.mypage') }}" method="get" class="inline-form">
                  <button type="submit">マイページ</button>
                </form>
                
                <form action="{{ route('logout') }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
            @endguest
        </div>
    </div>
@endsection

@section('content')
    <div class="container">
        <div class="content">
            <!-- トップページのコンテンツをここに追加 -->
        </div>
    </div>
@endsection
