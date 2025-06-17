<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Item routes with permission checks
    Route::get('/items', [ItemController::class, 'index'])->middleware('permission:view-items');
    Route::post('/items', [ItemController::class, 'store'])->middleware('permission:create-items');
    Route::get('/items/{id}', [ItemController::class, 'show'])->middleware('permission:view-items');
    Route::put('/items/{id}', [ItemController::class, 'update'])->middleware('permission:edit-items');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->middleware('permission:delete-items');
});