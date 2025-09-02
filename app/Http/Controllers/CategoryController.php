<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function categoriesIndex(Request $request)
  {
    $query = ProductCategory::query();

    // 検索条件
    if ($request->filled('id')) {
      $query->where('id', $request->id);
    }

    if ($request->filled('keyword')) {
      $keyword = $request->keyword;

      $query->where(function ($q) use ($keyword) {
        $q->where('name', 'like', "%{$keyword}%")
          // サブカテゴリ名
          ->orWhereHas('subCategories', function ($q3) use ($keyword) {
            $q3->where('name', 'like', "%{$keyword}%");
          });
      });
    }

    // 並び替え
    $sorts = ['id', 'created_at'];
    $sort = $request->get('sort', 'id');
    if (!in_array($sort, $sorts)) {
      $sort = 'id';
    }

    $direction = $request->get('direction', 'desc');
    if (!in_array($direction, ['asc', 'desc'])) {
      $direction = 'desc';
    }

    $categories = $query->orderBy($sort, $direction)->paginate(10)->appends($request->query());;

    return view('administers.categories.index', [
      'categories' => $categories,
      'direction' => $direction,
      'sort' => $sort
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $category = new ProductCategory();

    return view('administers.categories.create', [
      'category' => $category
    ]);
  }

  public function confirm(ProductCategoryRequest $request)
  {
    $validated = $request->validated();

    // 編集時は hidden で送られてくる id を追加
    if ($request->has('id')) {
      $validated['id'] = $request->input('id');
    }

    // セッションに保存
    $request->session()->put('category_post_data', $validated);

    return view('administers.categories.confirm', [
      'data' => $validated
    ]);
  }


  public function back(Request $request)
  {
    $data = $request->session()->get('category_post_data', []);

    if (!empty($data['id'])) {
      // 編集の場合 → editへ
      return redirect()
        ->route('admin.categories.edit', $data['id'])
        ->withInput($data);
    }

    // 新規作成の場合 → createへ
    return redirect()
      ->route('admin.categories.create')
      ->withInput($data);
  }


  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // セッションからデータ取得
    $data = $request->session()->get('category_post_data');

    if (!$data) {
      return redirect()->route('admin.categories.create')->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    // トランザクション開始
    DB::beginTransaction();

    try {
      // 商品大カテゴリ登録
      $category = ProductCategory::create([
        'name' => $data['name'],
      ]);

      // 商品小カテゴリ登録（空は除外）
      if (!empty($data['subcategories'])) {
        foreach ($data['subcategories'] as $subcategoryName) {
          if (!empty($subcategoryName)) {
            $category->subcategories()->create([
              'name' => $subcategoryName,
            ]);
          }
        }
      }

      DB::commit();

      // セッションクリア
      $request->session()->forget('category_post_data');

      return redirect()->route('admin.categories.index')
        ->with('success', '商品カテゴリを登録しました。');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->route('admin.categories.create')
        ->with('error', '登録処理中にエラーが発生しました。');
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    $category = ProductCategory::find($id);

    return view('administers.categories.show', [
      'category' => $category
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    $category = ProductCategory::find($id);

    // サブカテゴリを取得（子テーブルを hasMany で関連付け済みと仮定）
    $subcategories = $category->subCategories ?? collect();

    return view('administers.categories.create', [
      'category' => $category,
      'subcategories' => $subcategories
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    // セッションからデータ取得
    $data = $request->session()->get('category_post_data');

    if (!$data) {
      return redirect()
        ->route('admin.categories.edit', $id)
        ->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    unset($data['id']);

    // 該当のカテゴリを取得
    $category = ProductCategory::findOrFail($id);

    // データ更新
    $category->fill($data);
    $category->save();

    // サブカテゴリ更新
    $subcategories = $data['subcategories'] ?? [];

    // 既存のサブカテゴリをすべて物理削除
    $category->subCategories()->delete();

    // 新しいサブカテゴリを登録
    foreach ($subcategories as $name) {
      $name = trim($name);
      if ($name !== '') {
        $category->subCategories()->create(['name' => $name]);
      }
    }
    // セッションクリア
    $request->session()->forget('category_post_data');

    return redirect()
      ->route('admin.categories.index');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $category = ProductCategory::with('subCategories')->findOrFail($id);

    // サブカテゴリもソフトデリート
    foreach ($category->subCategories as $sub) {
      $sub->delete();
    }

    // 親カテゴリをソフトデリート
    $category->delete();

    return redirect()->route('admin.categories.index');
  }
}
