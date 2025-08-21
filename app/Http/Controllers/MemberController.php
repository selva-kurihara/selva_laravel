<?php

namespace App\Http\Controllers;

use App\Mail\MemberRegistered;
use App\Mail\ResetMail;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $member = Auth::user();

        // セッションからデータ取得
        $data = $request->session()->get('member_post_data');

        if (empty($member)) {

          if (!$data) {
              return redirect()->route('members.create')
                  ->with('error', 'セッションが切れました。もう一度入力してください。');
          }

          // データベースに保存
          $member = Member::create($data);

          // メール送信
          Mail::to($member->email)->send(new MemberRegistered($member));

          // セッションクリア
          $request->session()->forget('member_post_data');
        
          return view('members.store');
        }else{

          if (!$data) {
            return redirect()->route('members.edit')
              ->with('error', 'セッションが切れました。もう一度入力してください。');
          }

          // データベースに保存
          $member->fill($data);
          $member->save();

          // セッションクリア
          $request->session()->forget('member_post_data');

          return view('members.mypage', compact('member'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $member = Member::findOrFail($id);
      return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      // パラメータはログインユーザーと同じかチェックしても安全
      $member = Member::findOrFail($id);

      // 万一ログインユーザーじゃない id が入ってきたら弾く
      if ($member->id !== Auth::id()) {
        abort(403, '不正なアクセスです');
      }
        $member = Member::findOrFail($id);
        return view('members.edit', compact('member'));
    }

    /**
     * 登録・編集確認
     */
    public function confirm(MemberRequest $request)
    {
        $member = Auth::user(); // 編集対象

        $validated = $request->validated();

        // セッションに保存
        $request->session()->put('member_post_data', $validated);

        if (!empty($member)) {
          return view('members.edit-confirm', ['data' => $validated]);
        }else{
          return view('members.confirm', ['data' => $validated]);
        }
    }

    /**
     * 登録・編集戻り
     */
    public function back(Request $request)
    {
        $member = Auth::user();
        // セッションのデータを取得してフォームに戻る
        $data = $request->session()->get('member_post_data', []);
      
        if (empty($member)) {
        return redirect()->route('members.create')->withInput($data);
      }else{
        return redirect()->route('members.edit', ['member' => Auth::id()])
        ->withInput($data);
      }
    }
  
    /**
     * マイページ
     */
    public function mypage()
    {
      $member = Auth::user(); // ログインユーザー情報を取得

      if (!$member) {
        return redirect()->route('login'); // ログインしていなければログインページへ
      }

      return view('members.mypage', compact('member'));
    }

  /**
   * 編集確認用
   */
  public function editConfirm(MemberRequest $request, Member $member)
  {
    $validated = $request->validated();
    $request->session()->put('member_post_data', $validated);
    $viewData = collect($validated)->except(['password', 'password_confirmation'])->toArray();
    return view('members.confirm', ['data' => $viewData, 'member' => $member]);
  }

  /**
   * 退会ページ表示
   */
  public function withdraw()
  {
    $member = Auth::user();
    return view('members.withdraw', compact('member'));
  }

  /**
   * 退会処理（ソフトデリート）
   */
  public function withdrawProcess(Request $request)
  {
    $member = Auth::user();

    // ソフトデリート
    $member->delete();

    // ログアウト
    Auth::logout();

    // トップ画面へリダイレクト
    return redirect()->route('top');
  }

  /**
   * パスワード変更フォーム
   */
  public function editPassword(string $id)
  {
    $member = Member::findOrFail($id);
    return view('members.password-edit', compact('member'));
  }

  /**
   * パスワード更新
   */
  public function updatePassword(MemberRequest $request)
  {
    $member = Auth::user();

    $data = $request->validated();

    if (!empty($data['password'])) {
      // hashed キャストがあるので自動でハッシュ化される
      $member->password = $data['password'];
      $member->save();
    }

    return redirect()->route('members.mypage');
  }


  /**
   * メールアドレス変更フォーム
   */
  public function editEmail(string $id)
  {
    $member = Member::findOrFail($id);
    return view('members.email-edit', compact('member'));
  }

  /**
   * メールアドレス更新
   */
  public function updateEmail(MemberRequest $request)
  {
    $member = Auth::user();

    $data = $request->validated();

    // 認証コードを生成（6桁の数字）
    $authCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // DBに保存（メールはまだ更新しない）
    $member->auth_code = $authCode;
    $member->save();

    // メール送信
    Mail::to($data['email'])->send(new ResetMail($member, $authCode));

    // セッションに「更新予定の新しいメールアドレス」を保持
    $request->session()->put('pending_new_email', $data['email']);

    return redirect()->route('members.email.auth-code', $member->id);
  }

  /**
   * 認証コード入力時
   */
  public function authCodeForm()
  {
    $member = Auth::user();
    return view('members.auth-code-form', compact('member'));
  }

  /**
   * メール認証完了画面
   */
  public function verifyEmail(MemberRequest $request)
  {
    $member = Auth::user();

    // セッションに保存した新しいメールアドレスを取得
    $newEmail = $request->session()->get('pending_new_email');

    if (!$newEmail) {
      return redirect()->route('members.mypage')->withErrors(['email' => '新しいメールアドレスが見つかりません']);
    }

    // 認証成功 → メールアドレス更新
    $member->email = $newEmail;
    $member->auth_code = null;
    $member->save();

    // セッションから削除
    $request->session()->forget('pending_new_email');

    return redirect()->route('members.mypage');
  }
}
