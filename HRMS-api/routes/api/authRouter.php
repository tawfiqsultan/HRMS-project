<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::prefix('auth')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/updateEamil', [AuthController::class, 'updateEmail']);
        Route::post('/updatePassword', [AuthController::class, 'updatePassword']);
        Route::post('/sendResetCode', [AuthController::class, 'sendResetCode']);
        Route::post('/verifyResetCode', [AuthController::class, 'verifyResetCode']);
        Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
    });
});
