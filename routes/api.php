<?php

use App\Http\Controllers\Api\LetterController;
use App\Http\Controllers\Api\TrustedHubController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->group(function () {
    Route::post('/notification-order', 'handleNotification');
    Route::post('/letters/midtrans/create-transaction', 'createMidtransTransaction');
});
Route::controller(LetterController::class)->group(function () {
    Route::get('/letters', 'index');
    Route::get('/letters/detail/{slug}', 'show');
    Route::get('/letters/download/{orderId}/{slug}', 'downloadLetter');
});

Route::get('/get-trustedhub', [TrustedHubController::class, 'index'])->name('get.trustedhub');
