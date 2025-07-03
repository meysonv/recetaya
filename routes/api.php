<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecetaController;

Route::get('/recetas', [RecetaController::class, 'index']);

Route::middleware('auth:sanctum')->post('/recetas/{id}/favorito', [RecetaController::class, 'toggleFavorito']);
