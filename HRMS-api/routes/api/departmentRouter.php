<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentsController;

Route::prefix('departments')->group(function () {
    Route::get('/', [DepartmentsController::class, 'getAllDepartments']);
    Route::post('/add', [DepartmentsController::class, 'addDepartment']);
    Route::delete('/delete/{id}', [DepartmentsController::class, 'deleteDepartment']);
    Route::put('/update/{id}', [DepartmentsController::class, 'updateDepartment']);
    Route::get('/department/{id}', [DepartmentsController::class, 'getDepartment']);
});
