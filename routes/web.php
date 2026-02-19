<?php

use App\Http\Controllers\home\CategoryController;
use App\Http\Controllers\home\HomeController;
use App\Http\Controllers\home\OfferController;
use App\Http\Controllers\home\PaymentController;
use App\Http\Controllers\home\ProductController;
use App\Http\Controllers\home\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return to_route('home.index');
})->name('home');

Route::get('/accueil', [HomeController::class, 'home'])->name('home.index');
Route::get('/produit', [ProductController::class, 'product'])->name('home.product');
Route::get('/offers', [OfferController::class, 'offers'])->name('home.offers');
Route::get('/paiement', [PaymentController::class, 'payment'])->name('home.payment');
Route::get('/categories', [CategoryController::class, 'category'])->name('home.categories');
// Notes & Avis
Route::get('/notes', [ReviewController::class, 'review'])->name('home.review');
Route::get('/notes/{product}', [ReviewController::class, 'show'])->name('home.review.show');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/user.php';