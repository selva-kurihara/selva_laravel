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

class AdminProductController extends Controller
{
  /**
   * 商品登録フォーム表示
   */
  public function create()
  {
    $categories = ProductCategory::all();
    return view('administers.products.create', compact('categories'));
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

    return view('administers.products.create', [
      'category' => $category,
      'subcategories' => $subcategories,
      'categories' => $categories, // これを追加
      'product' => $product,
    ]);
  }




  /**
   * 戻るボタン処理
   */
  public function back(Request $request)
  {
    $data = $request->session()->get('product_post_data', []);
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
   * 商品一覧
   */
  public function list(Request $request)
  {
    $query = Product::query();

    // 検索条件
    if ($request->filled('id')) {
      $query->where('id', $request->id);
    }

    if ($request->filled('keyword')) {
      $keyword = $request->keyword;

      $query->where(function ($q) use ($keyword) {
        $q->where('name', 'like', "%{$keyword}%")
          ->orWhere('product_content', 'like', "%{$keyword}%");
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

    $products = $query->orderBy($sort, $direction)->paginate(10)->appends($request->query());;


    return view('administers.products.index', compact('products'));
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
