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
   *  商品登録フォーム表示
   *  @return \Illuminate\Contracts\View\View
   */
  public function create()
  {
    $categories = ProductCategory::all();
    return view('products.create', compact('categories'));
  }

  /**
   *  サブカテゴリ取得（AJAX）
   *  @param int $categoryId
   *  @return \Illuminate\Http\JsonResponse
   */
  public function getSubcategories($categoryId)
  {
    $subcategories = ProductSubcategory::where('product_category_id', $categoryId)->get();
    return response()->json($subcategories);
  }

  /**
   *  確認画面表示
   *  @param Request $request
   *  @return \Illuminate\Contracts\View\View
   */
  public function confirm(ProductRequest $request)
  {
    // 画像パス保持用
    $paths = [];

    for ($i = 0; $i < 4; $i++) {
        if ($request->hasFile("images.$i") && $request->file("images.$i")->isValid()) {
            // 新規アップロード → 一時保存
            $paths[$i] = $request->file("images.$i")->store('tmp/products', 'public');
        } else {
            // 既存のパスを維持（hiddenから）
            $paths[$i] = $request->input("imagePaths.$i") ?? '';
        }
    }

    // $request の中に imagePaths を入れ直す
    $data = $request->except('images');
    $data['imagePaths'] = $paths;

    // 確認画面戻り or 初回エラー時用にセッションへ保存
    $request->session()->put('product_post_data', $data);
    $request->session()->put('tmp_image_paths', $paths);

    // カテゴリ名とサブカテゴリ名を取得
    $categoryName = ProductCategory::find($data['product_category_id'])->name ?? '';
    $subcategoryName = ProductSubcategory::find($data['product_subcategory_id'])->name ?? '';

    return view('products.confirm', [
        'data'            => $data,
        'categoryName'    => $categoryName,
        'subcategoryName' => $subcategoryName,
        'imagePaths'      => $paths,
    ]);
  }

  /**
   *  戻るボタン
   *  @param Request $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function back(Request $request)
  {
    $data = $request->session()->get('product_post_data', []);
    return redirect()->route('products.create')->withInput($data);
  }

  /**
   *  商品登録
   *  @param Request $request
   *  @return \Illuminate\Http\RedirectResponse
   */
  public function store(ProductRequest $request)
  {
    // セッションから保存データを取得
    $data = $request->session()->get('product_post_data');

    if (!$data) {
      return redirect()->route('products.create')->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    // 会員ID
    $memberId = Auth::id();

    // 画像パス
    $paths = $data['imagePaths'] ?? [];
    for ($i = 0; $i < 4; $i++) {
        $paths[$i] = $paths[$i] ?? null;
    }

    // 保存処理
    DB::transaction(function () use (&$product, $paths, $data, $memberId) {
        // 画像を temp から本番ディレクトリへ移動
        $dir = 'products/' . date('Y/m/d') . '/' . Str::random(8);
        Storage::disk('public')->makeDirectory($dir);

        $final = [null, null, null, null];
        for ($i = 0; $i < 4; $i++) {
            $p = $paths[$i];
            if ($p) {
                // セキュリティ: 想定外の場所を弾く（任意）
                if (!str_starts_with($p, 'tmp/products/')) {
                    throw new \RuntimeException('invalid image path: ' . $p);
                }
                $new = $dir . '/' . basename($p);
                Storage::disk('public')->move($p, $new); // 移動（失敗時は例外に）
                $final[$i] = $new; // DB には public ディスク相対パスを保存
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

        // データベースに保存
        $product = Product::create($insertData);
    });

    // セッションクリア
    $request->session()->forget('product_post_data');
    $request->session()->forget('tmp_image_paths');

    return redirect()->route('top');
  }
}
