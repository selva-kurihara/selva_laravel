@extends('app')
@section('title', 'ログイン')

@section('content')
    <div class="form-wrapper">
        <h1>ログイン</h1>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-row">
              <label>メールアドレス（ID）</label>
              <input type="email" name="email" value="{{ old('email') }}">
            </div>
            
            <div class="form-row">
              <label>パスワード</label>
              <input type="password" name="password">
            </div>
            @error('password')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror
            <p>
              <a href="{{ route('password.request') }}">パスワードを忘れた方はこちら</a>
            </p>
            <button type="submit" class="submit-button">ログイン</button>
        </form>
        <form action="{{ url('/top') }}" method="GET">
          <button type="submit" class="submit-button-back">トップに戻る</button>
        </form>
    </div>
@endsection
