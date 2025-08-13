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
      Schema::create('product_subcategories', function (Blueprint $table) {
        $table->increments('id')->comment('サブカテゴリID');
        $table->unsignedInteger('product_category_id')->comment('カテゴリID');
        $table->string('name', 255)->comment('サブカテゴリ名');
        $table->timestamps();
        $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subcategories');
    }
};
