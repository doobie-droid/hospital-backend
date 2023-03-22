<?php

namespace App\Http\Controllers;


use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

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
                'remember_token' => Str::random(16) . 'YmdHis',
            ]);

            $token = JWTAuth::fromUser($user);
            //User created, return success response
            return $this->respondWithSuccess('Registration successful', [
                'user' => $user,
                'token' => $token,
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
                $token = JWTAuth::fromUser($user);


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

    // public function get_user(Request $request)
    // {
    //     $this->validate($request, [
    //         'token' => 'required'
    //     ]);

    //     $user = JWTAuth::authenticate($request->token);

    //     return response()->json(['user' => $user]);
    // }
}
