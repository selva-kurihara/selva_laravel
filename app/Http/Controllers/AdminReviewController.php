<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Support\Str;

class AdminReviewController extends Controller
{
  /**
   * レビュー登録フォーム表示
   */
  public function create()
  {
    $reviews = Review::all();
    return view('administers.reviews.create', compact('reviews'));
  }

  /**
   * サブカテゴリ取得（AJAX）
   */
  public function getSubcategories($categoryId)
  {
    $subcategories = ProductSubcategory::where('product_category_id', $categoryId)->get();
    return response()->json($subcategories);
  }

  /**
   * 確認画面表示
   */
  public function confirm(ProductRequest $request)
  {
    $paths = [];

    for ($i = 0; $i < 4; $i++) {
      if ($request->hasFile("images.$i") && $request->file("images.$i")->isValid()) {
        $paths[$i] = $request->file("images.$i")->store('tmp/products', 'public');
      } else {
        $paths[$i] = $request->input("imagePaths.$i") ?? '';
      }
    }

    $data = $request->except('images');
    $data['imagePaths'] = $paths;

    $request->session()->put('product_post_data', $data);
    $request->session()->put('tmp_image_paths', $paths);

    $categoryName    = ProductCategory::find($data['product_category_id'])->name ?? '';
    $subcategoryName = ProductSubcategory::find($data['product_subcategory_id'])->name ?? '';

    return view('administers.products.confirm', [
      'data'            => $data,
      'categoryName'    => $categoryName,
      'subcategoryName' => $subcategoryName,
      'imagePaths'      => $paths,
    ]);
  }

  public function edit(string $id)
  {
    // 商品を取得（カテゴリも一緒に取得）
    $product = Product::with('category.subCategories')->findOrFail($id);

    // 商品のカテゴリ
    $category = $product->category;

    // サブカテゴリ（カテゴリが存在しなければ空コレクション）
    $subcategories = $category->subCategories ?? collect();

    // 全カテゴリ一覧（編集画面で選択肢に使う場合）
    $categories = ProductCategory::all();

    $initialImagePaths = [];
    for ($i = 1; $i <= 4; $i++) {
        $col = 'image_' . $i;
        $initialImagePaths[$i - 1] = $product->$col ?: '';
    }

    return view('administers.products.create', [
      'category' => $category,
      'subcategories' => $subcategories,
      'categories' => $categories, // これを追加
      'product' => $product,
      'initialImagePaths' => $initialImagePaths,
    ]);
  }

  /**
   * 戻るボタン処理
   */
  public function back(Request $request)
  {
    $data = $request->session()->get('product_post_data', []);

    if (!empty($data['id'])) {
      return redirect()->route('admin.products.edit', $data['id'])->withInput($data);
    }

    return redirect()->route('admin.products.create')->withInput($data);
  }

  /**
   * 商品登録処理
   */
  public function store(Request $request)
  {
    $data = $request->session()->get('product_post_data');

    if (!$data) {
      return redirect()->route('admin.products.create')->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    $paths    = $data['imagePaths'] ?? [];
    for ($i = 0; $i < 4; $i++) {
      $paths[$i] = $paths[$i] ?? null;
    }

    DB::transaction(function () use (&$product, $paths, $data) {
      $dir = 'products/' . date('Y/m/d') . '/' . Str::random(8);
      Storage::disk('public')->makeDirectory($dir);

      $final = [null, null, null, null];
      for ($i = 0; $i < 4; $i++) {
        $p = $paths[$i];
        if ($p) {
          if (!str_starts_with($p, 'tmp/products/')) {
            throw new \RuntimeException('invalid image path: ' . $p);
          }
          $new = $dir . '/' . basename($p);
          Storage::disk('public')->move($p, $new);
          $final[$i] = $new;
        }
      }

      $insertData = [
        'member_id'               => "0",
        'product_category_id'     => $data['product_category_id'],
        'product_subcategory_id'  => $data['product_subcategory_id'],
        'name'                    => $data['name'],
        'image_1'                 => $final[0],
        'image_2'                 => $final[1],
        'image_3'                 => $final[2],
        'image_4'                 => $final[3],
        'product_content'         => $data['product_content'],
      ];

      $product = Product::create($insertData);
    });

    $request->session()->forget('product_post_data');
    $request->session()->forget('tmp_image_paths');

    return redirect()->route('admin.products.index');
  }

  /**
   * レビュー一覧
   */
  public function index(Request $request)
  {
    $query = Review::query();

    // 検索条件
    if ($request->filled('id')) {
      $query->where('id', $request->id);
    }

    if ($request->filled('keyword')) {
      $keyword = $request->keyword;

      $query->where(function ($q) use ($keyword) {
        $q->where('comment', 'like', "%{$keyword}%");
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

    $reviews = $query->orderBy($sort, $direction)->paginate(10)->appends($request->query());;


    return view('administers.reviews.index', compact('reviews'));
  }

  /**
   * 商品更新
   */
  public function update(Request $request, string $id)
  {
    $data = $request->session()->pull('product_post_data');

    if (!$data) {
      return redirect()->route('admin.products.edit', $id)->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    unset($data['id']);

    $product = Product::findOrFail($id);
    $product->update($data);
    return redirect()->route('admin.products.index');
  }

  /**
   * 商品詳細
   */
  public function show(string $id)
  {
    $product = Product::with([
        'category.subCategories',
        'member:id,name_sei,name_mei,nickname',
    ])->findOrFail($id);

    // カテゴリ名／サブカテゴリ名（null安全）
    $categoryName    = optional($product->category)->name ?? '';
    $subcategoryName = '';
    if ($product->product_subcategory_id && $product->category) {
        $match = $product->category->subCategories
            ->firstWhere('id', $product->product_subcategory_id);
        $subcategoryName = $match->name ?? '';
    }

    // 画像パス配列（image_1〜4）
    $imagePaths = [];
    for ($i = 1; $i <= 4; $i++) {
        $col = 'image_' . $i;
        if (!empty($product->$col)) {
            $imagePaths[] = Storage::url($product->$col);
        }
    }

    // レビュー集計
    $reviewQuery = $product->reviews(); 
    $reviewCount = $reviewQuery->count();
    $reviewAvg   = $reviewCount ? round($reviewQuery->avg('evaluation'), 1) : null;
    $reviews = $product->reviews()
        ->with('member:id,name_sei,name_mei,nickname')
        ->orderByDesc('id')
        ->paginate(3);

    $member = $product->member;

    return view('administers.products.show', compact(
        'product', 'categoryName', 'subcategoryName', 'imagePaths', 'reviewCount', 'reviewAvg', 'member', 'reviews'
    ));
  }

  /**
   * 商品削除
   */
  public function destroy(string $id)
  {
    $product = Product::with('reviews')->findOrFail($id);

    foreach ($product->reviews as $review) {
      $review->delete();
    }
    $product->delete();

    return redirect()->route('admin.products.index');
  }
}
