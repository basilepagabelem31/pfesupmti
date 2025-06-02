<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Pays;
use App\Models\Ville;

class PaysVilleSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $json = Storage::get('countries.json');
        $data = json_decode($json, true);

        foreach ($data as $country) {
            $code = isset($country['iso2']) ? $country['iso2'] : null;
            $nom = isset($country['name']) ? $country['name'] : null;

            // Utilise firstOrCreate pour les Pays en se basant sur 'code'
            // Si 'code' est null et non unique dans la DB, cela pourrait poser problème.
            // Assurez-vous que la colonne 'code' de la table 'pays' est UNIQUE ou gérez les nulls spécifiquement.
            $pays = Pays::firstOrCreate(
                ['code' => $code],
                ['nom' => $nom]
            );

            $paysId = $pays->id;

            if (isset($country['states'])) {
                foreach ($country['states'] as $state) {
                    if (isset($state['cities'])) {
                        foreach ($state['cities'] as $city) {
                            $cityName = isset($city['name']) ? Str::limit($city['name'], 255, '') : null;
                            $cityCode = isset($city['id']) ? (string)$city['id'] : null;

                            if ($cityName) { // On s'assure qu'il y a un nom pour la ville
                                // *** CHANGEMENT CLÉ ICI ***
                                // On utilise ['nom', 'pays_id'] pour la recherche car c'est la contrainte qui est violée.
                                // Si une ville avec ce nom et ce pays_id existe déjà, elle sera retournée.
                                // Sinon, une nouvelle sera créée avec ces attributs + le code.
                                Ville::firstOrCreate(
                                    [
                                        'nom' => $cityName,
                                        'pays_id' => $paysId
                                    ],
                                    [
                                        'code' => $cityCode // 'code' sera utilisé pour la création si non trouvé
                                    ]
                                );
                            }
                        }
                    }
                }
            }
        }
    }
}