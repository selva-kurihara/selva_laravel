@extends('admin')
@section('title', 'ログインフォーム')

@section('content')
    <div class="form-wrapper">
        <h1>管理画面</h1>

        @if(session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-row">
              <label style="min-width: 140px">ログインID</label>
              <input type="text" name="login_id" value="{{ old('login_id') }}">
            </div>
            @error('login_id')
                <div class="error">※{{ $message }}</div>
            @enderror

            <div class="form-row">
              <label style="min-width: 140px">パスワード</label>
              <input type="password" name="password">
            </div>
            @error('password')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror
            
            <button type="submit" class="submit-button">ログイン</button>
        </form>
    </div>
@endsection