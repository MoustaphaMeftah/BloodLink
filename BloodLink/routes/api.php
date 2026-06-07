<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🟢 AUTH (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Password reset (public)
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| 🔐 ROUTES PROTÉGÉES (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // 🔴 AUTH
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);

    // 👤 USER PROFILE
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | 🩸 DONORS
    |--------------------------------------------------------------------------
    */

    Route::get('/donors', [DonorController::class, 'index']);
    Route::get('/donors/{id}', [DonorController::class, 'show']);
    Route::put('/donors/{id}', [DonorController::class, 'update']);
    Route::delete('/donors/{id}', [DonorController::class, 'destroy']);

    // search & advanced features
    Route::get('/donors-search', [DonorController::class, 'search']);
    Route::get('/donors-nearby', [DonorController::class, 'getNearbyDonors']);
    Route::get('/donors/{id}/history', [DonorController::class, 'getDonationHistory']);
    Route::get('/donors/{id}/requests', [DonorController::class, 'getPendingRequests']);
    Route::post('/donors/{donorId}/respond/{requestId}', [DonorController::class, 'respondToRequest']);

    /*
    |--------------------------------------------------------------------------
    | 🩸 BLOOD REQUESTS
    |--------------------------------------------------------------------------
    */

    Route::get('/requests', [BloodRequestController::class, 'index']);
    Route::post('/requests', [BloodRequestController::class, 'store']);
    Route::get('/requests/{id}', [BloodRequestController::class, 'show']);
    Route::put('/requests/{id}', [BloodRequestController::class, 'update']);
    Route::delete('/requests/{id}', [BloodRequestController::class, 'destroy']);

    // advanced request actions
    Route::get('/requests/{id}/donors', [BloodRequestController::class, 'compatibleDonors']);
    Route::put('/requests/{id}/urgent', [BloodRequestController::class, 'markUrgent']);
    Route::put('/requests/{id}/complete', [BloodRequestController::class, 'markCompleted']);

    /*
    |--------------------------------------------------------------------------
    | 🩸 DONATIONS
    |--------------------------------------------------------------------------
    */

    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/donors/{donorId}/donations', [DonationController::class, 'history']);
    Route::get('/donors/{donorId}/eligibility', [DonationController::class, 'checkEligibility']);
});
