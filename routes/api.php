<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);
Route::get('email/{email_token}', [AuthController::class, 'verifyEmail']);


Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout', [AuthController::class, 'logout']);

    // Route::get('get_user', [AuthController::class, 'get_user']);
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('flutterwave/make-payment', [PaymentController::class, 'flutterwavePayment']);

    Route::post('paystack/make-payment', [PaymentController::class, 'paystackPayment']);
});
