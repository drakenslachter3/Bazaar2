<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalCalendarController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\LandingPageController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdvertiserController;

// Auth routes
Auth::routes();

// Taalwissel
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['nl', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

// Thuispagina
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);

// Aangepaste registratie routes
Route::get('/register/private', [RegisterController::class, 'showPrivateAdvertiserRegisterForm'])
    ->name('register.private')
    ->middleware('guest');
    
Route::get('/register/business', [RegisterController::class, 'showBusinessAdvertiserRegisterForm'])
    ->name('register.business')
    ->middleware('guest');

// Publiek toegankelijke advertentie routes
Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
Route::get('/advertisements/{advertisement}', [AdvertisementController::class, 'show'])->name('advertisements.show');

// Routes die authenticatie vereisen
Route::middleware('auth')->group(function() {
    // Advertentie routes die auth vereisen
    Route::get('/advertisements-create', [AdvertisementController::class, 'create'])->name('advertisements.createad');
    Route::post('/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    Route::get('/advertisements/{advertisement}/edit', [AdvertisementController::class, 'edit'])->name('advertisements.edit');
    Route::put('/advertisements/{advertisement}', [AdvertisementController::class, 'update'])->name('advertisements.update');
    Route::delete('/advertisements/{advertisement}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy');
    Route::get('/advertisements/{advertisement}/favorite', [AdvertisementController::class, 'toggleFavorite'])->name('advertisements.favorite');
    Route::get('/favorites', [AdvertisementController::class, 'favorites'])->name('advertisements.favorites');
    Route::get('/expiring-ads', [AdvertisementController::class, 'upcoming'])->name('advertisements.upcoming');
    Route::post('/advertisements/import', [AdvertisementController::class, 'import'])->name('advertisements.import');
    
    // Adverteerder dashboard
    Route::get('/advertiser/dashboard', [AdvertiserController::class, 'dashboard'])->name('advertiser.dashboard');
    Route::get('/advertiser/import', [AdvertiserController::class, 'importForm'])->name('advertisements.import.form');
    
    // Verhuur
    Route::resource('rentals', RentalController::class);
    Route::get('/rent/{advertisement}', [RentalController::class, 'create'])->name('rentals.create.from.ad');
    
    // Kalenders
    Route::get('/calendar/renter', [RentalCalendarController::class, 'renterCalendar'])->name('calendar.renter');
    Route::get('/calendar/advertiser', [RentalCalendarController::class, 'advertiserCalendar'])->name('calendar.advertiser');
    
    // Aankoop geschiedenis
    Route::get('/purchases', [PurchaseHistoryController::class, 'index'])->name('purchases.index');
    
    // Profielinstellingen
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Reviews
    Route::resource('reviews', ReviewController::class);
    Route::get('/review/product/{advertisement}', [ReviewController::class, 'createProductReview'])->name('reviews.product.create');
    Route::get('/review/user/{user}', [ReviewController::class, 'createUserReview'])->name('reviews.user.create');
});

// Zakelijke instellingen
Route::prefix('business')->middleware(['auth', 'business.owner'])->name('business.')->group(function () {
    Route::get('/edit', [BusinessController::class, 'edit'])->name('edit');
    Route::put('/update', [BusinessController::class, 'update'])->name('update');
    Route::post('/contract', [BusinessController::class, 'uploadContract'])->name('upload.contract');
    
    // Landing page beheer
    Route::resource('landing', LandingPageController::class);
    Route::post('landing/reorder', [LandingPageController::class, 'reorder'])->name('landing.reorder');
});

// Zakelijke landingspagina (voor publiek)
Route::get('/b/{customUrl}', [BusinessController::class, 'show'])->name('business.landing');

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{id}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
    Route::post('/contracts/{id}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
});