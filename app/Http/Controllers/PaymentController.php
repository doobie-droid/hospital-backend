<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    //
    public function flutterwavePayment(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'card_number' => ['required', 'size:16', 'string'],
                'cvv' => ['required', 'size:3', 'string'],
                'expiry_month' => ['required', 'string', 'min:1', 'max:2'],
                'expiry_year' => ['required', 'string', 'min:2', 'max:2'],
                'amount' => ['required', 'string'],

            ]);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            $base_url = env('APP_URL', 'http://localhost:8000/api');
            $currency = 'NGN';
            $email =  "user@example.com";
            $fullname = "Flutterwave Developers";
            $tx_ref = 'clafiya' . date('Ymdhis');;
            Log::info($tx_ref);
            $redirect_url = "{$base_url}";
            Log::info($redirect_url);
            // //Request is valid, create new user
            // $user = User::create([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'password' => Hash::make($request->password),
            //     'email_token' => Str::random(16) . 'YmdHis',
            // ]);
            // $email = $user->email;

            // $token = JWTAuth::fromUser($user);
            // //send welcome email
            // Mail::send('emails.users.welcome', ['user' => $user], function ($m) use ($email) {
            //     $m->from('dougieey1123@gmail.com', 'Your Application');

            //     $m->to($email, 'user name')->subject('Joe Goldberg says Welcome!');
            // });
            // //User created, return success response
            // return $this->respondWithSuccess('Registration successful', [
            //     'user' => $user,
            //     'token' => $token,
            // ]);
        } catch (\Exception $exception) {
            // Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
}
