<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcorrenciaController;

Route::post('/ocorrencias', [OcorrenciaController::class, 'store']);
Route::get('/ocorrencias/pendentes', [OcorrenciaController::class, 'indexPendentes']);
Route::get('/ocorrencias/arquivadas', [OcorrenciaController::class, 'indexArquivadas']);
Route::put('/ocorrencias/{id}/avaliar', [OcorrenciaController::class, 'avaliar']);
