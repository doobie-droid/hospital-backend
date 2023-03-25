<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AppointmentController;

Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);
Route::get('email/verify/new/{email_token}', [AuthController::class, 'verifyEmail']);


Route::post('flutterwave/webhook', [PaymentController::class, 'flutterwaveWebhook']);

Route::post('paystack/webhook', [PaymentController::class, 'paystackWebhook']);


Route::get('/payments/verified/redirect/{response?}', function (string $response = null) {
    return redirect('/success');
});




Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('flutterwave/verify-account', [PaymentController::class, 'flutterwaveVerifyAccount']);

    Route::post('flutterwave/make-payment/card', [PaymentController::class, 'flutterwaveCardPayment']);
    Route::post('flutterwave/verify-payment/card', [PaymentController::class, 'flutterwaveVerifyCardPayment']);

    Route::group(['prefix' => 'appointments'], function () {
        Route::get('/', [AppointmentController::class, 'get']);
        Route::post('/', [AppointmentController::class, 'create']);
        Route::patch('{appointment_id}', [AppointmentController::class, 'update']);
        Route::delete('{appointment_id}', [AppointmentController::class, 'delete']);
        Route::get('/all', [AppointmentController::class, 'getAll']);
    });


    Route::post('paystack/make-payment/mobile', [PaymentController::class, 'paystackMobilePayment']);

    Route::post('paystack/make-payment/ussd', [PaymentController::class, 'paystackUssdPayment']);
});
