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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name_sei', 255)->comment('氏名（姓）');
            $table->string('name_mei', 255)->comment('氏名（名）');
            $table->string('nickname', 255)->comment('ニックネーム');
            $table->integer('gender')->comment('性別（1=男性、2=女性）');
            $table->string('password', 255)->comment('パスワード');
            $table->string('email', 255)->comment('メールアドレス');
            $table->integer('auth_code')->nullable()->comment('認証コード');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
