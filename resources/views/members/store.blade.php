@extends('app')
@section('title', '会員情報登録完了')

@section('content')
    <div class="form-wrapper">
        <h1>会員情報登録完了</h1>
        <p>会員情報の登録が完了しました。</p>
        <form action="{{ url('/top') }}" method="GET">
          <button type="submit" class="submit-button-back">トップに戻る</button>
        </form>
    </div>
@endsection
