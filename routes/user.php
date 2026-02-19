<?php

use App\Http\Controllers\home\OrderController;
use App\Http\Controllers\home\PannierController;
use App\Http\Controllers\home\ReviewController;
use Illuminate\Support\Facades\Route;

// Routes protégées par authentification
Route::middleware(['auth.modal'])->group(function () {

    // Page panier
    Route::get('/pannier', [PannierController::class, 'pannier'])->name('home.pannier');

    // Gestion du panier
    Route::post('/pannier/add/{product}', [PannierController::class, 'add'])->name('home.pannier.add');
    Route::patch('/pannier/{cartItem}', [PannierController::class, 'updateQuantity'])->name('home.pannier.update');
    Route::delete('/pannier/{cartItem}', [PannierController::class, 'remove'])->name('home.pannier.remove');
    Route::delete('/pannier', [PannierController::class, 'clear'])->name('home.pannier.clear');
    // payment
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('home.checkout');
    Route::get('/commande/success', [OrderController::class, 'success'])->name('home.order.success');
    Route::get('/commande/failure', [OrderController::class, 'failure'])->name('home.order.failure');
    // Reviews
    Route::post('/notes/{product}', [ReviewController::class, 'store'])->name('home.review.store');
});

// Webhook Papi — PAS dans le groupe auth, PAS de CSRF
Route::post('/papi/callback', [OrderController::class, 'callback'])->name('papi.callback');

