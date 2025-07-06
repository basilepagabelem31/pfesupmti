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
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\FichierController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReunionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


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

// Route publique pour Analytics (accessible sans authentification si besoin)
Route::get('/analytics', function () {
    return view('pages.analytics');
})->name('analytics');


// --- GROUPE PRINCIPAL : Routes nécessitant une authentification (middleware 'auth') ---
Route::middleware(['auth'])->group(function () {

    // Tableau de bord commun pour tous les utilisateurs authentifiés.
    Route::get('/dashboard', function () {
        if (Auth::user()->isStagiaire()) {
            return redirect()->route('stagiaire.dashboard');
        } elseif (Auth::user()->isSuperviseur()) {
            return redirect()->route('superviseur.dashboard');
        } elseif (Auth::user()->isAdministrateur()) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->middleware('verified')->name('dashboard');


    // Gestion du profil (accessible à tous les utilisateurs authentifiés)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Routes pour la gestion des Demandes de Coéquipiers
    Route::get('/demandes-coequipiers', [DemandeCoequipierController::class, 'index'])->name('demande_coequipiers.index');
    Route::get('/demandes-coequipiers/create', [DemandeCoequipierController::class, 'create'])->name('demande_coequipiers.create');
    Route::post('/demandes-coequipiers', [DemandeCoequipierController::class, 'store'])->name('demande_coequipiers.store');
    Route::post('/demandes-coequipiers/{demande_coequipier}/accept', [DemandeCoequipierController::class, 'accept'])->name('demande_coequipiers.accept');
    Route::post('/demandes-coequipiers/{demande_coequipier}/refuse', [DemandeCoequipierController::class, 'refuse'])->name('demande_coequipiers.refuse');
    Route::delete('/demandes-coequipiers/{demande_coequipier}', [DemandeCoequipierController::class, 'cancel'])->name('demande_coequipiers.cancel');


    // Routes pour la gestion des Fichiers (logique de permission gérée dans le contrôleur)
    Route::get('/fichiers', [FichierController::class, 'index'])->name('fichiers.index');
    Route::get('/fichiers/create/{stagiaire?}', [FichierController::class, 'create'])->name('fichiers.create');
    Route::post('/fichiers', [FichierController::class, 'store'])->name('fichiers.store');
    Route::get('/fichiers/{fichier}/edit', [FichierController::class, 'edit'])->name('fichiers.edit');
    Route::put('/fichiers/{fichier}', [FichierController::class, 'update'])->name('fichiers.update');
    Route::delete('/fichiers/{fichier}', [FichierController::class, 'destroy'])->name('fichiers.destroy');
    Route::get('/fichiers/{fichier}/download', [FichierController::class, 'download'])->name('fichiers.download');
    Route::get('/stagiaires/{stagiaire}/fichiers', [FichierController::class, 'index'])->name('stagiaire.fichiers.index');


    // --- ROUTE SPÉCIFIQUE POUR LES VILLES PAR PAYS (AJAX) ---
    // Cette route est placée ici pour qu'elle soit sous le middleware 'auth'
    // mais PAS sous un préfixe 'admin' ou 'superviseur' qui ne correspondrait pas au JS.
    Route::get('/villes/by-pays/{pays_id}', [VilleController::class, 'getVilles'])->name('villes.by_pays');


    // --- Groupe de routes pour Administrateurs et Superviseurs ---
    // Ces routes sont accessibles par les deux rôles.
    Route::middleware('role:Administrateur,Superviseur')->group(function () {



          Route::prefix('admin')->group(function () {
             Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
            Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
          
        // ...
        Route::get('/edit/{user}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/update/{user}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/delete/{user}', [AdminController::class, 'delete'])->name('admin.delete');
         Route::get('/stagiaires/{user}', [AdminController::class, 'showStagiaireDetails'])->name('admin.stagiaires.show');

    });
        // Promotions (déplacées ici pour être accessibles aux deux rôles)
        Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
        Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
        Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
        Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');

        // Groupes
        Route::resource('groupes', GroupeController::class);
        Route::get('/groupes/{id}/stagiaires', [GroupeController::class, 'stagiairesActifs'])->name('groupes.stagiairesActifs');

        // Sujets
        Route::resource('sujets', SujetController::class);
        Route::post('/sujets/{sujet}/inscrire', [SujetController::class, 'inscrire'])->name('sujets.inscrire');
        Route::delete('/sujets/{sujet}/desinscrire/{stagiaire}', [SujetController::class, 'desinscrire'])->name('sujets.desinscrire');

        // Import de stagiaires
        Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
        Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');

        Route::get('/utilisateurs-stagiaires', [AdminController::class, 'indexStagiaire'])->name('admin.users.stagiaires');
        Route::get('/sujets/{sujet}/stagiaires-for-enrollment', [SujetController::class, 'getStagiairesForEnrollment']);
    });


    // --- Groupe de routes pour Administrateurs UNIQUEMENT ---
    Route::middleware('role:Administrateur')->group(function () {
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Préfixe 'admin' pour les routes spécifiques au panneau d'administration
        Route::prefix('admin')->group(function () {
            // AdminController - Gestion des utilisateurs, etc.
            Route::get('/lister', [AdminController::class, 'index'])->name('admin.index');
          
            Route::get('/utilisateurs-superviseurs', [AdminController::class, 'index'])->name('admin.users.superviseurs');
            Route::get('/email-logs', [EmailLogController::class, 'index'])->name('admin.email_logs.index');
            // CRUD pour Pays, Villes (gestion complète, pas seulement AJAX), Rôles
            Route::resource('pays', PaysController::class)->except(['show']);
            Route::resource('villes', VilleController::class)->except(['show', 'getVilles']);
            Route::resource('roles', RoleController::class)->except(['show']);
        });
    });


    // --- Groupe de routes pour Superviseurs UNIQUEMENT ---
    Route::middleware('role:Superviseur')->group(function () {
        Route::get('/superviseur/dashboard', [SuperviseurController::class, 'index'])->name('superviseur.dashboard');
        // Route pour la liste des stagiaires gérés par le superviseur, réutilisant AdminController::indexStagiaire
    Route::get('/superviseur/stagiaires', [AdminController::class, 'indexStagiaire'])->name('superviseur.stagiaires.index');

        // Les routes promotions, groupes, sujets, et stagiaires/import ont été déplacées
        // dans le groupe 'Administrateur,Superviseur' plus haut pour éviter la duplication.
        // NE PAS LES DÉFINIR ICI À NOUVEAU.
    });


    // --- Groupe de routes pour Stagiaires UNIQUEMENT ---
    Route::middleware('role:Stagiaire')->group(function () {
        Route::get('/stagiaires/dashboard', function () {
            return view('stagiaires.dashboard');
        })->name('stagiaire.dashboard');
    });

    //route pour la gestion des notes 
    Route::middleware(['auth', 'role:Administrateur,Superviseur'])->group(function () {
    Route::get('/stagiaires-notes', [NoteController::class, 'listeStagiaires'])->name('notes.liste_stagiaires');
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{id}/edit', [NoteController::class, 'edit'])->name('notes.edit');
    Route::put('/notes/{id}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');



    Route::get('/reunions', [ReunionController::class, 'index'])->name('reunions.index');
    Route::post('/reunions', [ReunionController::class, 'store'])->name('reunions.store');
    Route::get('/reunions/{id}', [ReunionController::class, 'show'])->name('reunions.show');
    Route::post('/reunions/{id}/cloture', [ReunionController::class, 'cloturer'])->name('reunions.cloture');
    Route::post('/reunions/{reunionId}/presence/{stagiaireId}', [ReunionController::class, 'updatePresence'])->name('reunions.updatePresence');


    });
    //suite pour la gestion des notes 
Route::middleware(['auth'])->group(function () {
    Route::get('/stagiaire/{id}', [NoteController::class, 'ficheStagiaire'])->name('notes.fiche_stagiaire');
    
    // Route pour marquer une notification spécifique comme lue
    Route::post('/notifications/{id}/marque-lu', function($id) {
        $notification = Auth::user()->unreadNotifications()->findOrFail($id);
        $stagiaire_id = $notification->data['stagiaire_id']; // Assurez-vous que 'stagiaire_id' est dans les data de la notification
        $notification->markAsRead();
        return redirect()->route('notes.fiche_stagiaire', $stagiaire_id)->with('success', 'Notification marquée comme lue.');
    })->name('notifications.markAsRead');

    // NOUVELLE ROUTE : Marquer toutes les notifications comme lues
    Route::post('/notifications/mark-all-read', function() {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    })->name('notifications.markAllAsRead');
});

    //gestion des absences et reunions gere pour le superviseur 
   Route::middleware(['auth','role:Superviseur'])->group(function(){
    // Route::get('/reunions', [ReunionController::class, 'index'])->name('reunions.index');
    // Route::post('/reunions', [ReunionController::class, 'store'])->name('reunions.store');
    // Route::get('/reunions/{id}', [ReunionController::class, 'show'])->name('reunions.show');
    // Route::post('/reunions/{id}/cloture', [ReunionController::class, 'cloturer'])->name('reunions.cloture');
    // Route::post('/reunions/{reunionId}/presence/{stagiaireId}', [ReunionController::class, 'updatePresence'])->name('reunions.updatePresence');
 
});
    //gestion des groupes 


     //Gestion des emails en relation avec la reunion 
    Route::middleware(['auth','role:Administrateur'])->name('admin.')->group(function(){
        Route::get('/admin/email-settings', [EmailSettingsController::class, 'index'])->name('email-settings.index');
        Route::post('/admin/email-settings/update', [EmailSettingsController::class, 'update'])->name('email-settings.update');

        Route::get('/admin/email-templates', [EmailTemplateController::class, 'index'])->name('email_templates.index');
        Route::post('/admin/email-templates', [EmailTemplateController::class, 'store'])->name('email_templates.store');
        Route::put('/admin/email-templates/{id}', [EmailTemplateController::class, 'update'])->name('email_templates.update');

        Route::get('/admin/logs', [AdminController::class, 'showLogs'])->name('logs.index');
    });

    


    // --- Pages statiques du template (nécessitent authentification mais pas de rôle spécifique) ---
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
    //Route::get('/page/login', function () { return view('pages.page-login'); });
    Route::get('/page/register', function () { return view('pages.page-register'); });
    Route::get('/page/messenger', function () { return view('pages.page-messenger'); });
    Route::get('/page/data-management', function () { return view('pages.page-data-management'); });
    Route::get('/page/file-manager', function () { return view('pages.page-file-manager'); });
    Route::get('/page/pricing', function () { return view('pages.page-pricing'); });
    Route::get('/landing', function () { return view('pages.landing'); });
    // Route::get('/profile', function () { return view('pages.profile'); });
    Route::get('/calendar', function () { return view('pages.calendar'); });
    Route::get('/settings', function () { return view('pages.settings'); });
    Route::get('/helper', function () { return view('pages.helper'); });

}); // FIN DU GROUPE PRINCIPAL middleware('auth')


Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile/{id}', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    
});

Route::middleware(['auth', 'role:Superviseur'])->group(function () {
    Route::get('/superviseur/profile', [SuperviseurController::class, 'profile'])->name('superviseur.profile');
    Route::put('/superviseur/profile/{id}', [SuperviseurController::class, 'updateProfile'])->name('superviseur.profile.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/stagiaires/profiles', [StagiaireController::class, 'profiles'])->name('stagiaires.profiles');
    Route::put('/stagiaires/profiles/{id}', [StagiaireController::class, 'update'])->name('stagiaires.profiles.update');
});


// Inclusion des routes d'authentification par défaut de Laravel Breeze/Jetstream
require __DIR__.'/auth.php';
