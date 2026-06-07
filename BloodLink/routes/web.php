<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Donor Routes
    Route::middleware('role:donor')->group(function () {
        Route::get('/donor/dashboard', [DonorController::class, 'dashboard'])->name('donor.dashboard');
        Route::get('/donor/requests', [DonorController::class, 'getPendingWebRequests'])->name('donor.requests');
        Route::post('/donor/requests/{request}/respond', [DonorController::class, 'respondToWebRequest'])->name('donor.respond');
        Route::get('/donor/history', [DonorController::class, 'getWebDonationHistory'])->name('donor.history');
    });

    // Hospital Routes
    Route::middleware('role:hospital')->group(function () {
        Route::get('/hospital/dashboard', [HospitalController::class, 'dashboard'])->name('hospital.dashboard');
        Route::get('/hospital/requests', [HospitalController::class, 'manageRequests'])->name('hospital.requests');
        Route::get('/hospital/requests/create', [HospitalController::class, 'showCreateRequest'])->name('hospital.request.create');
        Route::post('/hospital/requests', [RequestController::class, 'store'])->name('hospital.request.store');
        Route::get('/hospital/requests/{request}', [HospitalController::class, 'showRequest'])->name('hospital.request.show');
        Route::put('/hospital/requests/{request}', [HospitalController::class, 'updateRequest'])->name('hospital.request.update');
        Route::post('/hospital/response/{response}/confirm', [HospitalController::class, 'confirmDonor'])->name('hospital.response.confirm');
    });

    // Map Routes (accessible by all authenticated users)
    Route::get('/donor/nearby', [DonorController::class, 'showNearbyRequests'])->name('donor.nearby');
    Route::post('/donor/location', [DonorController::class, 'updateLocation'])->name('donor.location.update');
    Route::get('/hospital/nearby-donors', [HospitalController::class, 'showNearbyDonors'])->name('hospital.nearby-donors');
    Route::post('/hospital/location', [HospitalController::class, 'updateLocation'])->name('hospital.location.update');

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
        Route::get('/admin/analytics', [AdminController::class, 'showAnalytics'])->name('admin.analytics');
        Route::put('/admin/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.user.approve');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.user.delete');
        Route::get('/admin/requests', [AdminController::class, 'manageRequests'])->name('admin.requests');
        Route::put('/admin/requests/{bloodRequest}/status', [AdminController::class, 'updateRequestStatus'])->name('admin.request.status');
        Route::get('/admin/export', [AdminController::class, 'exportCsv'])->name('admin.export');
        Route::get('/admin/activity-log', [AdminController::class, 'showActivityLog'])->name('admin.activity-log');
        Route::get('/admin/map', [AdminController::class, 'showMap'])->name('admin.map');
    });

    // Messages Routes
    Route::get('/messages', [AuthController::class, 'showMessages'])->name('messages');
    Route::get('/messages/{user}', [AuthController::class, 'showConversation'])->name('messages.show');
    Route::post('/messages/{user}', [AuthController::class, 'sendMessage'])->name('messages.send');
    Route::post('/messages/read-all', [AuthController::class, 'markAllMessagesRead'])->name('messages.read-all');
    Route::get('/messages/recipients/list', [AuthController::class, 'getRecipients'])->name('messages.recipients');

    // Friend Routes
    Route::get('/friends', [FriendController::class, 'index'])->name('friends');
    Route::get('/friends/find', [FriendController::class, 'findPeople'])->name('friends.find');
    Route::post('/friends/{user}/send', [FriendController::class, 'sendRequest'])->name('friends.send');
    Route::post('/friends/{user}/accept', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/friends/{user}/decline', [FriendController::class, 'declineRequest'])->name('friends.decline');
    Route::delete('/friends/{user}/remove', [FriendController::class, 'removeFriend'])->name('friends.remove');
    Route::delete('/friends/{user}/cancel', [FriendController::class, 'cancelRequest'])->name('friends.cancel');
    Route::get('/notifications', [FriendController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/read', [FriendController::class, 'markNotificationRead'])->name('notifications.read');
});

// Email Verification Routes
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');
