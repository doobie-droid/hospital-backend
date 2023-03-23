<?php

namespace App\Http\Controllers;


use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'confirmed']
            ]);
            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }

            //Request is valid, create new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_token' => Str::random(16) . 'YmdHis',
            ]);
            $email = $user->email;
            //send welcome email
            Mail::send('emails.users.welcome', ['user' => $user], function ($m) use ($email) {
                $m->from('dougieey1123@gmail.com', 'Sign Up Almost Complete');

                $m->to($email, 'user name')->subject('Joe Goldberg says Welcome!');
            });
            //User created, return success response
            return $this->respondWithSuccess('Registration successful,Go to your email for verification then proceed to log in with those details', [
                'user' => $user
            ]);
        } catch (\Exception $exception) {
            // Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }

    public function authenticate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string']
            ]);

            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                Log::info($user);
                $token = JWTAuth::fromUser($user);
                Log::info($token);


                $key = "Authorization";
                $secure = true;
                $path = '/';
                $domain = '.clarify.com';
                $time_in_minutes = 2 * 60;
                $cookie = Cookie::queue($key, "Bearer {$token}", $time_in_minutes, $path, $domain, $secure);
                Log::info($cookie);
                return $this->respondWithSuccess('Login successful', [
                    'user' => $user,
                    'token' => $token,
                ]);
            } else {
                return $this->respondBadRequest('User credentials do not match our record');
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. Please try again later.');
        }
    }
    public function logout(Request $request)
    {
        try {
            //valid credential
            $validator = Validator::make($request->only('token'), [
                'token' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
            }

            //Request is validated, do logout        

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
            return $this->respondWithSuccess('User has been logged out');
        } catch (JWTException $exception) {
            Log::error($exception);
            return $this->respondInternalError('Oops, an error occurred. User cannot be logged out.');
        }
    }

    public function verifyEmail(Request $request, $email_token)
    {
        $validator = Validator::make(['email_token' => $email_token], [
            'email_token' => ['required', 'string', 'exists:users,email_token'],
        ]);
        if ($validator->fails()) {
            return $this->respondBadRequest('Invalid or missing input fields', $validator->errors()->toArray());
        }
        $user = User::where('email_token', $email_token)->first();
        if ($user) {
            $user->email_verified = 1;
            $user->email_token = '';
            $user->save();
            return $this->respondWithSuccess('Email verified successfully, You can now proceed to log in');
        } else {
            return $this->respondBadRequest('Token has expired');
        }
    }
    // public function get_user(Request $request)
    // {
    //     $this->validate($request, [
    //         'token' => 'required'
    //     ]);

    //     $user = JWTAuth::authenticate($request->token);

    //     return response()->json(['user' => $user]);
    // }
}
