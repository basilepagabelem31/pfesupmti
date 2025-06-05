<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //implementer de 2 groupes pour pouvoir implmenter la fonctionnalite de sujet 
        DB::table('groupes')->insert([
            [
                'code' => 'G1',
                'nom' => 'Groupe Alpha',
                'description' => 'Premier groupe Test',
                'jour' => Carbon::now()->toDateString(),
                'heure_debut' =>'09:00:00',
                'heure_fin' => '11:00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                'code' => 'G2',
                'nom' => 'Groupe Bêta',
                'description' => 'Second groupe de test',
                'jour' => Carbon::now()->addDay()->toDateString(),
                'heure_debut' => '14:00:00',
                'heure_fin' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
