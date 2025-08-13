@extends('app')

@section('title', 'パスワード再設定メール送信完了')

@section('content')
<div class="form-wrapper">
    <h1>パスワード再設定（メール送信完了）</h1>

    <p>
        パスワード再設定の案内メールを送信しました。<br>
        （まだパスワード再設定は完了しておりません）<br>
        届きましたメールに記載されている<br>
        『パスワード再設定URL』をクリックし、<br>
        パスワードの再設定を完了させてください。
    </p>

    <form action="{{ url('/top') }}" method="GET">
        <button type="submit" class="submit-button-back">トップに戻る</button>
    </form>
</div>
@endsection
