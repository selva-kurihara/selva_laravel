<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MemberRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{
  /**
   *  パスワード再設定メール送信フォーム表示
   *  @return \Illuminate\Contracts\View\View
   */
  public function showRequestForm()
  {
    return view('auth.passwords.email');
  }

  /**
   *  パスワード再設定メール送信処理
   *  @param MemberRequest $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function sendResetEmail(MemberRequest $request)
  {
    // メールアドレスで会員を検索
    $member = Member::where('email', $request->email)->first();

    // 7桁ランダム（1000000〜19999999 は21億未満）
    $token = mt_rand(1000000, 199999999);

    // トークンをDBに保存
    $member->auth_code = $token;
    $member->save();

    // パスワードリセットメール送信
    Mail::to($member->email)->send(new PasswordResetMail($member, $token));

    // メール送信完了画面へ遷移
    return view('auth.passwords.email_sent');
  }

  /**
   *  パスワード再設定フォーム表示
   *  @param string $token
   *  @return \Illuminate\Contracts\View\View
   */
  public function showResetForm($email, $token)
  {
    return view('auth.passwords.reset', ['email' => $email, 'token' => $token]);
  }

  /**
   *  パスワードリセット処理
   *  @param MemberRequest $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function reset(MemberRequest $request)
  {
    $user = Member::where('email', $request->email)->first();
    if (!$user || (string)$user->auth_code !== (string)$request->token) {
        return back()->withErrors(['email' => 'トークンが無効です。'])->withInput();
    }

    $user->password = $request->password;
    $user->auth_code = null;
    $user->save();

    // ログイン
    Auth::login($user);

    return redirect()->route('top')->with('status', 'パスワードを更新しました。');
  }
}
