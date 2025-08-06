<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('members', MemberController::class);
Route::post('members/confirm', [MemberController::class, 'confirm'])->name('members.confirm');
Route::post('members/back', [MemberController::class, 'back'])->name('members.back');