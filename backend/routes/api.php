<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Movie routes
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::post('/movies', [MovieController::class, 'store']);
Route::put('/movies/{id}', [MovieController::class, 'update']);
Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

// Helper routes
Route::get('/genres', [MovieController::class, 'genres']);
Route::get('/years', [MovieController::class, 'years']);

// Video streaming route
Route::get('/stream/{id}', [VideoController::class, 'stream']);
