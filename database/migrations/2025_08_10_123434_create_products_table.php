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
      Schema::create('products', function (Blueprint $table) {
        $table->id()->comment('スレッドID');
        $table->unsignedBigInteger('member_id')->comment('会員ID');
        $table->unsignedBigInteger('product_category_id')->comment('カテゴリID');
        $table->unsignedBigInteger('product_subcategory_id')->comment('サブカテゴリID');
        $table->string('name', 255)->comment('商品名');
        $table->string('image_1', 255)->nullable()->comment('写真１');
        $table->string('image_2', 255)->nullable()->comment('写真２');
        $table->string('image_3', 255)->nullable()->comment('写真３');
        $table->string('image_4', 255)->nullable()->comment('写真４');
        $table->text('product_content')->comment('商品説明');
        $table->timestamps(); // created_at, updated_at
        $table->softDeletes(); // deleted_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
