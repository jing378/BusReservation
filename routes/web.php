<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\TransactionController as CustomerTransactionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

/*
| Authentication
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
| Admin area. Each page posts back to its own URL with an "action" field, like the original app.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');

    Route::get('/trips', [TripController::class, 'index'])->name('trips');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');

    Route::get('/routes', [RouteController::class, 'index'])->name('routes');
    Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');

    Route::get('/buses', [BusController::class, 'index'])->name('buses');
    Route::post('/buses', [BusController::class, 'store'])->name('buses.store');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::post('/logs', [LogController::class, 'store'])->name('logs.store');

    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions', [AdminTransactionController::class, 'store'])->name('transactions.store');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
});

/*
| Customer area.
*/
Route::middleware('auth')->prefix('customer')->name('customer.')->group(function () {
    Route::middleware('customer')->group(function () {
        Route::get('/dashboard', CustomerDashboardController::class)->name('dashboard');
        Route::get('/transactions', [CustomerTransactionController::class, 'index'])->name('transactions');
        Route::post('/transactions', [CustomerTransactionController::class, 'store'])->name('transactions.store');
    });

    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

/*
| JSON lookups used by the pages' JavaScript (session-authenticated).
*/
Route::prefix('api')->name('api.')->controller(LookupController::class)->group(function () {
    Route::get('/weather', 'weather')->name('weather');

    Route::middleware('auth')->group(function () {
        Route::get('/check-seats', 'checkSeats')->name('check-seats');
        Route::get('/check-duplicate-user', 'checkDuplicateUser')->name('check-duplicate-user');
        Route::get('/fetch-users', 'fetchUsers')->name('fetch-users');

        Route::middleware('admin')->group(function () {
            Route::get('/check-duplicate-bus', 'checkDuplicateBus')->name('check-duplicate-bus');
            Route::get('/check-duplicate-route', 'checkDuplicateRoute')->name('check-duplicate-route');
            Route::get('/check-duplicate-trip', 'checkDuplicateTrip')->name('check-duplicate-trip');
            Route::get('/check-route-trips', 'checkRouteTrips')->name('check-route-trips');
            Route::get('/check-trip-bookings', 'checkTripBookings')->name('check-trip-bookings');
        });
    });
});
