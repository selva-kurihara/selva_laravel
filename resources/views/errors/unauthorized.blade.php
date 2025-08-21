@extends('app')

@section('title', 'アクセスエラー')

@section('content')
<div class="error-container" style="text-align:center; padding: 50px;">
    <h1 style="font-size: 2rem; color: #e3342f;">アクセスできません</h1>
    <p style="margin-top: 20px; font-size: 1.2rem;">
        このページにアクセスするにはログインが必要です。
    </p>
</div>
@endsection
