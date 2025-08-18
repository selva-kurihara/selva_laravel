<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('members', MemberController::class);
Route::post('members/confirm', [MemberController::class, 'confirm'])->name('members.confirm');
Route::post('members/back', [MemberController::class, 'back'])->name('members.back');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/top', [LoginController::class, 'index'])->name('top');

Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetEmail'])->name('password.email');
Route::get('password/reset/{email}/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

Route::get('/products/list', [ProductController::class, 'list'])->name('products.list');
Route::get('/products/detail/{product}', [ProductController::class, 'detail'])->name('products.detail');
Route::get('/subcategories/{categoryId}', [ProductController::class, 'getSubcategories']);

// ログイン時のみアクセス可能
Route::middleware('auth')->group(function () {
  // 商品登録ページ（フォーム表示）
  Route::get('products/create', [ProductController::class, 'create'])->name('products.create');

  // 商品登録処理
  Route::post('products', [ProductController::class, 'store'])->name('products.store');

  // レビュー関連
  Route::get('products/{product}/reviews/create', [ReviewController::class, 'create'])->name('products.reviews.create');
  Route::post('products/{product}/reviews/store', [ReviewController::class, 'store'])->name('products.reviews.store');
  Route::post('products/{product}/reviews/confirm', [ReviewController::class, 'confirm'])->name('products.reviews.confirm');
  Route::post('products/{product}/reviews/back', [ReviewController::class, 'back'])->name('products.reviews.back');
  Route::get('products/{product}/reviews/index', [ReviewController::class, 'index'])->name('products.reviews.index');

  Route::get('/mypage', [MemberController::class, 'mypage'])->name('members.mypage');

  Route::get('/withdraw', [MemberController::class, 'withdraw'])->name('members.withdraw');
  Route::post('/withdraw', [MemberController::class, 'withdrawProcess'])->name('members.withdraw.process');
  });
