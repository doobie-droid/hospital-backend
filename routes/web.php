<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Services\Payment\Providers\Flutterwave\Flutterwave;
use Illuminate\Support\Facades\Log;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email', function () {
    $flutterwave = new Flutterwave;
    $req = $flutterwave->verifyTransaction(4219558);
    if ($req->status !== 'success' || $req->data->status !== 'successful') {
        //update the payment table to show failed payment
        return $this->respondWithSuccess('Payment received successfully');
    }
    //update the payment table to show successful payment
    //notify the user of successful payment
    dd($req);
});
