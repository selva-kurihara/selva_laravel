@extends('app')

@section('title', 'メールアドレス変更')

@section('content')
  <div class="form-wrapper">
    <h1>メールアドレス変更</h1>
    <form method="POST" action="{{ route('members.email.update', $member->id) }}">
        @csrf

        <div class="form-row">
            <label>現在のメールアドレス</label>
            <div class="current-email">{{$member->email}}</div>
        </div>

        <div class="form-row">
            <label>変更後のメールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        @error('email')
            <div class="required">※{{ $message }}</div>
        @enderror

        <button type="submit" class="submit-button">認証メール送信</button>
    </form>

    <!-- マイページに戻るボタン -->
    <form method="GET" action="{{ route('members.mypage') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button-back">マイページに戻る</button>
    </form>
  </div>
@endsection
