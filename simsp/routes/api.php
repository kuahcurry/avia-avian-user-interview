<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Spare Part Fetch
Route::get(
    '/spare-parts/fetch', [App\Http\Controllers\Api\SparePartController::class, 'fetchSpareParts']
);

// Spare Part Usage
Route::post(
    '/spare-parts/usage/record', [App\Http\Controllers\Api\SparePartUsageController::class, 'recordUsage']
);
Route::get(
    '/spare-parts/usage/fetch', [App\Http\Controllers\Api\SparePartUsageController::class, 'fetchUsage']
);
