<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Administrateur

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/index',[AdminController::class,'create'])->name('admin.create');
Route::post('/admin/index',[AdminController::class,'store'])->name('admin.store');
Route::get('/admin/edit/{id}',[AdminController::class,'edit'])->name('admin.edit');
Route::put('/admin/update/{id}',[AdminController::class,'update'])->name('admin.update');
Route::delete('/admin/delete/{id}',[AdminController::class,'delete'])->name('admin.delete');







use App\Http\Controllers\PaysController;

// Lister tous les pays
Route::get('admin/pays', [PaysController::class, 'index'])->name('pays.index');

// Afficher le formulaire pour ajouter un pays
Route::get('admin/pays/create', [PaysController::class, 'create'])->name('pays.create');

// Enregistrer un nouveau pays
Route::post('admin/pays', [PaysController::class, 'store'])->name('pays.store');

// Afficher le formulaire pour modifier un pays
Route::get('admin/pays/{pays}/edit', [PaysController::class, 'edit'])->name('pays.edit');

// Mettre à jour un pays
Route::put('admin/pays/{pays}', [PaysController::class, 'update'])->name('pays.update');

// Supprimer un pays
Route::delete('admin/pays/{pays}', [PaysController::class, 'destroy'])->name('pays.destroy');


use App\Http\Controllers\VilleController;

// Lister toutes les villes
Route::get('admin/villes', [VilleController::class, 'index'])->name('villes.index');

// Afficher le formulaire pour ajouter une ville
Route::get('admin/villes/create', [VilleController::class, 'create'])->name('villes.create');

// Enregistrer une nouvelle ville
Route::post('admin/villes', [VilleController::class, 'store'])->name('villes.store');

// Afficher le formulaire pour modifier une ville
Route::get('admin/villes/{ville}/edit', [VilleController::class, 'edit'])->name('villes.edit');

// Mettre à jour une ville
Route::put('admin/villes/{ville}', [VilleController::class, 'update'])->name('villes.update');

// Supprimer une ville
Route::delete('admin/villes/{ville}', [VilleController::class, 'destroy'])->name('villes.destroy');










Route::get('/', function () {
	return view('/pages/index');
});

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
