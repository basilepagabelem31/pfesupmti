<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StagiairesImport;
use Illuminate\Support\Facades\Auth; // Importez Auth pour l'utilisateur connecté
use App\Models\Log; // Importez votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class StagiairesImportController extends Controller
{
    /**
     * Affiche le formulaire pour l'importation des stagiaires.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire d'importation
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_stagiaires_import_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire d'importation des stagiaires.",
            'object_snapshot' => [],
        ]);

        return view('admin.import_stagiaires');
    }

    /**
     * Gère l'importation des stagiaires à partir d'un fichier.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $fileName = $request->file('file') ? $request->file('file')->getClientOriginalName() : 'N/A';
        $fileSize = $request->file('file') ? $request->file('file')->getSize() : 'N/A';

        // Enregistrement du log : Tentative d'importation
        Log::create([
            'user_id' => $user->id,
            'action' => 'stagiaires_import_attempt',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté d'importer un fichier de stagiaires.",
            'object_snapshot' => [
                'file_name' => $fileName,
                'file_size' => $fileSize,
            ],
        ]);

        try {
            // Validation du fichier
            $request->validate([
                'file' => 'required|mimes:xlsx,csv,txt|max:10240', // 10MB
            ], [
                'file.required' => 'Veuillez sélectionner un fichier.',
                'file.mimes' => 'Le fichier doit être au format XLSX, CSV ou texte.',
                'file.max' => 'La taille du fichier ne doit pas dépasser 10MB.',
            ]);

            // Enregistrement du log : Fichier validé
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaires_import_file_validated',
                'log_message' => "Le fichier d'importation '{$fileName}' a été validé avec succès pour l'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                ],
            ]);

            $import = new StagiairesImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $errors = $import->errors(); // Assurez-vous que votre StagiairesImport gère une propriété 'errors'

            if (count($failures) > 0 || count($errors) > 0) {
                $errorMessage = "L'importation est terminée avec des avertissements :<br>";
                $logDetails = [];

                foreach ($failures as $failure) {
                    // Correction ici : Vérifier si $failure est un objet ou un tableau avant d'accéder aux propriétés/méthodes.
                    $row = is_object($failure) && method_exists($failure, 'row') ? $failure->row() : (is_array($failure) ? ($failure['row'] ?? 'N/A') : 'N/A');
                    $failureErrors = is_object($failure) && method_exists($failure, 'errors') ? $failure->errors() : (is_array($failure) ? ($failure['errors'] ?? []) : []);
                    $failureValues = is_object($failure) && method_exists($failure, 'values') ? $failure->values() : (is_array($failure) ? ($failure['values'] ?? []) : []);

                    $failureMessage = "Ligne " . $row . ": " . implode(", ", $failureErrors);
                    $errorMessage .= $failureMessage . "<br>";
                    $logDetails[] = ['type' => 'failure', 'row' => $row, 'errors' => $failureErrors, 'values' => $failureValues];
                }

                foreach ($errors as $error) {
                    $errorMessage .= "Erreur générale: " . (is_string($error) ? $error : json_encode($error)) . "<br>";
                    $logDetails[] = ['type' => 'general_error', 'message' => (is_string($error) ? $error : json_encode($error))];
                }

                // Enregistrement du log : Importation avec avertissements/erreurs de ligne
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'stagiaires_import_with_warnings',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a importé des stagiaires avec des avertissements/erreurs. Fichier: '{$fileName}'.",
                    'object_snapshot' => [
                        'file_name' => $fileName,
                        'total_failures' => count($failures),
                        'total_general_errors' => count($errors),
                        'details' => $logDetails,
                    ],
                ]);

                return redirect()->back()->with('warning', $errorMessage);
            } else {
                // Enregistrement du log : Importation réussie
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'stagiaires_import_success',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a importé les stagiaires avec succès. Fichier: '{$fileName}'.",
                    'object_snapshot' => [
                        'file_name' => $fileName,
                    ],
                ]);
                return redirect()->back()->with('success', 'Importation des stagiaires réussie !');
            }

        } catch (ValidationException $e) {
            // Ce bloc gère spécifiquement les ValidationException de Maatwebsite\Excel.
            // $e->failures() est garanti de retourner un tableau d'objets Maatwebsite\Excel\Validators\Failure.
            $failures = $e->failures(); 

            $errorMessage = "Des erreurs de validation sont survenues :<br>";
            $logDetails = [];

            foreach ($failures as $failure) {
                // Ici, $failure est un objet Maatwebsite\Excel\Validators\Failure, donc ->row() est correct.
                $failureMessage = "Ligne " . $failure->row() . ": " . implode(", ", $failure->errors());
                $errorMessage .= $failureMessage . "<br>";
                $logDetails[] = ['type' => 'validation_failure', 'row' => $failure->row(), 'errors' => $failure->errors(), 'values' => $failure->values()];
            }

            // Enregistrement du log : Échec de validation Maatwebsite\Excel
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaires_import_excel_validation_failed',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a rencontré des erreurs de validation lors de l'importation du fichier '{$fileName}'.",
                'object_snapshot' => [
                    'file_name' => $fileName,
                    'total_failures' => count($failures),
                    'details' => $logDetails,
                ],
            ]);

            return redirect()->back()->with('error', $errorMessage);

        } catch (\Throwable $e) {
            // Enregistrement du log : Erreur inattendue lors de l'importation
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaires_import_critical_error',
                'log_message' => "Erreur critique inattendue lors de l'importation des stagiaires par '{$user->nom} {$user->prenom}' (ID: {$user->id}). Fichier: '{$fileName}'. Erreur: " . $e->getMessage(),
                'object_snapshot' => [
                    'file_name' => $fileName,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            // Utilisez LaravelLog pour les erreurs système si nécessaire, en plus de votre propre modèle Log
            \Illuminate\Support\Facades\Log::error('Erreur inattendue lors de l\'importation de stagiaires : ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Une erreur inattendue est survenue lors de l\'importation. Détails : ' . $e->getMessage());
        }
    }
}
