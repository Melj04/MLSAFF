<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChartControl;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\navigationControl;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\DeviceVerificationController;

use App\Http\Controllers\RfidBinding_controller;

Route::get('/bind-rfid', [RfidBinding_controller::class, 'showBindForm'])->name('rfid.bind');
Route::post('/bind-rfid', [RfidBinding_controller::class, 'bindTag'])->name('rfid.bind.submit');
Route::get('/unbound-tags', [RfidBinding_controller::class, 'showUnboundTags'])->name('rfid.unbound');

use App\Http\Controllers\EncryptionController;

Route::get('/encryption/{sensorId}', [EncryptionController::class, 'encryptDecrypt']);

Route::get('/welcome', function () {
    return view('welcome');
});

// Login Page and Submission
Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// OTP routes
Route::middleware('auth')->group(function () {
    Route::get('device/verify', [DeviceVerificationController::class, 'showVerifyForm'])->name('device.show');
    Route::post('/device/verify', [DeviceVerificationController::class, 'verify'])->name('device.verify');
    Route::post('/device/resend', [DeviceVerificationController::class, 'resend'])->name('device.resend');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [VerifyEmailController::class, '__invoke'])
        ->middleware('throttle:6,1')
        ->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Protected routes
Route::middleware(['auth', 'VerifyDevice', 'verified'])->group(function () {
    Route::get('/dashboard', [ChartControl::class, 'chart'])->name('dashboard');
    Route::get('/chart-data', [ChartControl::class, 'getChartData']);
    Route::get('/IoTcontrol', [navigationControl::class, 'control'])->name('control');
    Route::get('/IoTparameter', [navigationControl::class, 'parameter'])->name('parameter');
    Route::get('/about', [navigationControl::class, 'about'])->name('about');
});

Route::middleware(['auth', 'verified', 'VerifyDevice'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
#hello
