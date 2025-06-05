<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\StagiairesImportController;
use App\Http\Controllers\PaysController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuperviseurController ;
use App\Http\Controllers\GroupeController ;
<<<<<<< HEAD
use App\Http\Controllers\SujetController ; 
use App\Http\Controllers\DemandeCoequipierController ;
use App\Http\Controllers\FichierController ;
// <-- N'oubliez pas d'importer le SujetController

=======
use App\Http\Controllers\SujetController;
>>>>>>> b65e56cffa7f49c52f6d27fd87502a8ac9e42bbd
use Illuminate\Support\Facades\Route;

// Redirection de la page d'accueil vers la page de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route publique (pour le moment, /analytics n'est pas protégé par auth)
Route::get('/analytics', function () {
    return view('pages.analytics'); // Assurez-vous que la vue est 'pages/analytics'
})->name('analytics');


// --- Routes pour tous les utilisateurs authentifiés ---
// Ces routes sont protégées par le middleware 'auth'.
Route::middleware(['auth'])->group(function () {

    // Tableau de bord commun
    // La redirection spécifique au rôle se fait dans app/Providers/RouteServiceProvider.php
    // ou vous pouvez ajouter la logique ici si vous préférez.
    Route::get('/dashboard', function () {
        if (Auth::user()->isStagiaire()) {
            return redirect()->route('stagiaires.dashboard');
        }
        // Pour les autres rôles (Admin, Superviseur), vous pouvez les laisser sur le dashboard générique
        // ou les rediriger vers un dashboard spécifique si vous en créez un pour eux.
        return view('dashboard');
    })->middleware('verified')->name('dashboard'); // 'verified' est pour la vérification d'e-mail


    // Gestion du profil (accessible à tous les utilisateurs authentifiés)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

<<<<<<< HEAD
    // Route pour le tableau de bord spécifique du stagiaire
    // Protégée aussi par le middleware 'role:Stagiaire' pour plus de sécurité
    Route::get('/stagiaires/dashboard', function () {
        return view('stagiaires.dashboard');
    })->middleware('role:Stagiaire')->name('stagiaires.dashboard');


    // Routes pour la gestion des Demandes de Coéquipiers
    // Ces routes sont communes à tous les rôles qui peuvent interagir avec les demandes
    Route::get('/demandes-coequipiers', [DemandeCoequipierController::class, 'index'])->name('demande_coequipiers.index');
    Route::get('/demandes-coequipiers/create', [DemandeCoequipierController::class, 'create'])->name('demande_coequipiers.create');
    Route::post('/demandes-coequipiers', [DemandeCoequipierController::class, 'store'])->name('demande_coequipiers.store');
    Route::post('/demandes-coequipiers/{demande_coequipier}/accept', [DemandeCoequipierController::class, 'accept'])->name('demande_coequipiers.accept');
    Route::post('/demandes-coequipiers/{demande_coequipier}/refuse', [DemandeCoequipierController::class, 'refuse'])->name('demande_coequipiers.refuse');
    Route::delete('/demandes-coequipiers/{demande_coequipier}', [DemandeCoequipierController::class, 'cancel'])->name('demande_coequipiers.cancel');


    // Routes pour la gestion des Fichiers
    // La logique de permission est gérée à l'intérieur du FichierController
    Route::get('/fichiers', [FichierController::class, 'index'])->name('fichiers.index');
    Route::get('/fichiers/create/{stagiaire?}', [FichierController::class, 'create'])->name('fichiers.create');
    Route::post('/fichiers', [FichierController::class, 'store'])->name('fichiers.store');
    Route::get('/fichiers/{fichier}/edit', [FichierController::class, 'edit'])->name('fichiers.edit');
    Route::put('/fichiers/{fichier}', [FichierController::class, 'update'])->name('fichiers.update');
    Route::delete('/fichiers/{fichier}', [FichierController::class, 'destroy'])->name('fichiers.destroy');
    Route::get('/fichiers/{fichier}/download', [FichierController::class, 'download'])->name('fichiers.download');
    // Route pour l'index des fichiers d'un stagiaire spécifique (pour superviseurs/admins)
    Route::get('/stagiaires/{stagiaire}/fichiers', [FichierController::class, 'index'])->name('stagiaire.fichiers.index');


    // --- Routes pour les Administrateurs et Superviseurs ---
    // Ces routes sont protégées par le middleware 'auth' (déjà en place) et 'role' pour les rôles 'Administrateur' ou 'Superviseur'.
    Route::middleware('role:Administrateur,Superviseur')->group(function () {
        // Gestion des Groupes (CRUD complet)
        Route::resource('groupes', GroupeController::class);
=======


// Routes accessibles uniquement aux Administrateurs
Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    // AdminController
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');//affichage de admin et superviseur 
        Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');

    // Vues spécifiques pour superviseurs-Administrateur /stagiaires
        //Route::get('/Administrateur-Superviseur', [AdminController::class, 'index'])->name('admin.superviseur');
        Route::get('/Stagiaire', [AdminController::class, 'indexStagiaire'])->name('admin.stagiaire');

    // Import de stagiaires (accessible à tous ? sinon à placer dans le groupe Administrateur)
    Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
    Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');
        
    //les routes pour la promotion
        Route::get('promotions', [PromotionController::class, 'index'])->name('promotions.index');
        Route::post('promotions', [PromotionController::class, 'store'])->name('promotions.store');
        Route::put('promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
        Route::delete('promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

    //les routes pour le sujet 
        Route::resource('sujets',SujetController::class)->except(['show','create','edit']);
        Route::post('/sujets/{sujet}/inscrire',[SujetController::class,'inscrire'])->name('sujets.inscrire');
        Route::delete('/sujets/{sujet}/desinscrire/{stagiaire}', [SujetController::class, 'desinscrire'])->name('sujets.desinscrire');
});



// Route pour le superviseur
Route::middleware(['auth','role:Superviseur'])->group(function(){
    Route::get('/test-dashboard-Supervisseur',[SuperviseurController::class,'index'])->name('superviseur.dashboard');
});
// Route pour le stagiaire
Route::middleware(['auth','role:Stagiaire'])->group(function(){
    Route::get('/test-dashboard-Stagiaire',[StagiaireController::class,'index'])->name('stagiaire.dashboard');
});

//Groupe
Route::resource('groupes', GroupeController::class);
Route::post('/groupes/store', [GroupeController::class, 'store'])->name('groupes.store');
Route::put('/groupes/{id}', [GroupeController::class, 'update'])->name('groupes.update');

//route publique pour le moment 
Route::get('/analytics', function () {
	return view('/pages/analytics');
});
>>>>>>> b65e56cffa7f49c52f6d27fd87502a8ac9e42bbd

        // Gestion des Sujets (CRUD complet)
        Route::resource('sujets', SujetController::class);

        // Import de stagiaires
        Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
        Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');
    });


    // --- Routes accessibles uniquement aux Administrateurs ---
    // Ces routes sont protégées par le middleware 'auth' (déjà en place) et 'role' pour le rôle 'Administrateur'.
    Route::middleware('role:Administrateur')->group(function () {
        Route::prefix('admin')->group(function () {
            // AdminController - Routes pour la gestion des utilisateurs (Superviseurs et Stagiaires) par l'Admin
            Route::get('/', [AdminController::class, 'index'])->name('admin.index');
            Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
            Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
            Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
            Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
            Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');

            // Vues spécifiques pour superviseurs/stagiaires sous le tableau de bord de l'admin
            // J'ai renommé les routes pour éviter toute confusion avec d'autres routes 'index'
            Route::get('/utilisateurs-superviseurs', [AdminController::class, 'index'])->name('admin.users.superviseurs');
            Route::get('/utilisateurs-stagiaires', [AdminController::class, 'indexStagiaire'])->name('admin.users.stagiaires');

            // Pays (CRUD complet pour l'Admin)
            Route::resource('pays', PaysController::class)->except(['show']);

            // Villes (CRUD complet pour l'Admin)
            Route::resource('villes', VilleController::class)->except(['show']);
            Route::get('/villes/by-pays/{pays_id}', [VilleController::class, 'getVilles'])->name('villes.by_pays');

            // Rôles (CRUD complet pour l'Admin)
            Route::resource('roles', RoleController::class)->except(['show']);

            // Promotions (CRUD complet pour l'Admin)
            // J'ai ramené ces routes à l'intérieur du groupe 'admin' pour qu'elles soient protégées par 'role:Administrateur'
            Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
            Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
            Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
            Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
        });
    });

    // --- Routes spécifiques au Superviseur UNIQUEMENT ---
    // Ces routes sont protégées par le middleware 'auth' (déjà en place) et 'role' pour le rôle 'Superviseur'.
    Route::middleware('role:Superviseur')->group(function () {
        // Ajoutez ici les routes spécifiques au superviseur
        Route::get('/superviseur/dashboard', [SuperviseurController::class, 'index'])->name('superviseur.dashboard');
        // Par exemple: gestion des notes, gestion des absences, etc.
        // Route::get('/superviseur/notes', [NoteController::class, 'index'])->name('superviseur.notes.index');
    });

});

