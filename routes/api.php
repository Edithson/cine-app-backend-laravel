<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\SeanceController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ReservationController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne !',
        'timestamp' => now(),
    ]);
});

// les films
Route::post('/films', [FilmsController::class, 'index']);
Route::post('/films/deleted', [FilmsController::class, 'indexDeleted']);
Route::post('/films/create', [FilmsController::class, 'store']);
Route::get('/films/{id}', [FilmsController::class, 'show']);
Route::put('/films/update/{id}', [FilmsController::class, 'update']);
Route::delete('/films/delete/{id}', [FilmsController::class, 'destroy']);
Route::get('/films/restore/{id}', [FilmsController::class, 'restore']);

// les seances
Route::post('/seances', [SeanceController::class, 'index']);
Route::post('/seances/create', [SeanceController::class, 'store']);
Route::get('/seances/{id}', [SeanceController::class, 'show']);
Route::put('/seances/update/{id}', [SeanceController::class, 'update']);
Route::delete('/seances/delete/{id}', [SeanceController::class, 'destroy']);

// les salles
Route::get('/salles', [SalleController::class, 'index']);
Route::post('/salles/create', [SalleController::class, 'store']);
Route::get('/salles/{id}', [SalleController::class, 'show']);
Route::put('/salles/update/{id}', [SalleController::class, 'update']);
Route::delete('/salles/delete/{id}', [SalleController::class, 'destroy']);

// les catégories
Route::get('/categorie', [CategorieController::class, 'index']);
Route::post('/categorie/create', [CategorieController::class, 'store']);
Route::get('/categorie/{id}', [CategorieController::class, 'show']);
Route::put('/categorie/update/{id}', [CategorieController::class, 'update']);
Route::delete('/categorie/delete/{id}', [CategorieController::class, 'destroy']);

// les avis
Route::get('/avis', [AvisController::class, 'index']);
Route::get('/avis/user/{id}', [AvisController::class, 'getAvisByUser']);
Route::get('/avis/film/{id}', [AvisController::class, 'getAvisByFilm']);
Route::post('/avis/create', [AvisController::class, 'store']);
Route::put('/avis/update/{id}', [AvisController::class, 'update']);
Route::delete('/avis/delete/{id}', [AvisController::class, 'destroy']);

// les reservations
Route::get('/reservation', [ReservationController::class, 'index']);
Route::get('/reservation/user/{id}', [ReservationController::class, 'getReservationsByUser']);
Route::get('/reservation/seance/{id}', [ReservationController::class, 'getReservationsBySeance']);
Route::post('/reservation/create', [ReservationController::class, 'store']);
Route::get('/reservation/{id}', [ReservationController::class, 'show']);
Route::put('/reservation/update/{id}', [ReservationController::class, 'update']);
Route::delete('/reservation/delete/{id}', [ReservationController::class, 'destroy']);

// les utilisateurs
Route::post('/user', [UserController::class, 'index']);
Route::post('/user/deleted', [UserController::class, 'indexDeleted']);
Route::post('/user/create', [UserController::class, 'store']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/update/{id}', [UserController::class, 'update']);
Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);
Route::get('/user/restore/{id}', [UserController::class, 'restore']);

// connexion et deconnexion classique
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
