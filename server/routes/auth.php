<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;


Route::controller(AuthController::class)->group(function () {
  Route::post('register', 'register')->middleware('guest');
  Route::post('login', 'login')->middleware('guest');
  Route::post('logout', 'logout')->middleware('auth');
  Route::post('refresh', 'refresh');
});

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
  ->middleware('guest')
  ->name('password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])
  ->middleware('guest')
  ->name('password.store');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
  ->middleware(['auth', 'throttle:6,1'])
  ->name('verification.send');
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
  ->middleware(['auth', 'signed', 'throttle:6,1'])
  ->name('verification.verify');
