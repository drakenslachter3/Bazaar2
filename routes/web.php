<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
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

Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['nl', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/register/private', [RegisterController::class, 'showPrivateAdvertiserRegisterForm'])->name('register.private');
Route::get('/register/business', [RegisterController::class, 'showBusinessAdvertiserRegisterForm'])->name('register.business');

Route::resource('advertisements', AdvertisementController::class);
Route::get('/advertisements/{advertisement}/favorite', [AdvertisementController::class, 'toggleFavorite'])->name('advertisements.favorite');
Route::get('/favorites', [AdvertisementController::class, 'favorites'])->name('advertisements.favorites');
Route::get('/expiring-ads', [AdvertisementController::class, 'upcoming'])->name('advertisements.upcoming');
Route::post('/advertisements/import', [AdvertisementController::class, 'import'])->name('advertisements.import');

Route::resource('rentals', RentalController::class);
Route::get('/rent/{advertisement}', [RentalController::class, 'create'])->name('rentals.create.from.ad');

Route::get('/calendar/renter', [RentalCalendarController::class, 'renterCalendar'])->name('calendar.renter');
Route::get('/calendar/advertiser', [RentalCalendarController::class, 'advertiserCalendar'])->name('calendar.advertiser');

Route::get('/purchases', [PurchaseHistoryController::class, 'index'])->name('purchases.index');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::resource('reviews', ReviewController::class);
Route::get('/review/product/{advertisement}', [ReviewController::class, 'createProductReview'])->name('reviews.product.create');
Route::get('/review/user/{user}', [ReviewController::class, 'createUserReview'])->name('reviews.user.create');

Route::prefix('business')->middleware(['auth', 'business.owner'])->name('business.')->group(function () {
    Route::get('/edit', [BusinessController::class, 'edit'])->name('edit');
    Route::put('/update', [BusinessController::class, 'update'])->name('update');
    Route::post('/contract', [BusinessController::class, 'uploadContract'])->name('upload.contract');
    
    Route::resource('landing', LandingPageController::class);
    Route::post('landing/reorder', [LandingPageController::class, 'reorder'])->name('landing.reorder');
});

Route::get('/b/{customUrl}', [BusinessController::class, 'show'])->name('business.landing');

Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{id}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
    Route::post('/contracts/{id}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
});