// --- Routes spécifiques au Superviseur ---
// Ces routes sont protégées par le middleware 'auth' et 'role' pour le rôle 'Superviseur'.

	
    // Routes spécifiques de l'interface utilisateur (pages statiques du template)
    Route::get('/analytics', function () {
        return view('/pages/analytics');
    });

    Route::get('/email/inbox', function () {
        return view('/pages/email-inbox');
    });

    Route::get('/email/compose', function () {
        return view('/pages/email-compose');
    });

    Route::get('/email/detail', function () {
        return view('/pages/email-detail');
    });

    Route::get('/widgets', function () {
        return view('/pages/widgets');
    });

    Route::get('/pos/customer-order', function () {
        return view('/pages/pos-customer-order');
    });

    Route::get('/pos/kitchen-order', function () {
        return view('/pages/pos-kitchen-order');
    });

    Route::get('/pos/counter-checkout', function () {
        return view('/pages/pos-counter-checkout');
    });

    Route::get('/pos/table-booking', function () {
        return view('/pages/pos-table-booking');
    });

    Route::get('/pos/menu-stock', function () {
        return view('/pages/pos-menu-stock');
    });

    Route::get('/ui/bootstrap', function () {
        return view('/pages/ui-bootstrap');
    });

    Route::get('/ui/buttons', function () {
        return view('/pages/ui-buttons');
    });

    Route::get('/ui/card', function () {
        return view('/pages/ui-card');
    });

    Route::get('/ui/icons', function () {
        return view('/pages/ui-icons');
    });

    Route::get('/ui/modal-notifications', function () {
        return view('/pages/ui-modal-notifications');
    });

    Route::get('/ui/typography', function () {
        return view('/pages/ui-typography');
    });

    Route::get('/ui/tabs-accordions', function () {
        return view('/pages/ui-tabs-accordions');
    });

    Route::get('/form/elements', function () {
        return view('/pages/form-elements');
    });

    Route::get('/form/plugins', function () {
        return view('/pages/form-plugins');
    });

    Route::get('/form/wizards', function () {
        return view('/pages/form-wizards');
    });

    Route::get('/table/elements', function () {
        return view('/pages/table-elements');
    });

    Route::get('/table/plugins', function () {
        return view('/pages/table-plugins');
    });

    Route::get('/chart/chart-js', function () {
        return view('/pages/chart-js');
    });

    Route::get('/chart/chart-apex', function () {
        return view('/pages/chart-apex');
    });

    Route::get('/map', function () {
        return view('/pages/map');
    });

    Route::get('/layout/starter-page', function () {
        return view('/pages/layout-starter-page');
    });

    Route::get('/layout/fixed-footer', function () {
        return view('/pages/layout-fixed-footer');
    });

    Route::get('/layout/full-height', function () {
        return view('/pages/layout-full-height');
    });

    Route::get('/layout/full-width', function () {
        return view('/pages/layout-full-width');
    });

    Route::get('/layout/boxed-layout', function () {
        return view('/pages/layout-boxed-layout');
    });

    Route::get('/layout/minified-sidebar', function () {
        return view('/pages/layout-minified-sidebar');
    });

    Route::get('/layout/top-nav', function () {
        return view('/pages/layout-top-nav');
    });

    Route::get('/layout/mixed-nav', function () {
        return view('/pages/layout-mixed-nav');
    });

    Route::get('/layout/mixed-nav-boxed-layout', function () {
        return view('/pages/layout-mixed-nav-boxed-layout');
    });

    Route::get('/page/scrum-board', function () {
        return view('/pages/page-scrum-board');
    });

    Route::get('/page/products', function () {
        return view('/pages/page-products');
    });

    Route::get('/page/product/details', function () {
        return view('/pages/page-product-details');
    });

    Route::get('/page/orders', function () {
        return view('/pages/page-orders');
    });

    Route::get('/page/order/details', function () {
        return view('/pages/page-order-details');
    });

    Route::get('/page/gallery', function () {
        return view('/pages/page-gallery');
    });

    Route::get('/page/search-results', function () {
        return view('/pages/page-search-results');
    });

    Route::get('/page/coming-soon', function () {
        return view('/pages/page-coming-soon');
    });

    Route::get('/page/error', function () {
        return view('/pages/page-error');
    });

    Route::get('/page/login', function () {
        return view('/pages/page-login');
    });

    Route::get('/page/register', function () {
        return view('/pages/page-register');
    });

    Route::get('/page/messenger', function () {
        return view('/pages/page-messenger');
    });

    Route::get('/page/data-management', function () {
        return view('/pages/page-data-management');
    });

    Route::get('/page/file-manager', function () {
        return view('/pages/page-file-manager');
    });

    Route::get('/page/pricing', function () {
        return view('/pages/page-pricing');
    });

    Route::get('/landing', function () {
        return view('/pages/landing');
    });

    Route::get('/profile', function () {
        return view('/pages/profile');
    });

    Route::get('/calendar', function () {
        return view('/pages/calendar');
    });

    Route::get('/settings', function () {
        return view('/pages/settings');
    });

    Route::get('/helper', function () {
        return view('/pages/helper');
    });


<<<<<<< HEAD
// Route pour le superviseur
// Route::middleware(['auth','role:Superviseur'])->group(function(){
//     Route::get('/test-dashboard-Supervisseur',[SuperviseurController::class,'index'])->name('superviseur.dashboard');
// });

// // --- Routes spécifiques au Stagiaire ---
// // Ces routes sont protégées par le middleware 'auth' et 'role' pour le rôle 'Stagiaire'.
// Route::middleware(['auth','role:Stagiaire'])->group(function(){
//     Route::get('/test-dashboard-Stagiaire',[StagiaireController::class,'index'])->name('stagiaire.dashboard');
// });




// //Groupe
// Route::resource('groupes', GroupeController::class);
// Route::post('/groupes/store', [GroupeController::class, 'store'])->name('groupes.store');
// Route::put('/groupes/{id}', [GroupeController::class, 'update'])->name('groupes.update');
=======
>>>>>>> b65e56cffa7f49c52f6d27fd87502a8ac9e42bbd


require __DIR__.'/auth.php';