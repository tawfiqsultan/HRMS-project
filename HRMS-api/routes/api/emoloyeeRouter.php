<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;


Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'getAllEmployees']);
    Route::post('/add', [EmployeeController::class, 'addEmployee']);
    Route::delete('/delete/{id}', [EmployeeController::class, 'deleteEmployee']);
    Route::put('/update/{id}', [EmployeeController::class, 'updateEmployee']);
    Route::get('/employee/{id}', [EmployeeController::class, 'getEmployee']);
});
