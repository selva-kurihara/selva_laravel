@extends('app')

@section('title', 'パスワード再設定')

@section('content')
    <div class="form-wrapper">
        <h1>パスワード再設定</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-row">
                <label style="min-width: 170px">新しいパスワード</label>
                <input type="password" name="password">
            </div>
            @error('password')
                <div class="required">※{{ $message }}</div>
            @enderror

            <div class="form-row">
                <label style="min-width: 170px">新しいパスワード（確認）</label>
                <input type="password" name="password_confirmation">
            </div>
            @error('password_confirmation')
                <div class="required">※{{ $message }}</div>
            @enderror
            @error('email')
                <div class="required">※{{ $message }}</div>
            @enderror

            <button type="submit" class="submit-button">パスワードリセット</button>
        </form>

        <form action="{{ url('/top') }}" method="GET" style="margin-top: 1rem;">
            <button type="submit" class="submit-button-back">トップに戻る</button>
        </form>
    </div>
@endsection
