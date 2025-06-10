<?php

use Illuminate\Support\Facades\Route;

$idRegex = '[0-9]+';
$slugRegex = '[0-9a-z\-]+';

// MODIFIÉ : Ajout de ->name('home') pour nommer la route d'accueil
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/biens',[\App\Http\Controllers\PropertyController::class, 'index'])->name('property.index');
Route::get('/biens/{slug}-{property}', [\App\Http\Controllers\PropertyController::class, 'show'])->name('property.show')->where([
    'property'=> $idRegex,
    'slug'=> $slugRegex
]);
Route::get('/biens_locatif', [\App\Http\Controllers\RentalController::class, 'index'])->name('rental.index');
Route::get('/biens_locatif/{slug}-{rental}', [\App\Http\Controllers\RentalController::class, 'show'])->name('rental.show')->where([
    'rental'=> $idRegex,
    'slug'=> $slugRegex
]);


Route::post('/biens/{property}/contact', [\App\Http\Controllers\PropertyController::class, 'contact'])->name('property.contact')->where([
    'property'=> $idRegex, 
]);
Route::post('/biens_locatif/{rental}/contact', [\App\Http\Controllers\RentalController::class, 'contact'])->name('rental.contact')->where([
    'rental'=> $idRegex, 
]);


Route::get('/login', [\App\Http\Controllers\AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'doLogin']);
Route::delete('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);

// AJOUTÉ : 'admin' au tableau des middlewares pour protéger la section admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::resource('property', \App\Http\Controllers\Admin\PropertyController::class)->except(['show']);
    Route::resource('option', \App\Http\Controllers\Admin\OptionController::class)->except(['show']);
    Route::resource('rental', \App\Http\Controllers\Admin\RentalController::class)->except(['show']);
    Route::resource('feature', \App\Http\Controllers\Admin\FeatureController::class)->except(['show']);

});