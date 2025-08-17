@extends('app')

@section('title', '退会ページ')

@section('header')
<div class="header-container">
    <div class="header-right">
        <form action="{{ route('top') }}" method="get" class="inline-form">
            <button type="submit">トップに戻る</button>
        </form>
        <form action="{{ route('logout') }}" method="POST" class="inline-form">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </div>
</div>
@endsection

@section('content')
<div class="container">
    <a>退会します。よろしいですか？</a>

  <form method="GET" action="{{ route('members.mypage') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button-back">マイページに戻る</button>
  </form>

<form method="POST" action="{{ route('members.withdraw.process') }}">
    @csrf
    <button type="submit" class="submit-button">退会する</button>
</form>




    
</div>
@endsection
