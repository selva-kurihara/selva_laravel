<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;

class AdminReviewController extends Controller
{
  /**
   * レビュー登録フォーム表示
   */
  public function create()
  {
    // 紐づく商品一覧を取得
    $products = Product::all();

    // 紐づく会員一覧を取得
    $members = Member::all();

    return view('administers.reviews.create', compact('products', 'members'));
  }

  /**
   * 確認画面表示
   */
  public function confirm(ReviewRequest $request)
  {
    $validated = $request->validated();

    // 編集時は hidden で送られてくる id を追加
    if ($request->has('id')) {
      $validated['id'] = $request->input('id');
    }

    // セッションに保存
    $request->session()->put('review_post_data', $validated);

    // 紐づく商品名と会員名を取得
    $product = Product::withCount('reviews')->withAvg('reviews', 'evaluation')->find($validated['product_id']);
    $member  = Member::find($validated['member_id']);

    // 商品写真（1枚目のみ）
    $firstImagePath = null;
    if ($product) {
        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $col) {
            $p = $product->{$col} ?? null;
            if (is_string($p) && $p !== '') {
                $firstImagePath = $p;
                break;
            }
        }
    }

    return view('administers.reviews.confirm', [
      'data'        => $validated,
      'product' => $product,
      'member'  => $member,
      'firstImagePath' => $firstImagePath,
    ]);
  }


  public function edit(string $id)
  {
    // 編集対象のレビューを取得
    $review = Review::findOrFail($id);

    // 紐づく商品一覧を取得
    $products = Product::all();

    // 紐づく会員一覧を取得
    $members = Member::all();

    return view('administers.reviews.create', compact('review', 'products', 'members'));
  }


  /**
   * 戻るボタン処理
   */
  public function back(Request $request)
  {
    $data = $request->session()->get('review_post_data', []);

    if (!empty($data['id'])) {
      return redirect()->route('admin.reviews.edit', $data['id'])->withInput($data);
    }

    return redirect()->route('admin.reviews.create')->withInput($data);
  }

  /**
   * 商品登録処理
   */
  public function store(Request $request)
  {
    $data = $request->session()->get('review_post_data');

    if (!$data) {
      return redirect()->route('admin.reviews.create')->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    $review = Review::create($data);

    // セッション破棄
    $request->session()->forget('review_post_data');

    return redirect()->route('admin.reviews.index');
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
    $data = $request->session()->pull('review_post_data');

    if (!$data) {
      return redirect()->route('admin.reviews.edit', $id)->with('error', 'セッションが切れました。もう一度入力してください。');
    }

    unset($data['id']);

    $review = Review::findOrFail($id);
    $review->update($data);
    return redirect()->route('admin.reviews.index');
  }

  /**
   * 商品詳細
   */
  public function show(string $id)
  {
    $review = Review::with(['product', 'member'])->findOrFail($id);

    $product = Product::withCount('reviews')->withAvg('reviews', 'evaluation')->find($review->product_id);
    $member = Member::find($review->member_id);

    $firstImagePath = null;
    if ($product) {
        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $col) {
            $p = $product->{$col} ?? null;
            if (is_string($p) && $p !== '') {
                $firstImagePath = $p;
                break;
            }
        }
    }

    return view('administers.reviews.show', compact('review', 'product', 'member', 'firstImagePath'));
  }


  /**
   * 削除
   */
  public function destroy(string $id)
  {
    $review = Review::findOrFail($id);
    $review->delete();

    return redirect()->route('admin.reviews.index');
  }
}
