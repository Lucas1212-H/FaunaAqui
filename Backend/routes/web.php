<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcorrenciaController;

// Rotas de API
Route::prefix('api')->group(function () {
    Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
});

// Rotas web
Route::get('/', function () {
    return view('welcome');
});
