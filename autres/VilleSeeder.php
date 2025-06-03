<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VilleSeeder extends Seeder
{
    public function run(): void
    {
        $france_id = DB::table('pays')->where('code', 'FR')->value('id');
        $maroc_id = DB::table('pays')->where('code', 'MA')->value('id');
        $usa_id   = DB::table('pays')->where('code', 'US')->value('id');

        DB::table('villes')->insert([
            ['code' => 'PAR', 'nom' => 'Paris', 'pays_id' => $france_id],
            ['code' => 'MRK', 'nom' => 'Marrakech', 'pays_id' => $maroc_id],
            ['code' => 'NYC', 'nom' => 'New York', 'pays_id' => $usa_id],
        ]);
    }
}
