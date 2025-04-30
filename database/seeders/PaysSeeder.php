<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pays')->insert([
            [
                "code" => " 223",
                'nom' => " Mali",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "code" => "212",
                'nom' => "Maroc ",
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ]);
    }
}
