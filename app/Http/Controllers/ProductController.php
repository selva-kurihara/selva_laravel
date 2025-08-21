<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\ProductSubcategory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
  /**
   * 商品登録フォーム表示
   */
  public function create()
  {
    $categories = ProductCategory::all();
    return view('products.create', compact('categories'));
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

    return view('products.confirm', [
      'data'            => $data,
      'categoryName'    => $categoryName,
      'subcategoryName' => $subcategoryName,
      'imagePaths'      => $paths,
    ]);
  }

  /**
   * 戻るボタン処理
   */
  public function back(Request $request)
  {
    $data = $request->session()->get('product_post_data', []);
    return redirect()->route('products.create')->withInput($data);
  }

  /**
   * 商品登録処理
   */
  public function store(Request $request)
  {
    $data = $request->session()->get('product_post_data');

    if (!$data) {
      return redirect()->route('products.create')->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    $memberId = Auth::id();
    $paths    = $data['imagePaths'] ?? [];
    for ($i = 0; $i < 4; $i++) {
      $paths[$i] = $paths[$i] ?? null;
    }

    DB::transaction(function () use (&$product, $paths, $data, $memberId) {
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
        'member_id'               => $memberId,
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

    return redirect()->route('products.list');
  }

  /**
   * 商品一覧
   */
  public function list(Request $request)
  {
    // 検索条件取得
    $categoryId = $request->input('product_category_id');
    $subcategoryId = $request->input('product_subcategory_id');
    $freeWord = $request->input('free_word');

    // クエリ作成
    $query = Product::with(['category', 'subcategory'])->withAvg('reviews', 'evaluation');

    if (!empty($categoryId)) {
      $query->where('product_category_id', $categoryId);
    }

    if (!empty($subcategoryId)) {
      $query->where('product_subcategory_id', $subcategoryId);
    }

    if (!empty($freeWord)) {
      $query->where(function ($q) use ($freeWord) {
        $q->where('name', 'like', "%{$freeWord}%")
          ->orWhere('product_content', 'like', "%{$freeWord}%");
      });
    }

    // ページネーション
    $products = $query->orderByDesc('id')->paginate(10)->appends($request->all());

    // カテゴリ一覧
    $categories = ProductCategory::all();

    return view('products.list', compact('products', 'categories'));
  }

  /**
   * 商品詳細
   */
  public function detail(Product $product)
  {
    $product->loadAvg('reviews', 'evaluation');
    return view('products.detail', compact('product'));
  }
}
