<?php

use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__ . '/api/*.php') as $routeFile) {
    require $routeFile;
}
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
