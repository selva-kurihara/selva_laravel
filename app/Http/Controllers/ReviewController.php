<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Review;

class ReviewController extends Controller
{
  /**
   * 商品レビュー登録フォーム表示
   */
  public function create(Product $product)
  {
    return view('reviews.create', compact('product'));
  }

  /**
   * 商品レビュー登録確認画面表示
   */
  public function confirm(ReviewRequest $request, Product $product)
  {
    // バリデーション済みデータを取得
    $validated = $request->validated();

    // 入力内容をセッションに保存
    session()->put('review_post_data', $validated);

    return view('reviews.confirm', [
      'product' => $product,
      'data'    => $validated, 
  ]);
  }

  /**
   * 戻るボタン処理
   */
  public function back(Request $request, Product $product)
  {
    $data = $request->session()->get('review_post_data', []);

    return redirect()->route('products.reviews.create', ['product' => $product->id])->withInput($data);
  }

  /**
   * 商品レビュー登録処理
   */
  public function store(Request $request, Product $product)
  {
    $data = $request->session()->get('review_post_data');

    if (!$data) {
      return redirect()->route('products.reviews.create', ['product' => $product->id])
        ->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    $memberId = Auth::id();

    \App\Models\Review::create([
      'product_id' => $product->id,
      'member_id'  => $memberId,
      'evaluation' => $data['evaluation'],
      'comment'    => $data['comment'],
    ]);

    // セッション破棄
    $request->session()->forget('review_post_data');

    // ここで完了画面を直接返す
    return view('reviews.complete', compact('product'));
  }

  public function index(Product $product)
  {

    $reviews = $product->reviews()
      ->with('member')
      ->orderByDesc('created_at')
      ->paginate(5);

    $product->loadAvg('reviews', 'evaluation'); 

    return view('reviews.index', compact('product', 'reviews'));
  }
}
