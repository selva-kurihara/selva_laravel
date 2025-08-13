@extends('app')
@section('title', '会員情報登録')

@section('content')
    <div class="form-wrapper">
        <h1>会員情報登録</h1>

        <form method="POST" action="{{ route('members.confirm') }}">
            @csrf
            <div class="form-row">
                <label>氏名</label>
                <div class="name-inputs">
                    <span>姓</span>
                    <input type="text" name="name_sei" value="{{ old('name_sei') }}">
                    <span>名</span>
                    <input type="text" name="name_mei" value="{{ old('name_mei') }}">
                </div>
            </div>
            @error('name_sei')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror
            @error('name_mei')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>ニックネーム</label>
                <input type="text" name="nickname" value="{{ old('nickname') }}">
            </div>
            @error('nickname')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>性別</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="1" checked>
                        男性
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2" @checked(old('gender') == 2)>
                        女性
                    </label>
                </div>
            </div>
            @error('gender')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>パスワード</label>
                <input type="password" name="password">
            </div>
            @error('password')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>パスワード確認</label>
                <input type="password" name="password_confirmation">
            </div>
            @error('password_confirmation')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            @error('email')
                <div class="required">
                    ※{{ $message }}
                </div>
            @enderror

            <button type="submit" class="submit-button">確認画面へ</button>
        </form>
        <form action="{{ url('/top') }}" method="GET">
          <button type="submit" class="submit-button-back">トップに戻る</button>
        </form>
    </div>
@endsection
