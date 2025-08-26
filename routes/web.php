<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\AdministerController;
use App\Http\Controllers\Auth\AdminLoginController;

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
  Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
  Route::post('products/confirm', [ProductController::class, 'confirm'])->name('products.confirm');
  Route::post('products/back', [ProductController::class, 'back'])->name('products.back');
  Route::post('products', [ProductController::class, 'store'])->name('products.store');

  Route::get('products/{product}/reviews/create', [ReviewController::class, 'create'])->name('products.reviews.create');
  Route::post('products/{product}/reviews/store', [ReviewController::class, 'store'])->name('products.reviews.store');
  Route::post('products/{product}/reviews/confirm', [ReviewController::class, 'confirm'])->name('products.reviews.confirm');
  Route::post('products/{product}/reviews/back', [ReviewController::class, 'back'])->name('products.reviews.back');
  Route::get('products/{product}/reviews/index', [ReviewController::class, 'index'])->name('products.reviews.index');
  Route::get('products/reviews/management', [ReviewController::class, 'management'])->name('products.reviews.management');
  Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
  Route::post('products/{product}/reviews/{review}/confirm', [ReviewController::class, 'editConfirm'])->name('products.reviews.edit.confirm');
  Route::put('products/{product}/reviews/{review}', [ReviewController::class, 'update'])->name('products.reviews.update');
  Route::get('reviews/{review}/delete-confirm', [ReviewController::class, 'deleteConfirm'])->name('reviews.delete.confirm');
  Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

  Route::get('/mypage', [MemberController::class, 'mypage'])->name('members.mypage');

  Route::get('/withdraw', [MemberController::class, 'withdraw'])->name('members.withdraw');
  Route::post('/withdraw', [MemberController::class, 'withdrawProcess'])->name('members.withdraw.process');

  // パスワード変更
  Route::get('members/{member}/password/edit', [MemberController::class, 'editPassword'])->name('members.password.edit');
  Route::post('password/update', [MemberController::class, 'updatePassword'])->name('members.password.update');

  // メールアドレス変更
  Route::get('members/{member}/email/edit', [MemberController::class, 'editEmail'])->name('members.email.edit');
  Route::post('members/{member}/email/update', [MemberController::class, 'updateEmail'])->name('members.email.update');
  Route::get('members/{member}/email/verify', [MemberController::class, 'authCodeForm'])->name('members.email.auth-code');
  Route::post('members/{member}/email/verify', [MemberController::class, 'verifyEmail'])->name('members.email.verify');
});

// エラー画面
Route::view('/unauthorized', 'errors.unauthorized')->name('errors.unauthorized');

// 管理者画面用
Route::prefix('admin')->name('admin.')->group(function () {
  // ログアウト時
  Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login'])->name('login.post');
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
    });

  // ログイン時
  Route::middleware('auth:admin')->group(function () {
      Route::get('top', [AdminLoginController::class, 'top'])->name('top');
      Route::get('members', [AdministerController::class, 'membersIndex'])->name('members.index');
      Route::get('members/create', [AdministerController::class, 'create'])->name('members.create');
      Route::post('members/confirm', [AdministerController::class, 'confirm'])->name('members.confirm');
      Route::post('members/back', [AdministerController::class, 'back'])->name('members.back');
      Route::post('members/store', [AdministerController::class, 'store'])->name('members.store');
      Route::get('members/{member}/edit', [AdministerController::class, 'edit'])->name('members.edit');
      Route::put('members/{member}', [AdministerController::class, 'update'])->name('members.update');
  });
});