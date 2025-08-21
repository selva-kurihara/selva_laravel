@extends('app')

@section('title', 'マイページ')

@section('header')
<div class="header-container">
    <div class="header-left">マイページ</div>
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
<div class="form-wrapper">
    <table class="user-info-table">
      <tr>
        <th>氏名</th>
        <td>{{ $member->name_sei ?? '' }} {{ $member->name_mei ?? '' }}</td>
      </tr>

      <tr>
          <th>ニックネーム</th>
          <td>{{ $member->nickname ?? '' }}</td>
      </tr>

      <tr>
        <th>性別</th>
        <td>
          <div>{{ $member->gender == 1 ? '男性' : ($member->gender == 2 ? '女性' : '') }}</div>
          <div>
            <form method="GET" action="{{ route('members.edit', ['member' => Auth::id()]) }}">
              <button type="submit" class="btn">会員情報変更</button>
            </form>
          </div>
        </td>
      </tr>

      <tr>
        <th>パスワード</th>
        <td>
          <div>セキュリティのため非表示</div>
          <div>
            <form action="{{ route('members.password.edit', ['member' => Auth::id()]) }}" method="GET">
              <button type="submit" class="btn">パスワード変更</button>
            </form>
          </div>
        </td>
      </tr>
      
      <tr>
        <th>メールアドレス</th>
        <td>
          <div>{{ $member->email ?? '' }}</div>
          <div>
            <form action="{{ route('members.email.edit', ['member' => Auth::id()]) }}" method="GET">
              <button type="submit" class="btn">メールアドレス変更</button>
            </form>
          </div>
          <div>
            <form action="{{ route('products.reviews.management', ['member' => Auth::id()]) }}" method="GET">
              <button type="submit" class="btn">商品レビュー管理</button>
            </form>
          </div>
        </td>
      </tr>

    </table>

    <form method="GET" action="{{ route('members.withdraw') }}">
      <button type="submit" class="submit-button-back">退会</button>
    </form>  
</div>
@endsection
