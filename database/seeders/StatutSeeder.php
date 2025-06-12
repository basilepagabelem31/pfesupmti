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
        DB::table('statuts')->insert([
            [
                'nom' => 'Actif',
                'description' => 'Le stagiaire est actif et peut recevoir des notifications.'
            ],
            [
                'nom' => 'Abandonné',
                'description' => 'Le stagiaire a abandonné la formation.'
            ],
            [
                'nom' => 'Archivé',
                'description' => 'Le stagiaire est archivé et inactif.'
            ],
            [
                'nom' => 'Terminé',
                'description' => 'Le stagiaire a terminé la formation.'
            ]
        ]);
    }
}