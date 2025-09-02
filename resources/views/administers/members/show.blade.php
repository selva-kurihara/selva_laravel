@extends('admin')

@section('title', '会員詳細')

@section('header')
    <div class="header-container">
        <div class="header-left">会員詳細</div>
        <div class="header-right">
            <form action="{{ route('admin.members.index') }}" method="GET" class="inline-form">
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
            <td>{{ $member->id }}</td>
        </tr>
        <tr>
            <th>氏名</th>
            <td>{{ $member->name_sei }} {{ $member->name_mei }}</td>
        </tr>
        <tr>
          <th>ニックネーム</th>
          <td>{{ $member->nickname }}</td>
        </tr>
        <tr>
          <th>性別</th>
          <td>
              @if($member->gender == 1)
                  男性
              @elseif($member->gender == 2)
                  女性
              @else
                  不明
              @endif
          </td>
        </tr>
        <tr>
          <th>パスワード</th>
          <td>セキュリティのため非表示</td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>{{ $member->email }}</td>
        </tr>
    </table>

    <div class="button-group">
        {{-- 編集ボタン --}}
        <form action="{{ route('admin.members.edit', $member->id) }}" method="GET" style="display:inline-block;">
        <button type="submit" class="submit-button-back">編集</button>
        </form>

        {{-- 削除ボタン --}}
        <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="submit-button-back">削除</button>
        </form>
    </div>
</div>
@endsection
