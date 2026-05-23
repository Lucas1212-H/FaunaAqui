<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcorrenciaController;

Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
Route::get('/ocorrencias/pendentes', [OcorrenciaController::class, 'indexPendentes']);
Route::get('/ocorrencias/arquivadas', [OcorrenciaController::class, 'indexArquivadas']);
Route::get('/ocorrencias/publicados', [OcorrenciaController::class, 'indexPublicados']);
Route::put('/ocorrencias/{id}/avaliar', [OcorrenciaController::class, 'avaliar']);
Route::put('/ocorrencias/{id}/publicar', [OcorrenciaController::class, 'publicar']);
