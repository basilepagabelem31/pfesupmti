<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StagiairesImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 
use App\helper\LogHelper; 

class StagiairesImportController extends Controller
{
    /**
     * Affiche le formulaire pour l'importation des stagiaires.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        return view('admin.import_stagiaires');
    }

    /**
     * Gère l'importation des stagiaires à partir d'un fichier.
     * Cette méthode contient la logique de traitement du fichier Excel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240', // 10MB
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.mimes' => 'Le fichier doit être au format XLSX, CSV ou texte.',
            'file.max' => 'La taille du fichier ne doit pas dépasser 10MB.',
        ]);

        $user = Auth::user(); // Récupère l'utilisateur connecté

        try {
            $import = new StagiairesImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $errors = $import->errors();

            if (count($failures) > 0 || count($errors) > 0) {
                $errorMessageForUser = "L'importation est terminée avec des avertissements :<br>";
                $logMessageDetails = [];

                foreach ($failures as $failure) {
                    $errorMessageForUser .= "Ligne " . $failure['row'] . ": " . implode(", ", $failure['errors']) . "<br>";
                    $logMessageDetails[] = "Ligne " . $failure['row'] . ": " . implode(", ", $failure['errors']);
                }

                foreach ($errors as $error) {
                    $errorMessageForUser .= "Erreur générale: " . $error . "<br>";
                    $logMessageDetails[] = "Erreur générale: " . $error;
                }

                // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LES AVERTISSEMENTS/ÉCHECS PARTIELS
                LogHelper::logAction(
                    'Importation stagiaires (avertissements)',
                    'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a importé un fichier de stagiaires. Importation terminée avec des avertissements ou des échecs partiels. Détails: ' . implode('; ', $logMessageDetails),
                    $user->id
                );

                return redirect()->back()->with('warning', $errorMessageForUser);
            } else {
                // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LE SUCCÈS COMPLET
                LogHelper::logAction(
                    'Importation stagiaires (succès)',
                    'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a importé un fichier de stagiaires avec succès.',
                    $user->id
                );
                return redirect()->back()->with('success', 'Importation des stagiaires réussie !');
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessageForUser = "Des erreurs de validation sont survenues :<br>";
            $logMessageDetails = [];

            foreach ($failures as $failure) {
                $errorMessageForUser .= "Ligne " . $failure->row() . ": " . implode(", ", $failure->errors()) . "<br>";
                $logMessageDetails[] = "Ligne " . $failure->row() . ": " . implode(", ", $failure->errors());
            }

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LES ERREURS DE VALIDATION
            LogHelper::logAction(
                'Importation stagiaires (validation échouée)',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté d\'importer un fichier de stagiaires, mais l\'opération a échoué en raison d\'erreurs de validation. Détails: ' . implode('; ', $logMessageDetails),
                $user->id
            );

            return redirect()->back()->with('error', $errorMessageForUser);

        } catch (\Throwable $e) {
            // Log l'erreur complète pour le débogage (peut être vu par un admin via le log Laravel)
            Log::error('Erreur inattendue lors de l\'importation des stagiaires par ' . $user->email . ' : ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LES ERREURS INATTENDUES
            LogHelper::logAction(
                'Importation stagiaires (erreur système)',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté d\'importer un fichier de stagiaires, mais une erreur système inattendue est survenue. Message: ' . $e->getMessage(),
                $user->id
            );

            return redirect()->back()->with('error', 'Une erreur inattendue est survenue lors de l\'importation. Détails : ' . $e->getMessage());
        }
    }
}