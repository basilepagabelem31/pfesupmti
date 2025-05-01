<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaysVilleSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M'); // Augmentation mémoire

        $json = Storage::get('countries.json');
        $data = json_decode($json, true);

        foreach ($data as $country) {
            // Récupérer le code et le nom, ou les définir à null s'ils n'existent pas
            $code = isset($country['iso2']) ? $country['iso2'] : null;
            $nom = isset($country['name']) ? $country['name'] : null;

            // Si le pays n'a pas de code ou de nom, on les met à null
            if (!$code) {
                $code = null;
            }

            if (!$nom) {
                $nom = null;
            }

            // Insérer le pays dans la table
            $paysId = DB::table('pays')->insertGetId([
                'code' => $code,
                'nom' => $nom,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Vérifier si le pays contient des états et des villes
            if (isset($country['states'])) {
                foreach ($country['states'] as $state) {
                    if (isset($state['cities'])) {
                        foreach ($state['cities'] as $city) {
                            // On récupère le nom de la ville
                            $cityName = isset($city['name']) ? Str::limit($city['name'], 255, '') : null;

                            // Si la ville a un nom, on l'insère, sinon on l'ignore
                            if ($cityName) {
                                DB::table('villes')->insert([
                                    'code' => (string)$city['id'],
                                    'nom' => $cityName,
                                    'pays_id' => $paysId,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
