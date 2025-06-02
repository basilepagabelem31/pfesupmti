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
use Illuminate\Support\Facades\Route;

// Redirection de la page d'accueil vers la page de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Tableau de bord
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Gestion du profil (authentifié)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Import de stagiaires (accessible à tous ? sinon à placer dans le groupe Administrateur)
Route::get('/stagiaires/import', [StagiairesImportController::class, 'showImportForm'])->name('stagiaires.import.form');
Route::post('/stagiaires/import', [StagiairesImportController::class, 'import'])->name('stagiaires.import');

// Routes accessibles uniquement aux Administrateurs
Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    // AdminController
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
        Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');

        // Vues spécifiques pour superviseurs/stagiaires
        Route::get('/Administrateur-Superviseur', [AdminController::class, 'index'])->name('admin.superviseur');
        Route::get('/Stagiaire', [AdminController::class, 'indexStagiaire'])->name('admin.stagiaire');

        // Pays
        Route::resource('pays', PaysController::class)->except(['show']);

        // Villes
        Route::resource('villes', VilleController::class)->except(['show']);
        Route::get('/villes/by-pays/{pays_id}', [VilleController::class, 'getVilles'])->name('villes.by_pays');

        // Rôles
        Route::resource('roles', RoleController::class)->except(['show']);
    });
});














	
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








// Route pour le superviseur
Route::middleware(['auth','role:Superviseur'])->group(function(){
    Route::get('/test-dashboard-Supervisseur',[SuperviseurController::class,'index'])->name('superviseur.dashboard');
});
// Route pour le stagiaire
Route::middleware(['auth','role:Stagiaire'])->group(function(){
    Route::get('/test-dashboard-Stagiaire',[StagiaireConrtoller::class,'index'])->name('stagiaire.dashboard');
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

//Groupe
Route::resource('groupes', GroupeController::class);
Route::post('/groupes/store', [GroupeController::class, 'store'])->name('groupes.store');
Route::put('/groupes/{id}', [GroupeController::class, 'update'])->name('groupes.update');




require __DIR__.'/auth.php';