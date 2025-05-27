<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaysSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pays')->insert([
            ['code' => 'FR', 'nom' => 'France'],
            ['code' => 'MA', 'nom' => 'Maroc'],
            ['code' => 'US', 'nom' => 'États-Unis'],
        ]);
    }
}
