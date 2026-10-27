<?php

use App\Http\Controllers\TodolistController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api_key')->group(function () {
    // Matches /api/v1/todolist
    Route::prefix('v1')->group(function () {
        Route::get('/todolist', [TodolistController::class, 'index']);
        Route::post('/todolist', [TodolistController::class, 'store']);
        Route::put('/todolist/{todolistId}', [TodolistController::class, 'update']);
        Route::delete('/todolist/{todolistId}', [TodolistController::class, 'destroy']);
    });

    // Matches /api/todolist
    Route::get('/todolist', [TodolistController::class, 'index']);
    Route::post('/todolist', [TodolistController::class, 'store']);
    Route::put('/todolist/{todolistId}', [TodolistController::class, 'update']);
    Route::delete('/todolist/{todolistId}', [TodolistController::class, 'destroy']);
});
