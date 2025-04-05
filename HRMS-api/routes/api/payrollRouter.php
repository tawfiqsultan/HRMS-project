<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollController;

Route::prefix('payroll')->group(function () {
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/generate', [PayrollController::class, 'generatePayroll']);
        Route::get('/all', [PayrollController::class, 'getAllPayroll']);
        Route::get('/payroll/{id}', [PayrollController::class, 'getPayroll']);
        Route::get('/employee/{id}', [PayrollController::class, 'getEmployeePayrolls']);
        Route::patch('/update/{id}', [PayrollController::class, 'updatePayroll']);
    });
});
