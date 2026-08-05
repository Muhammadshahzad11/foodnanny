<?php

use App\Http\Controllers\Frontend\ManifestController;
use App\Http\Controllers\Frontend\OfflineController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Installer\InstallerController;
use App\Http\Controllers\RootController;
use Illuminate\Support\Facades\Route;


Route::prefix('install')->name('installer.')->middleware(['web'])->group(function () {
    Route::get('/', [InstallerController::class, 'index'])->name('index');
    Route::get('/requirement', [InstallerController::class, 'requirement'])->name('requirement');
    Route::get('/permission', [InstallerController::class, 'permission'])->name('permission');
    Route::get('/license', [InstallerController::class, 'license'])->name('license');
    Route::post('/license', [InstallerController::class, 'licenseStore'])->name('licenseStore');
    Route::get('/site', [InstallerController::class, 'site'])->name('site');
    Route::post('/site', [InstallerController::class, 'siteStore'])->name('siteStore');
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database', [InstallerController::class, 'databaseStore'])->name('databaseStore');
    Route::get('/final', [InstallerController::class, 'final'])->name('final');
    Route::get('/final-store', [InstallerController::class, 'finalStore'])->name('finalStore');
});


Route::get('/', [RootController::class, 'index'])->middleware(['installed'])->name('home');
Route::prefix('payment')->name('payment.')->middleware(['installed'])->group(function () {
    Route::get('/{paymentGateway:slug}/pay/{frontendOrder}', [PaymentController::class, 'index'])->name('index');
    Route::post('/{frontendOrder}/pay', [PaymentController::class, 'payment'])->name('store')->middleware('throttle:payment');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{frontendOrder}/success', [PaymentController::class, 'success'])->name('success')->middleware('throttle:webhook');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{frontendOrder}/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{frontendOrder}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/successful/{frontendOrder}', [PaymentController::class, 'successful'])->name('successful');
});
Route::get('/manifest.json',[ManifestController::class, 'show'])->middleware(['installed'])->name('laravelpwa.manifest');
Route::get('/offline', OfflineController::class)->middleware(['installed'])->name('pwa.offline');
Route::get('/pwa/install-config', [ManifestController::class, 'installConfig'])->middleware(['installed'])->name('pwa.install-config');
Route::any('/{any}', [RootController::class, 'index'])->middleware(['installed'])->where(['any' => '.*']);
