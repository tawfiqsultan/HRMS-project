ى<?php

    use App\Http\Controllers\AttendanceController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('attendance')->group(function () {
        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
            Route::get('/startWorkDay', [AttendanceController::class, 'startWorkDay']);
            Route::get('/all', [AttendanceController::class, 'getAllAttendance']);
            Route::get('/attendance/{id}', [AttendanceController::class, 'getAttendance']);
            Route::patch('/update/{id}', [AttendanceController::class, 'updateAttendance']);
        });

        Route::middleware(['auth:sanctum', 'role:employee'])->group(function () {
            Route::post('/checkIn', [AttendanceController::class, 'checkIn']);
            Route::post('/checkOut', [AttendanceController::class, 'checkOut']);
        });
    });
