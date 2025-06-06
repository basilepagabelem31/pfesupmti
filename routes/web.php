<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\StagiairesImportController;
use App\Http\Controllers\PaysController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuperviseurController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\SujetController;
use App\Http\Controllers\DemandeCoequipierController;
use App\Http\Controllers\FichierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Important pour Auth::user()

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ici, vous pouvez enregistrer les routes web pour votre application. Ces
| routes sont chargées par le RouteServiceProvider et toutes seront
| assignées au groupe de middleware "web". Créez de super choses !
|
*/

// Redirection de la page d'accueil vers la page de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route publique pour Analytics (si elle n'a pas besoin d'authentification)
Route::get('/analytics', function () {
    return view('pages.analytics');
})->name('analytics');


// --- GROUPE PRINCIPAL : Routes nécessitant une authentification (middleware 'auth') ---
Route::middleware(['auth'])->group(function () {

    // Tableau de bord commun pour tous les utilisateurs authentifiés.
    // La redirection spécifique au rôle devrait être gérée dans app/Providers/RouteServiceProvider.php (méthode HOME()).
    Route::get('/dashboard', function () {
        if (Auth::user()->isStagiaire()) {
            // Si l'utilisateur est un stagiaire, rediriger vers son dashboard spécifique.
            // Cette route 'stagiaires.dashboard' est définie plus bas dans le groupe 'role:Stagiaire'.
            return redirect()->route('stagiaires.dashboard');
        }
        // Pour les autres rôles (Admin, Superviseur) ou si aucun rôle spécifique, afficher le dashboard générique.
        return view('dashboard');
    })->middleware('verified')->name('dashboard'); // 'verified' si la vérification d'e-mail est activée


    // Gestion du profil (accessible à tous les utilisateurs authentifiés)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Routes pour la gestion des Demandes de Coéquipiers
    // Accessibles par les rôles autorisés (logique dans le contrôleur ou policies si plus complexe).
    Route::get('/demandes-coequipiers', [DemandeCoequipierController::class, 'index'])->name('demande_coequipiers.index');
    Route::get('/demandes-coequipiers/create', [DemandeCoequipierController::class, 'create'])->name('demande_coequipiers.create');
    Route::post('/demandes-coequipiers', [DemandeCoequipierController::class, 'store'])->name('demande_coequipiers.store');
    Route::post('/demandes-coequipiers/{demande_coequipier}/accept', [DemandeCoequipierController::class, 'accept'])->name('demande_coequipiers.accept');
    Route::post('/demandes-coequipiers/{demande_coequipier}/refuse', [DemandeCoequipierController::class, 'refuse'])->name('demande_coequipiers.refuse');
    Route::delete('/demandes-coequipiers/{demande_coequipier}', [DemandeCoequipierController::class, 'cancel'])->name('demande_coequipiers.cancel');


    // Routes pour la gestion des Fichiers
    // La logique de permission détaillée est gérée à l'intérieur du FichierController.
    Route::get('/fichiers', [FichierController::class, 'index'])->name('fichiers.index');
    Route::get('/fichiers/create/{stagiaire?}', [FichierController::class, 'create'])->name('fichiers.create');
    Route::post('/fichiers', [FichierController::class, 'store'])->name('fichiers.store');
    Route::get('/fichiers/{fichier}/edit', [FichierController::class, 'edit'])->name('fichiers.edit');
    Route::put('/fichiers/{fichier}', [FichierController::class, 'update'])->name('fichiers.update');
    Route::delete('/fichiers/{fichier}', [FichierController::class, 'destroy'])->name('fichiers.destroy');
    Route::get('/fichiers/{fichier}/download', [FichierController::class, 'download'])->name('fichiers.download');
    // Route pour l'index des fichiers d'un stagiaire spécifique (pour superviseurs/admins)
    Route::get('/stagiaires/{stagiaire}/fichiers', [FichierController::class, 'index'])->name('stagiaire.fichiers.index');


    // --- Groupe de routes pour Administrateurs et Superviseurs ---
    // Ces routes sont accessibles si l'utilisateur a le rôle 'Administrateur' OU 'Superviseur'.
    Route::middleware('role:Administrateur,Superviseur')->group(function () {
        // Gestion des Groupes (CRUD complet via Route::resource)
        Route::resource('groupes', GroupeController::class);

        // Gestion des Sujets (CRUD complet via Route::resource, avec actions supplémentaires)
        Route::resource('sujets', SujetController::class);
        Route::post('/sujets/{sujet}/inscrire', [SujetController::class, 'inscrire'])->name('sujets.inscrire');
        Route::delete('/sujets/{sujet}/desinscrire/{stagiaire}', [SujetController::class, 'desinscrire'])->name('sujets.desinscrire');

        // Import de stagiaires
        Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
        Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');
    });


    // --- Groupe de routes pour Administrateurs UNIQUEMENT ---
    // Ces routes ne sont accessibles que si l'utilisateur a le rôle 'Administrateur'.
    Route::middleware('role:Administrateur')->group(function () {
        Route::prefix('admin')->group(function () {
            // AdminController - Gestion des utilisateurs (Admin, Superviseurs, Stagiaires)
            Route::get('/', [AdminController::class, 'index'])->name('admin.index'); // Tableau de bord principal admin
            Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
            Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
            Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
            Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
            Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');

            // Vues spécifiques pour les listes d'utilisateurs sous l'admin (si AdminController.index peut filtrer)
            Route::get('/utilisateurs-superviseurs', [AdminController::class, 'index'])->name('admin.users.superviseurs');
            Route::get('/utilisateurs-stagiaires', [AdminController::class, 'indexStagiaire'])->name('admin.users.stagiaires');

            // CRUD pour Pays, Villes, Rôles
            Route::resource('pays', PaysController::class)->except(['show']);
            Route::resource('villes', VilleController::class)->except(['show']);
            Route::get('/villes/by-pays/{pays_id}', [VilleController::class, 'getVilles'])->name('villes.by_pays');
            Route::resource('roles', RoleController::class)->except(['show']);

            // Promotions (CRUD complet pour l'Admin, placé sous le prefix admin)
            Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
            Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
            Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
            Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
        });
    });


    // --- Groupe de routes pour Superviseurs UNIQUEMENT ---
    // Ces routes ne sont accessibles que si l'utilisateur a le rôle 'Superviseur'.
    Route::middleware('role:Superviseur')->group(function () {
        // Tableau de bord spécifique du superviseur
        Route::get('/superviseur/mon-tableau-de-bord', [SuperviseurController::class, 'index'])->name('superviseur.dashboard');
        // Autres routes spécifiques au superviseur (ex: gestion des notes des stagiaires, absences, etc.)
        // Route::get('/superviseur/notes', [NoteController::class, 'index'])->name('superviseur.notes.index');
    });

    // --- Routes spécifiques au Stagiaire UNIQUEMENT (si non couvertes par le dashboard principal) ---
    // La route 'stagiaires.dashboard' est déjà définie et protégée ci-dessus.
    // Si vous avez d'autres routes JUSTE pour les stagiaires qui ne sont pas des dashboards :
    // Route::middleware('role:Stagiaire')->group(function () {
    //     Route::get('/stagiaire/mes-notes', [StagiaireController::class, 'showNotes'])->name('stagiaire.notes');
    // });

    // --- Routes génériques de pages du template (nécessitent authentification mais pas de rôle spécifique) ---
    // J'ai regroupé les routes de pages statiques de votre template ici.
    Route::get('/email/inbox', function () { return view('pages.email-inbox'); });
    Route::get('/email/compose', function () { return view('pages.email-compose'); });
    Route::get('/email/detail', function () { return view('pages.email-detail'); });
    Route::get('/widgets', function () { return view('pages.widgets'); });
    Route::get('/pos/customer-order', function () { return view('pages.pos-customer-order'); });
    Route::get('/pos/kitchen-order', function () { return view('pages.pos-kitchen-order'); });
    Route::get('/pos/counter-checkout', function () { return view('pages.pos-counter-checkout'); });
    Route::get('/pos/table-booking', function () { return view('pages.pos-table-booking'); });
    Route::get('/pos/menu-stock', function () { return view('pages.pos-menu-stock'); });
    Route::get('/ui/bootstrap', function () { return view('pages.ui-bootstrap'); });
    Route::get('/ui/buttons', function () { return view('pages.ui-buttons'); });
    Route::get('/ui/card', function () { return view('pages.ui-card'); });
    Route::get('/ui/icons', function () { return view('pages.ui-icons'); });
    Route::get('/ui/modal-notifications', function () { return view('pages.ui-modal-notifications'); });
    Route::get('/ui/typography', function () { return view('pages.ui-typography'); });
    Route::get('/ui/tabs-accordions', function () { return view('pages.ui-tabs-accordions'); });
    Route::get('/form/elements', function () { return view('pages.form-elements'); });
    Route::get('/form/plugins', function () { return view('pages.form-plugins'); });
    Route::get('/form/wizards', function () { return view('pages.form-wizards'); });
    Route::get('/table/elements', function () { return view('pages.table-elements'); });
    Route::get('/table/plugins', function () { return view('pages.table-plugins'); });
    Route::get('/chart/chart-js', function () { return view('pages.chart-js'); });
    Route::get('/chart/chart-apex', function () { return view('pages.chart-apex'); });
    Route::get('/map', function () { return view('pages.map'); });
    Route::get('/layout/starter-page', function () { return view('pages.layout-starter-page'); });
    Route::get('/layout/fixed-footer', function () { return view('pages.layout-fixed-footer'); });
    Route::get('/layout/full-height', function () { return view('pages.layout-full-height'); });
    Route::get('/layout/full-width', function () { return view('pages.layout-full-width'); });
    Route::get('/layout/boxed-layout', function () { return view('pages.layout-boxed-layout'); });
    Route::get('/layout/minified-sidebar', function () { return view('pages.layout-minified-sidebar'); });
    Route::get('/layout/top-nav', function () { return view('pages.layout-top-nav'); });
    Route::get('/layout/mixed-nav', function () { return view('pages.layout-mixed-nav'); });
    Route::get('/layout/mixed-nav-boxed-layout', function () { return view('pages.layout-mixed-nav-boxed-layout'); });
    Route::get('/page/scrum-board', function () { return view('pages.page-scrum-board'); });
    Route::get('/page/products', function () { return view('pages.page-products'); });
    Route::get('/page/product/details', function () { return view('pages.page-product-details'); });
    Route::get('/page/orders', function () { return view('pages.page-orders'); });
    Route::get('/page/order/details', function () { return view('pages.page-order-details'); });
    Route::get('/page/gallery', function () { return view('pages.page-gallery'); });
    Route::get('/page/search-results', function () { return view('pages.page-search-results'); });
    Route::get('/page/coming-soon', function () { return view('pages.page-coming-soon'); });
    Route::get('/page/error', function () { return view('pages.page-error'); });
    Route::get('/page/login', function () { return view('pages.page-login'); });
    Route::get('/page/register', function () { return view('pages.page-register'); });
    Route::get('/page/messenger', function () { return view('pages.page-messenger'); });
    Route::get('/page/data-management', function () { return view('pages.page-data-management'); });
    Route::get('/page/file-manager', function () { return view('pages.page-file-manager'); });
    Route::get('/page/pricing', function () { return view('pages.page-pricing'); });
    Route::get('/landing', function () { return view('pages.landing'); });
    Route::get('/profile', function () { return view('pages.profile'); }); // Attention: ceci pourrait entrer en conflit avec /profile au-dessus
    Route::get('/calendar', function () { return view('pages.calendar'); });
    Route::get('/settings', function () { return view('pages.settings'); });
    Route::get('/helper', function () { return view('pages.helper'); });

}); // FIN DU GROUPE PRINCIPAL middleware('auth')


// Inclusion des routes d'authentification par défaut de Laravel Breeze/Jetstream
require __DIR__.'/auth.php';