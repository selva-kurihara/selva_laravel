<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Models\Product;
use App\Models\Member;
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
    if (!auth()->check()) {
      return redirect()->route('error.unauthorized');
    }
    $product->loadAvg('reviews', 'evaluation');
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

    $product->loadAvg('reviews', 'evaluation');
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

  public function management()
  {
    $member = Auth::user();

    $reviews = $member->reviews()
      ->with(['product.category', 'product.subcategory'])
      ->orderByDesc('created_at')
      ->paginate(5);

    return view('reviews.management', compact('member', 'reviews'));
  }

  public function edit($id)
  {
    $review = Review::findOrFail($id);
    $product = $review->product; // リレーション経由で商品を取得

    return view('reviews.create', compact('review', 'product'));
  }

  public function editConfirm(ReviewRequest $request, $productId, $reviewId)
  {
    $review = Review::findOrFail($reviewId);

    $data = $request->validated();
    // セッションに保存（確認画面 → update で利用する）
    $request->session()->put('review_edit_data', $data);

    return view('reviews.confirm', [
      'product' => $review->product,
      'review'  => $review,
      'data'   => $data,
    ]);
  }

  public function update(Request $request, $productId, $reviewId)
  {
    $review = Review::findOrFail($reviewId);


    // editConfirm() でセッションに保存した値を取り出す
    $data = $request->session()->get('review_edit_data');

    if (!$data) {
      // セッションが切れている場合は編集画面に戻す
      return redirect()->route('reviews.edit', ['review' => $reviewId])
        ->with('error', 'セッションが切れました。再度入力してください。');
    }

    // 更新
    $review->update($data);

    // セッションから不要になったデータを削除
    $request->session()->forget('review_edit_data');

    // 完了後にリダイレクト（商品詳細へ）
    return redirect()->route('products.reviews.management', ['product' => $productId]);
  }

  public function deleteConfirm($reviewId)
  {
    $review = Review::findOrFail($reviewId);
    $product = $review->product;

    $data = [
      'evaluation' => $review->evaluation,
      'comment' => $review->comment,
    ];

    return view('reviews.confirm', compact('review', 'product', 'data'))
      ->with('isDelete', true); // 削除画面フラグ
  }

  public function destroy($reviewId)
  {
    $review = Review::findOrFail($reviewId);
    $productId = $review->product_id;

    $review->delete();

    return redirect()->route('products.reviews.management', ['product' => $productId]);
  }
}
