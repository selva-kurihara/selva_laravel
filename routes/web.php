<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ProductController;


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

// ログイン時のみアクセス可能
Route::middleware('auth')->group(function () {
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/confirm', [ProductController::class, 'confirm'])->name('products.confirm');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::post('/products/back', [ProductController::class, 'back'])->name('products.back');
    Route::get('/subcategories/{categoryId}', [ProductController::class, 'getSubcategories']);
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::post('/products/back', [ProductController::class, 'back'])->name('products.back');
});