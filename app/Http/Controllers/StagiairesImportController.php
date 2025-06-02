<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StagiairesImport;
use Illuminate\Support\Facades\Log; // Assurez-vous d'avoir ceci si vous l'utilisez

class StagiairesImportController extends Controller
{
    /**
     * Affiche le formulaire pour l'importation des stagiaires.
     * C'est cette méthode qui était manquante.
     *
     * @return \Illuminate\View\View
     */
    public function showImportForm()
    {
        // Assurez-vous que cette vue existe bien.
        // Par exemple, si votre fichier est resources/views/stagiaires/import_form.blade.php
        // Alors le nom de la vue sera 'stagiaires.import_form'
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
        // Votre logique de validation du fichier (déjà présente)
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240', // 10MB
        ], [
            'file.required' => 'Veuillez sélectionner un fichier.',
            'file.mimes' => 'Le fichier doit être au format XLSX, CSV ou texte.',
            'file.max' => 'La taille du fichier ne doit pas dépasser 10MB.',
        ]);

        try {
            $import = new StagiairesImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            $errors = $import->errors();

            if (count($failures) > 0 || count($errors) > 0) {
                $errorMessage = "L'importation est terminée avec des avertissements :<br>";

                foreach ($failures as $failure) {
                    $errorMessage .= "Ligne " . $failure['row'] . ": " . implode(", ", $failure['errors']) . "<br>";
                }

                foreach ($errors as $error) {
                    $errorMessage .= "Erreur générale: " . $error . "<br>";
                }
                return redirect()->back()->with('warning', $errorMessage);
            } else {
                return redirect()->back()->with('success', 'Importation des stagiaires réussie !');
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = "Des erreurs de validation sont survenues :<br>";
            foreach ($failures as $failure) {
                $errorMessage .= "Ligne " . $failure->row() . ": " . implode(", ", $failure->errors()) . "<br>";
            }
            return redirect()->back()->with('error', $errorMessage);

        } catch (\Throwable $e) {
            Log::error('Erreur inattendue lors de l\'importation : ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Une erreur inattendue est survenue lors de l\'importation. Détails : ' . $e->getMessage());
        }
    }
}