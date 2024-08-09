<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BurgerController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\ForgotPasswordLinkController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware(['auth:sanctum'])->group(function () {
    });

    Route::apiResource('burgers', BurgerController::class);
    Route::apiResource('commandes', CommandeController::class);
    Route::post('commandes/{commande}/payer', [CommandeController::class, 'payer']);
    Route::get('commandes/filter', [CommandeController::class, 'filter']);
    Route::get('commandes/sendCompletionEmail/{id}', [CommandeController::class, 'sendCompletionEmail']);
    Route::put('/commandes/{id}/terminer', [CommandeController::class, 'terminerCommande']);
    Route::put('/commandes/{id}/annuler', [CommandeController::class, 'annulerCommande']);
    Route::post('/commandes/{id}/send-email', [CommandeController::class, 'sendEmail']);
    // Route::post('commandes/{commande}/payer', [CommandeController::class, 'payer'])->name('commandes.payer');
    // Route::put('commandes/{id}/terminer', [CommandeController::class, 'terminerCommande'])->name('commandes.terminer');
    // Route::put('commandes/{id}/annuler', [CommandeController::class, 'annulerCommande'])->name('commandes.annuler');
    
    // email
    Route::get('/email', [ForgotPasswordLinkController::class, 'index']);
    Route::post('/sendEmail', [ForgotPasswordLinkController::class, 'sendEmail']);

    

    Route::get('commandes/en-cours', [StatistiqueController::class, 'getCommandesEnCoursDuJour']);
    Route::get('commandes/validees', [StatistiqueController::class, 'getCommandesValideesDuJour']);
    Route::get('recettes/journalieres', [StatistiqueController::class, 'getRecettesJournalieres']);
    Route::get('commandes/annulees', [StatistiqueController::class, 'getCommandesAnnuleesDuJour']);
    Route::get('statistiques/totaux', [StatistiqueController::class, 'getTotaux']);

    

Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);



Route::get('/commandes-en-cours-du-jour', [StatistiqueController::class, 'getCommandesEnCoursDuJour']);
Route::get('/commandes-validees-du-jour', [StatistiqueController::class, 'getCommandesValideesDuJour']);
Route::get('/recettes-journalieres', [StatistiqueController::class, 'getRecettesJournalieres']);
Route::get('/commandes-annulees-du-jour', [StatistiqueController::class, 'getCommandesAnnuleesDuJour']);
Route::get('/totaux', [StatistiqueController::class, 'getTotaux']);
