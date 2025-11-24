<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas por el token del microservicio de autenticación
Route::middleware('auth.micro')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::patch('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
