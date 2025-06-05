<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StagiairesImportController;
use App\Http\Controllers\PaysController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuperviseurController ;
use App\Http\Controllers\StagiaireConrtoller ;
use App\Http\Controllers\GroupeController ;
use App\Http\Controllers\SujetController ; 
use App\Http\Controllers\DemandeCoequipierController ;
use App\Http\Controllers\FichierController ;
// <-- N'oubliez pas d'importer le SujetController

use Illuminate\Support\Facades\Route;

// Redirection de la page d'accueil vers la page de login
Route::get('/', function () {
    return redirect()->route('login');
});




// Assurez-vous que ces routes sont protégées par un middleware d'authentification si nécessaire
Route::middleware(['auth'])->group(function () {
    // Page d'index des demandes de coéquipiers (envoyées et reçues)
    Route::get('/demandes-coequipiers', [DemandeCoequipierController::class, 'index'])->name('demande_coequipiers.index');

    // Afficher le formulaire pour envoyer une demande
    Route::get('/demandes-coequipiers/create', [DemandeCoequipierController::class, 'create'])->name('demande_coequipiers.create');

    // Traiter l'envoi d'une nouvelle demande
    Route::post('/demandes-coequipiers', [DemandeCoequipierController::class, 'store'])->name('demande_coequipiers.store');

    // Accepter une demande (méthode POST pour action)
    Route::post('/demandes-coequipiers/{demande_coequipier}/accept', [DemandeCoequipierController::class, 'accept'])->name('demande_coequipiers.accept');

    // Refuser une demande (méthode POST pour action)
    Route::post('/demandes-coequipiers/{demande_coequipier}/refuse', [DemandeCoequipierController::class, 'refuse'])->name('demande_coequipiers.refuse');

    // Annuler une demande (par le demandeur)
    Route::delete('/demandes-coequipiers/{demande_coequipier}', [DemandeCoequipierController::class, 'cancel'])->name('demande_coequipiers.cancel');




        // Routes pour la gestion des Fichiers
    Route::get('/fichiers', [FichierController::class, 'index'])->name('fichiers.index');
    Route::get('/fichiers/create/{stagiaire?}', [FichierController::class, 'create'])->name('fichiers.create');
    Route::post('/fichiers', [FichierController::class, 'store'])->name('fichiers.store');
    Route::get('/fichiers/{fichier}/edit', [FichierController::class, 'edit'])->name('fichiers.edit');
    Route::put('/fichiers/{fichier}', [FichierController::class, 'update'])->name('fichiers.update');
    Route::delete('/fichiers/{fichier}', [FichierController::class, 'destroy'])->name('fichiers.destroy');
    Route::get('/fichiers/{fichier}/download', [FichierController::class, 'download'])->name('fichiers.download');

    // Route pour l'index des fichiers d'un stagiaire spécifique (pour superviseurs/admins)
    Route::get('/stagiaires/{stagiaire}/fichiers', [FichierController::class, 'index'])->name('stagiaire.fichiers.index');
});

// Tableau de bord commun pour tous les utilisateurs authentifiés
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Gestion du profil (accessible à tous les utilisateurs authentifiés)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Routes pour les Administrateurs et Superviseurs ---
// Ces routes sont protégées par le middleware 'auth' et 'role' pour les rôles 'Administrateur' ou 'Superviseur'.
Route::middleware(['auth', 'role:Administrateur,Superviseur'])->group(function () {
    // Gestion des Groupes (CRUD complet)
    Route::resource('groupes', GroupeController::class);

    // Gestion des Sujets (CRUD complet) <-- NOUVELLE ROUTE ICI
    Route::resource('sujets', SujetController::class);

    // Import de stagiaires (si les superviseurs peuvent aussi importer)
    Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
    Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');
});

// --- Routes accessibles uniquement aux Administrateurs ---
// Ces routes sont protégées par le middleware 'auth' et 'role' pour le rôle 'Administrateur'.
Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::prefix('admin')->group(function () {
        // AdminController - Routes pour la gestion des utilisateurs (Superviseurs et Stagiaires) par l'Admin
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');

        // Vues spécifiques pour superviseurs/stagiaires sous le tableau de bord de l'admin
        Route::get('/Administrateur-Superviseur', [AdminController::class, 'index'])->name('admin.superviseur');
        Route::get('/Stagiaire', [AdminController::class, 'indexStagiaire'])->name('admin.stagiaire');

        // Pays (CRUD complet pour l'Admin)
        Route::resource('pays', PaysController::class)->except(['show']);

        // Villes (CRUD complet pour l'Admin)
        Route::resource('villes', VilleController::class)->except(['show']);
        Route::get('/villes/by-pays/{pays_id}', [VilleController::class, 'getVilles'])->name('villes.by_pays');

        // Rôles (CRUD complet pour l'Admin)
        Route::resource('roles', RoleController::class)->except(['show']);
    });
});

// --- Routes spécifiques au Superviseur ---
// Ces routes sont protégées par le middleware 'auth' et 'role' pour le rôle 'Superviseur'.
Route::middleware(['auth','role:Superviseur'])->group(function(){
    Route::get('/test-dashboard-Supervisseur',[SuperviseurController::class,'index'])->name('superviseur.dashboard');
});

// --- Routes spécifiques au Stagiaire ---
// Ces routes sont protégées par le middleware 'auth' et 'role' pour le rôle 'Stagiaire'.
Route::middleware(['auth','role:Stagiaire'])->group(function(){
    Route::get('/test-dashboard-Stagiaire',[StagiaireConrtoller::class,'index'])->name('stagiaire.dashboard');
});

require __DIR__.'/auth.php';