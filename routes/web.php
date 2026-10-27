<?php

use App\Http\Controllers\TodolistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('swagger');
});

Route::get('/docs', function () {
    return view('swagger');
});

Route::get('/api/documentation', function () {
    return view('swagger');
});

Route::middleware('auth.api_key')->group(function () {
    Route::get('/todolist', [TodolistController::class, 'index']);
    Route::post('/todolist', [TodolistController::class, 'store']);
    Route::put('/todolist/{todolistId}', [TodolistController::class, 'update']);
    Route::delete('/todolist/{todolistId}', [TodolistController::class, 'destroy']);
});
