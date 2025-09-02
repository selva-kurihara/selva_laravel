@extends('admin')
@section('title', '会員登録確認')

@section('header')
    <div class="header-container">
        <div class="header-left">会員登録確認</div>
        <div class="header-right">
            <form action="{{ route('admin.members.index') }}" method="GET" class="inline-form">
                <button type="submit">一覧へ戻る</button>
            </form>
        </div>
    </div>
@endsection

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
                <input type="hidden" name="email" value="{{ $data['email'] }}">
            </div>
            {{-- 登録／更新ボタン --}}
            @if(isset($data['id']))
            <form method="POST" action="{{ route('admin.members.update', $data['id']) }}">
                @csrf
                @method('PUT')
            @else
            <form method="POST" action="{{ route('admin.members.store') }}">
                @csrf
            @endif

            <button type="submit" class="submit-button">
                {{ isset($data['id']) ? '更新する' : '登録完了' }}
            </button>
            </form>
        </div>
        
        <form method="POST" action="{{ route('admin.members.back') }}">
            @csrf
            <button type="submit" class="submit-button-back">前に戻る</button>
        </form>
    </div>
@endsection
