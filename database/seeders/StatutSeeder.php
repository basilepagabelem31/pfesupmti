<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StatutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        DB::table('statuts')->insert([
            [
                "nom" => "Active",
                "description"=>"Le statut est actif",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "nom" => "Desactive",
                "description"=>"Le statut est désactivé",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "nom" => "Archive",
                "description"=>"Le statut est archive",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
