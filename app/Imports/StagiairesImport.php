<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
// Important: si vous voulez le numéro de ligne exact dans model() pour les échecs,
// il faudrait implémenter WithMapping et obtenir l'index ou compter manuellement les lignes.
// Pour l'instant, on utilisera $row pour les détails dans onFailure si la ligne est "sautée".
// Ou vous pouvez utiliser WithChunkReading pour obtenir le numéro de ligne.


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// IMPORTANT : Assurez-vous d'importer tous les modèles nécessaires
use App\Models\User;
use App\Models\Groupe;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Role;
use App\Models\Statut;

class StagiairesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    // Déclaration UNIQUE des propriétés
    protected $failures = [];
    protected $skippedErrors = [];
    protected $rowNumber = 0; // Pour suivre le numéro de ligne dans model()

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++; // Incrémente le numéro de ligne pour chaque appel de model()
        Log::info('Tentative d\'importation de la ligne CSV (numéro ' . $this->rowNumber . ') : ', $row);

        // Vérification des champs obligatoires du CSV
        // Si ces champs sont vides, la ligne est probablement invalide dès le départ
        if (empty($row['email']) || empty($row['cin']) || empty($row['nom']) || empty($row['prenom'])) {
            $errorMessage = 'Champs obligatoires manquants (email, cin, nom, prenom)';
            Log::warning('Ligne ' . $this->rowNumber . ' ignorée : ' . $errorMessage, $row);
            $this->onFailure(new Failure(
                $this->rowNumber,
                'general',
                [$errorMessage],
                $row
            ));
            return null;
        }

        try {
            // Création de l'instance User avec les champs directs
            $user = new User([
                'nom'               => $row['nom'] ?? null,
                'prenom'            => $row['prenom'] ?? null,
                'email'             => $row['email'] ?? null,
                'cin'               => $row['cin'] ?? null,
                'telephone'         => $row['telephone'] ?? null,
                'adresse'           => $row['adresse'] ?? null,
                'universite'        => $row['universite'] ?? null,
                'faculte'           => $row['faculte'] ?? null,
                'titre_formation'   => $row['titre_formation'] ?? null,
                'password'          => Hash::make($row['cin']), // Utilisation du CIN comme mot de passe initial
            ]);

            // --- GESTION DES CLÉS ÉTRANGÈRES ---
            // 1. Groupe
            if (!empty($row['code_groupe'])) {
                $groupe = Groupe::where('code', $row['code_groupe'])->first();
                if ($groupe) {
                    $user->groupe_id = $groupe->id;
                } else {
                    $errorMessage = 'Groupe introuvable pour le code: ' . $row['code_groupe'];
                    Log::warning('Ligne ' . $this->rowNumber . ': ' . $errorMessage . '. Ligne ignorée.', $row);
                    $this->onFailure(new Failure($this->rowNumber, 'code_groupe', [$errorMessage], $row));
                    return null;
                }
            } else {
                 // Si code_groupe est vide mais obligatoire, vous pouvez ajouter un échec
                 // $errorMessage = 'Le code groupe est obligatoire';
                 // $this->onFailure(new Failure($this->rowNumber, 'code_groupe', [$errorMessage], $row));
                 // return null;
            }


            // 2. Pays
            if (!empty($row['code_pays'])) {
                $pays = Pays::where('code', $row['code_pays'])->first();
                if ($pays) {
                    $user->pays_id = $pays->id;
                } else {
                    $errorMessage = 'Pays introuvable pour le code: ' . $row['code_pays'];
                    Log::warning('Ligne ' . $this->rowNumber . ': ' . $errorMessage . '. Ligne ignorée.', $row);
                    $this->onFailure(new Failure($this->rowNumber, 'code_pays', [$errorMessage], $row));
                    return null;
                }
            }


            // 3. Ville (dépend du Pays)
            // Assurez-vous que pays_id a été trouvé avant de chercher la ville
            if (!empty($row['code_ville']) && isset($user->pays_id)) {
                $ville = Ville::where('code', $row['code_ville'])
                                  ->where('pays_id', $user->pays_id)
                                  ->first();
                if ($ville) {
                    $user->ville_id = $ville->id;
                } else {
                    $errorMessage = 'Ville introuvable pour le code: ' . $row['code_ville'] . ' et Pays ID: ' . $user->pays_id;
                    Log::warning('Ligne ' . $this->rowNumber . ': ' . $errorMessage . '. Ligne ignorée.', $row);
                    $this->onFailure(new Failure($this->rowNumber, 'code_ville', [$errorMessage], $row));
                    return null;
                }
            } else if (!empty($row['code_ville']) && !isset($user->pays_id)) {
                // Cas où code_ville est présent mais pays_id n'a pas été trouvé (pays introuvable)
                $errorMessage = 'Ville spécifiée mais Pays non trouvé.';
                Log::warning('Ligne ' . $this->rowNumber . ': ' . $errorMessage, $row);
                $this->onFailure(new Failure($this->rowNumber, 'code_ville', [$errorMessage], $row));
                return null;
            }


            // 4. Rôle par défaut (Stagiaire)
            $roleStagiaire = Role::where('nom', 'Stagiaire')->first();
            if ($roleStagiaire) {
                $user->role_id = $roleStagiaire->id;
            } else {
                $errorMessage = 'Rôle "Stagiaire" introuvable. Veuillez exécuter vos seeders de rôles.';
                Log::error('Ligne ' . $this->rowNumber . ': ' . $errorMessage . '. Ligne ignorée.', $row);
                $this->onFailure(new Failure($this->rowNumber, 'role', [$errorMessage], $row));
                return null;
            }

            // 5. Statut par défaut (Actif)
            $statutActif = Statut::where('nom', 'Actif')->first();
            if ($statutActif) {
                $user->statut_id = $statutActif->id;
            } else {
                $errorMessage = 'Statut "Actif" introuvable. Veuillez exécuter vos seeders de statuts.';
                Log::error('Ligne ' . $this->rowNumber . ': ' . $errorMessage . '. Ligne ignorée.', $row);
                $this->onFailure(new Failure($this->rowNumber, 'statut', [$errorMessage], $row));
                return null;
            }

            Log::info('Modèle User créé avec succès (avant sauvegarde) pour ligne ' . $this->rowNumber . ' : ', $user->toArray());
            return $user; // C'est ici que le modèle est renvoyé pour être sauvegardé par Maatwebsite/Excel
        } catch (\Throwable $e) {
            Log::error('Erreur inattendue lors de la construction du modèle pour la ligne ' . $this->rowNumber . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), $row);
            $this->onError($e); // Utilisez la méthode onError de l'interface
            return null;
        }
    }


    /**
     * Pour les règles de validation
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cin'         => ['required', 'string', 'max:255', 'unique:users,cin'],
            'nom'         => ['required', 'string', 'max:255'],
            'prenom'      => ['required', 'string', 'max:255'],
            'telephone'   => ['required', 'string', 'max:255'], // Ajoutez vos validations
            'code_groupe' => ['nullable', 'string', 'exists:groupes,code'], // Vérifie si le code_groupe existe
            'code_pays'   => ['nullable', 'string', 'exists:pays,code'], // Optionnel, mais doit exister si présent
            'code_ville'  => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    // Cette validation personnalisée fonctionne mieux avec WithValidation
                    // et quand les données de la ligne sont directement accessibles.
                    // Elle peut ne pas être parfaite pour des dépendances croisées de champs multiples.
                    // Une autre approche est de laisser la logique de model() gérer l'échec de la recherche FK.
                    $data = $this->getCurrentRowData(); // Tente de récupérer les données de la ligne courante
                    $code_pays = $data['code_pays'] ?? null;

                    if (!empty($code_pays) && !empty($value)) {
                        $pays = Pays::where('code', $code_pays)->first();
                        if (!$pays || !Ville::where('code', $value)->where('pays_id', $pays->id)->exists()) {
                            $fail('La ville ' . $value . ' n\'existe pas pour le pays ' . $code_pays . '.');
                        }
                    }
                },
            ],
            // Vous pouvez commenter ces règles si vous les gérez via la logique de model() et $fillable
            // 'adresse'       => ['nullable', 'string', 'max:255'],
            // 'universite'    => ['nullable', 'string', 'max:255'],
            // 'faculte'       => ['nullable', 'string', 'max:255'],
            // 'titre_formation' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Permet d'accéder aux données de la ligne courante pour la validation personnalisée.
     * Nécessite l'implémentation de WithBatchInserts ou WithChunkReading pour une utilisation fiable.
     * Pour ToModel seul, il est plus sûr de passer les données à la fonction anonyme.
     */
    protected function getCurrentRowData()
    {
        // Ceci est une astuce qui fonctionne dans certains cas avec WithValidation,
        // mais pour une robustesse maximale, envisager WithBatchInserts/WithChunkReading
        // ou passer la ligne complète à la validation personnalisée.
        // Puisque la validation est déclenchée avant model(), request()->all() peut contenir les données du formulaire,
        // pas nécessairement la ligne du CSV.
        // Pour les validations complexes dépendantes, il est parfois plus simple de laisser la logique
        // dans model() gérer les échecs et appeler onFailure.
        // Ici, je retourne un tableau vide pour éviter une erreur si request()->all() est vide ou non pertinent.
        // La validation d'existence simple est souvent suffisante pour les FK directes.
        return []; // Revoir cette partie si la validation croisée pose problème.
    }

    /**
     * Pour gérer les erreurs (SkipsOnError)
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        Log::error('Erreur lors de l\'importation (générale) : ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
        $this->skippedErrors[] = $e->getMessage();
    }

    /**
     * Pour gérer les échecs de validation (SkipsOnFailure)
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
            Log::warning('Échec de validation ligne ' . $failure->row() . ' pour ' . $failure->attribute() . ': ' . implode(', ', $failure->errors()) . ' Valeurs: ' . json_encode($failure->values()));
        }
    }

    public function failures()
    {
        return $this->failures;
    }

    public function errors()
    {
        return $this->skippedErrors;
    }

    // Vous pouvez laisser ces méthodes si vous prévoyez de les utiliser
    // public function batchSize(): int
    // {
    //     return 1000;
    // }

    // public function chunkSize(): int
    // {
    //     return 1000;
    // }
}
