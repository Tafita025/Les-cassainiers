<?php

use App\Http\Controllers\auth\AuthentifiedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthentifiedController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthentifiedController::class, 'register'])->name('auth.register');
Route::post('/logout', [AuthentifiedController::class, 'logout'])->name('auth.logout');