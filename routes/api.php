<?php

use App\Http\Controllers\Api\DonorApiController;
use App\Http\Controllers\Api\MatchApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.basic')->group(function (): void {
    Route::get('/donors', DonorApiController::class);
    Route::get('/matches', MatchApiController::class);
});
