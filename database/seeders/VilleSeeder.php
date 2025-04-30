<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VilleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('villes')->insert([
            [
                "code" => "commune 6",
                'nom' => " Magmabougou",
                "pays_id"=> 1 ,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "code" => "commune 5",
                'nom' => "Rabat ",
                "pays_id"=> 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ]);
    }
}
