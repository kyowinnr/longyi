<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [AccountingController::class, 'index'])->name('dashboard');
    Route::post('/members/recruit', [AccountingController::class, 'recruit'])->name('members.recruit');
    Route::post('/seed-example', [AccountingController::class, 'seedExample'])->name('seed.example');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
