<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /**
   *  トップページ表示
   *  @return \Illuminate\Contracts\View\View
   */
  public function index()
  {
    return view('top');  // resources/views/top/index.blade.php を表示
  }

  /**
   *  ログインフォーム表示
   *  @return \Illuminate\Contracts\View\View
   */
  public function showLoginForm()
  {
    return view('members.login');
  }

  /**
   *  ログイン処理
   *  @param Request $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function login(Request $request)
  {

    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();
      return redirect()->intended('/top');
    }

    return back()->withErrors([
      'password' => 'メールアドレスまたはパスワードが間違っています。',
    ])->onlyInput('email');
  }

  /**
   *  ログアウト処理
   *  @param Request $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/top');
  }
}