<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('product_categories')->insert([
        ['id' => 1, 'name' => 'インテリア', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 2, 'name' => '家電', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 3, 'name' => 'ファッション', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 4, 'name' => '美容', 'created_at' => now(), 'updated_at' => now()],
        ['id' => 5, 'name' => '本・雑誌', 'created_at' => now(), 'updated_at' => now()],
      ]);
  }
}
