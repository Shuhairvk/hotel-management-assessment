<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('permission:admin')->apiResource('staff', StaffController::class)->parameters(['staff' => 'staff']);

    Route::middleware('permission:guests')->apiResource('guests', GuestController::class);
    Route::middleware('permission:rooms')->apiResource('rooms', RoomController::class);

    Route::middleware('permission:bookings')->group(function () {
        Route::get('bookings/stats', [BookingController::class, 'stats']);
        Route::get('bookings/options', [BookingController::class, 'options']);
        Route::apiResource('bookings', BookingController::class);
    });
});
