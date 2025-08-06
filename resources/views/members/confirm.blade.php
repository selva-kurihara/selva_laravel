@extends('app')
@section('title', '会員登録確認')

@section('content')
    <div class="form-wrapper">
        <h1>会員情報確認画面</h1>

        <div>
            @csrf
            <div class="form-row">
                <label>氏名</label>
                <div class="name-inputs">
                    {{ $data['name_sei'] }}　{{ $data['name_mei'] }}
                </div>
            </div>

            <div class="form-row">
                <label>ニックネーム</label>
                <div class="name-inputs">
                    {{ $data['nickname'] }}
                </div>
            </div>

            <div class="form-row">
                <label>性別</label>
                <div class="name-inputs">
                    {{ $data['gender'] == 1 ? '男性' : '女性' }}
                </div>
            </div>

            <div class="form-row">
                <label>パスワード</label>
                <div class="name-inputs">
                    セキュリティのため非表示
                </div>
            </div>

            <div class="form-row">
                <label>メールアドレス</label>
                <div class="name-inputs">
                    {{ $data['email'] }}
                </div>
            </div>
            <form method="POST" action="{{ route('members.store') }}">
                @csrf
                <button type="submit" class="submit-button">登録完了</button>
            </form>
            <form method="POST" action="{{ route('members.back') }}">
                @csrf
                <button type="submit" class="submit-button-back">前に戻る</button>
            </form>
        </div>
    </div>
@endsection
