<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\Frontend\PropertyController as FrontendPropertyController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Frontend\WelcomeController;
use Illuminate\Support\Facades\Route;




Route::get('/', [WelcomeController::class, 'index']);

Route::get('/categories', [FrontendCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [FrontendCategoryController::class, 'show'])->name('categories.show');
Route::get('/properties', [FrontendPropertyController::class, 'index'])->name('properties.index');
Route::post('/booking/step-one', [FrontendBookingController::class, 'storeStepOne'])->name('bookings.store.step.one');
Route::get('/booking/step-one', [FrontendBookingController::class, 'stepOne'])->name('bookings.step.one');
Route::post('/booking/step-two', [FrontendBookingController::class, 'storeStepTwo'])->name('bookings.store.step.two');
Route::get('/booking/step-two', [FrontendBookingController::class, 'stepTwo'])->name('bookings.step.two');
Route::get('/thankyou', [WelcomeController::class, 'thankyou'])->name('thankyou');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'admin'])->name('admin.')->prefix('admin')->group(function(){
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::resource('/categories', CategoryController::class);
    Route::resource('/properties', PropertyController::class);
    Route::resource('/tables', TableController::class);
    Route::resource('/bookings', BookingController::class);
});

require __DIR__.'/auth.php';
