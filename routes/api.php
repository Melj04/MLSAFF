<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\EncryptionController;
use App\Http\Controllers\RfidBinding_controller;

Route::post('/receive', [EncryptionController::class, 'receiveData']);
Route::post('/verify-tag', [RfidBinding_controller::class, 'verifyTag'])->name('rfid.verify');

use App\Http\Controllers\EspDataController;

Route::get('/command-data', [EspDataController::class, 'getCommandData']);

Route::post('/store-plaintext', [EncryptionController::class, 'storePlainText']);
Route::post('/store-encrypted', [EncryptionController::class, 'storeCipher']);
