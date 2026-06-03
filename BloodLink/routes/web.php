<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // // Donor Routes
    // Route::middleware('role:donor')->group(function () {
    //     Route::get('/donor/dashboard', [DonorController::class, 'dashboard'])->name('donor.dashboard');
    //     Route::get('/donor/requests', [DonorController::class, 'getPendingRequests'])->name('donor.requests');
    //     Route::post('/donor/requests/{request}/respond', [DonorController::class, 'respondToRequest'])->name('donor.respond');
    //     Route::get('/donor/history', [DonorController::class, 'getDonationHistory'])->name('donor.history');
    // });

    // // Hospital Routes
    // Route::middleware('role:hospital')->group(function () {
    //     Route::get('/hospital/dashboard', [HospitalController::class, 'dashboard'])->name('hospital.dashboard');
    //     Route::get('/hospital/requests', [HospitalController::class, 'manageRequests'])->name('hospital.requests');
    //     Route::get('/hospital/requests/create', [HospitalController::class, 'showCreateRequest'])->name('hospital.request.create');
    //     Route::post('/hospital/requests', [RequestController::class, 'store'])->name('hospital.request.store');
    //     Route::get('/hospital/requests/{request}', [HospitalController::class, 'showRequest'])->name('hospital.request.show');
    //     Route::put('/hospital/requests/{request}', [HospitalController::class, 'updateRequest'])->name('hospital.request.update');
    // });

    // // Admin Routes
    // Route::middleware('role:admin')->group(function () {
    //     Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    //     Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    //     Route::get('/admin/analytics', [AdminController::class, 'showAnalytics'])->name('admin.analytics');
    //     Route::put('/admin/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.user.approve');
    // });

    // Messages Routes
    Route::get('/messages', [AuthController::class, 'showMessages'])->name('messages');
    Route::get('/messages/{user}', [AuthController::class, 'showConversation'])->name('messages.show');
    Route::post('/messages/{user}', [AuthController::class, 'sendMessage'])->name('messages.send');
});

// Email Verification Routes
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');

// Password Reset Routes
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
