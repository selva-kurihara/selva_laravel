<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdministerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::table('administers')->insertOrIgnore([
          'name' => '栗原愛真音',          
          'login_id' => 'kuri1234',         
          'password' => Hash::make('kuri1111'), 
      ]);  
    }
}
