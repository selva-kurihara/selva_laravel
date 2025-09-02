@extends('admin')
@section('title', 'ログイン')

@section('header')        
  <div class="header-container"> 
    <div class="header-left">管理者メインメニュー</div>
     <div class="header-right">
       <span>ようこそ {{ Auth::guard('admin')->user()->name }} さん</span> 
      </div>

      @guest @else
      <form action="{{ route('admin.logout') }}" method="POST" class="inline-form"> 
        @csrf 
        <button type="submit">ログアウト</button> 
      </form> 
      @endguest
     </div>
@endsection
      
@section('content')
  <div class="form-wrapper"> 
    <form method="GET" action="{{ route('admin.members.index') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button">会員一覧</button>
    </form>
  </div>
  <div class="form-wrapper"> 
    <form method="GET" action="{{ route('admin.categories.index') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button">商品カテゴリ一覧</button>
    </form>
  </div>
  <div class="form-wrapper"> 
    <form method="GET" action="{{ route('admin.products.index') }}" style="margin-top:10px;">
      <button type="submit" class="submit-button">商品一覧</button>
    </form>
  </div>
@endsection