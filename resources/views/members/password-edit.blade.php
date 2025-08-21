@extends('app')

@section('title', 'パスワード変更')

@section('content')
  <div class="form-wrapper">
    <h1>パスワード変更</h1>

    <form method="POST" action="{{ route('members.password.update', $member->id) }}">
        @csrf

        <div class="form-row">
            <label>パスワード</label>
            <input type="password" name="password">
        </div>
        @error('password')
            <div class="required">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>パスワード確認</label>
            <input type="password" name="password_confirmation">
        </div>
        @error('password_confirmation')
            <div class="required">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">パスワードを変更</button>
    </form>

    <!-- マイページに戻るボタン -->
    <form method="GET" action="{{ route('members.mypage') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button-back">マイページに戻る</button>
    </form>
  </div>
@endsection
