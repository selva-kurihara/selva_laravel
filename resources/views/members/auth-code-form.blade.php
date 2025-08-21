@extends('app')

@section('title', 'メールアドレス認証')

@section('content')
<div class="form-wrapper">
  <h1>メールアドレス認証</h1>
  <p>新しいメールアドレスに送信された認証コードを入力してください。</p>

  <form method="POST" action="{{ route('members.email.verify', $member->id) }}">
      @csrf
      <div class="form-row">
        <label>認証コード</label>
        <input type="text" name="auth_code" maxlength="6">
      </div>
      @error('auth_code')
        <div class="required">※{{ $message }}</div>
      @enderror

      <button type="submit" class="submit-button">認証して更新</button>
  </form>
</div>
@endsection
