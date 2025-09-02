<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MemberRequest;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class AdministerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function membersIndex(Request $request)
    {
      $query = Member::query();

      // 検索条件
      if ($request->filled('id')) {
        $query->where('id', $request->id);
      }
      if ($request->filled('gender')) {
          // 想定値(1,2)だけに限定して安全に
          $genders = array_values(array_intersect(
              array_map('intval', (array)$request->gender),
              [1, 2]
          ));

          if (!empty($genders)) {
              $query->whereIn('gender', $genders);
          }
      }
      if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
          $q->where('name_sei', 'like', "%{$keyword}%")
            ->orWhere('name_mei', 'like', "%{$keyword}%")
            ->orWhere('email', 'like', "%{$keyword}%");
        });
      }

      // 並び替え
      $sorts =['id', 'created_at'];
      $sort = $request->get('sort', 'id');
      if (!in_array($sort, $sorts)) {
        $sort = 'id';
      }

      $direction = $request->get('direction', 'desc');
      if (!in_array($direction, ['asc', 'desc'])) {
        $direction = 'desc';
      }

      $members = $query->orderBy($sort, $direction)->paginate(10)->appends($request->query());;

      return view('administers.members.index', [
        'members' => $members,
        'direction' => $direction,
        'sort' => $sort
      ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $member = new Member();

      return view('administers.members.create', [
        'member' => $member
      ]);
    }

    public function confirm(MemberRequest $request)
    {
      $validated = $request->validated();

      // 編集時は hidden で送られてくる id を追加
      if ($request->has('id')) {
        $validated['id'] = $request->input('id');
      }

      // セッションに保存
      $request->session()->put('member_post_data', $validated);

      return view('administers.members.confirm', [
        'data' => $validated
      ]);
    }


    public function back(Request $request)
    {
      $data = $request->session()->get('member_post_data', []);
    
      if (!empty($data['id'])) {
        // 編集の場合 → editへ
        return redirect()
          ->route('admin.members.edit', $data['id'])
          ->withInput($data);
      }

      // 新規作成の場合 → createへ
      return redirect()
        ->route('admin.members.create')
        ->withInput($data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      // セッションからデータ取得
      $data = $request->session()->pull('member_post_data');

      if (!$data) {
        return redirect()->route('admin.members.index');
      }

      // メールアドレスの重複チェック
      if (Member::where('email', $data['email'])->exists()) {
        return redirect()->route('admin.members.index');
      }

      // トランザクション開始
      DB::beginTransaction();

      try {
        // データベースに保存 
        $member = Member::create($data);

        DB::commit();

        return redirect()->route('admin.members.index');
      } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();

        // UNIQUE違反など想定：一覧へ（既に登録された可能性が高い）
        // 必要ならここでエラー文言を付けてもOK
        return redirect()->route('admin.members.index');
      } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('admin.members.create')->with('error', '登録処理中にエラーが発生しました。');
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $member = Member::find($id);

      return view('administers.members.show', [
      'member' => $member
    ]);
  }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = Member::find($id);

        return view('administers.members.create', [
          'member' => $member
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, string $id)
  {
    // セッションからデータ取得
    $data = $request->session()->get('member_post_data');

    if (!$data) {
      return redirect()
        ->route('admin.members.edit', $id)
        ->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    unset($data['id']);

    // 該当の会員を取得
    $member = Member::findOrFail($id);
    
    // データ更新
    $member->fill($data);
    $member->save();

    // セッションクリア
    $request->session()->forget('member_post_data');

    return redirect()
      ->route('admin.members.index');
  }


  /**
   * Remove the specified resource from storage.
   */
    public function destroy(string $id)
    {
      $member = Member::findOrFail($id);
      $member->delete();

      return redirect()
        ->route('admin.members.index');
    }
}
