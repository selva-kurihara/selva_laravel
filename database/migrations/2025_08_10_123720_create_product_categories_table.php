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
      if (!Schema::hasTable('product_categories')) {
        Schema::create('product_categories',function (Blueprint $table) {
          $table->id()->comment('カテゴリID');
          $table->string('name', 255)->comment('カテゴリ名');
          $table->timestamps(); // created_at, updated_at
          $table->softDeletes(); // deleted_at  
        });
      }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::dropIfExists('product_categories');
    }
};
