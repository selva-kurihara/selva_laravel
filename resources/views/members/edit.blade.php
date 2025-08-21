@extends('app')

@section('title', '会員情報変更')

@section('content')
<div class="form-wrapper">
    <h1>会員情報変更</h1>

    <!-- 確認画面へ送信 -->
    <form method="POST" action="{{ route('members.confirm') }}">
        @csrf
        <div class="form-row">
            <label>氏名</label>
            <div class="name-inputs">
                <span>姓</span>
                <input type="text" name="name_sei" value="{{ old('name_sei', $member->name_sei) }}">
                <span>名</span>
                <input type="text" name="name_mei" value="{{ old('name_mei', $member->name_mei) }}">
            </div>
        </div>
        @error('name_sei')
            <div class="required">※{{ $message }}</div>
        @enderror
        @error('name_mei')
            <div class="required">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>ニックネーム</label>
            <input type="text" name="nickname" value="{{ old('nickname', $member->nickname) }}">
        </div>
        @error('nickname')
            <div class="required">※{{ $message }}</div>
        @enderror

        <div class="form-row">
            <label>性別</label>
            <div class="radio-group">
              <label>
                  <input type="radio" name="gender" value="1" 
                      @checked(old('gender', $member->gender) == 1)> 男性
              </label>
              <label>
                  <input type="radio" name="gender" value="2" 
                      @checked(old('gender', $member->gender) == 2)> 女性
              </label>
            </div>  
        </div>
        @error('gender')
            <div class="required">※{{ $message }}</div>
        @enderror
        
        <input type="hidden" name="id" value="{{ Auth::id() }}">
        <button type="submit" class="submit-button">確認画面へ</button>
    </form>

    <!-- マイページに戻るボタン -->
    <form method="GET" action="{{ route('members.mypage') }}" style="margin-top:10px;">
        <button type="submit" class="submit-button-back">マイページに戻る</button>
    </form>
</div>
@endsection
