@extends('app')

@section('title', 'パスワード再設定')

@section('content')
<div class="form-wrapper">
    <h1>パスワード再設定用メール送信</h1>

    <p>
        パスワード再設定用のURLを記載したメールを送信します。<br>
        ご登録されたメールアドレスを入力してください。
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-row">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <div class="required">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">送信する</button>
    </form>

    <form action="{{ url('/top') }}" method="GET" style="margin-top: 10px;">
        <button type="submit" class="submit-button-back">トップに戻る</button>
    </form>
</div>
@endsection
