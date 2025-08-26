<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('reviews', function (Blueprint $table) {
      $table->id(); // id
      $table->unsignedBigInteger('member_id'); // 会員ID
      $table->unsignedBigInteger('product_id'); // 商品ID
      $table->integer('evaluation'); // 評価
      $table->text('comment'); // コメント
      $table->timestamps(); // created_at, updated_at
      $table->softDeletes(); // deleted_at
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reviews');
  }
};
