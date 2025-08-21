<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomAuthenticate extends Middleware
{
   /**
     * 未ログイン時のリダイレクト先URLを返す
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // ここで「products.create だけ」オリジナルエラーへ
            if ($request->routeIs('products.create')) {
                return route('errors.unauthorized'); // ↓で作るルート名
            }
            // それ以外は従来どおりログインへ
            return route('login');
        }
    }
}
