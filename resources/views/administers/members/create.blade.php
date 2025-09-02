@extends('admin')
@section('title', isset($member->id) ? '会員情報編集' : '会員情報登録')

@section('header')
    <div class="header-container">
        <div class="header-left">{{ isset($member->id) ? '会員編集' : '会員登録' }}</div>
        <div class="header-right">
            <form action="{{ route('admin.members.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="form-wrapper">
        <form method="POST" action="{{ route('admin.members.confirm') }}">
            @csrf 

            @if(isset($member->id)) 
            <input type="hidden" name="id" value="{{ $member->id }}"> 
            @endif

            <div class="form-row">
                <label>ID</label>
                <div class="name-inputs">
                    @if(isset($member->id)) 
                        {{ $member->id }}
                    @else
                        登録後に自動採番
                    @endif
                </div>
            </div>

            <div class="form-row">
                <label>氏名</label>
                <div class="name-inputs">
                    <span>姓</span>
                    <input type="text" name="name_sei" value="{{ old('name_sei') ?? $member->name_sei }}">
                    <span>名</span>
                    <input type="text" name="name_mei" value="{{ old('name_mei') ?? $member->name_mei }}">
                </div>
            </div>
            @error('name_sei')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror
            @error('name_mei')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>ニックネーム</label>
                <input type="text" name="nickname" value="{{ old('nickname') ?? $member->nickname }}">
            </div>
            @error('nickname')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>性別</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="1" @checked(old('gender') ?? $member->gender == 1)>
                        男性
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2" @checked(old('gender') ?? $member->gender == 2)>
                        女性
                    </label>
                </div>
            </div>
            @error('gender')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>パスワード</label>
                <input type="password" name="password">
            </div>
            @error('password')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>パスワード確認</label>
                <input type="password" name="password_confirmation">
            </div>
            @error('password_confirmation')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <div class="form-row">
                <label>メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') ?? $member->email }}">
            </div>
            @error('email')
                <div class="error">
                    ※{{ $message }}
                </div>
            @enderror

            <input type="hidden" name="id" value="{{$member->id}}">
            <button type="submit" class="submit-button">確認画面へ</button>
        </form>
    </div>
@endsection
