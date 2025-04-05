<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;

Route::prefix('departments')->group(function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/', [DepartmentController::class, 'getAllDepartments']);
        Route::post('/add', [DepartmentController::class, 'addDepartment']);
        Route::delete('/delete/{id}', [DepartmentController::class, 'deleteDepartment']);
        Route::put('/update/{id}', [DepartmentController::class, 'updateDepartment']);
        Route::get('/department/{id}', [DepartmentController::class, 'getDepartment']);
    });
});
