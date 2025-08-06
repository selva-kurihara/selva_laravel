<?php

namespace App\Http\Controllers;

use App\Mail\MemberRegistered;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        // セッションからデータ取得
        $data = $request->session()->get('member_post_data');

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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * 登録・編集確認
     */
    public function confirm(MemberRequest $request)
    {
        $validated = $request->validated();

        // セッションに保存
        $request->session()->put('member_post_data', $validated);

        // ビューに渡すデータからパスワードを除外
        $viewData = collect($validated)->except(['password', 'password_confirmation'])->toArray();

        return view('members.confirm', ['data' => $viewData]);
    }

    /**
     * 登録・編集戻り
     */
    public function back(Request $request)
    {
        // セッションのデータを取得してフォームに戻る
        $data = $request->session()->get('member_post_data', []);

        return redirect()->route('members.create')->withInput($data);
    }
}
