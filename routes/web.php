<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


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
    return view('emails.users.welcome');
});

Route::get('/send-email', function () {
    $user = User::findOrFail(1);
    Mail::send('emails.users.welcome', ['user' => $user], function ($m) {
        $m->from('dougieey1123@gmail.com', 'Your Application');

        $m->to('lesliedouglas23@gmail.com', 'user name')->subject('Joe Goldberg says Welcome!');
    });
    return "Email Sent with attachment. Check your inbox.";
});
