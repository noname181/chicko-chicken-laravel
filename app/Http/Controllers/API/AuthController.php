<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Traits\SMSHelper;
use App\Traits\ResponseHelper;
use App\Traits\Utility;

use App\Mail\ResetPassword;

use App\User;
use Setting;

class AuthController extends Controller
{
    use SMSHelper, ResponseHelper, Utility;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationFailed($validator->errors());
        }
        
        $user = User::where('email', $request->email)->orWhere('phone', $request->email)->get()->first();

        if ($user && \Hash::check($request->password, $user->password)) {
            // $user->tokens()->delete();

            if ($user->hasVerifiedPhone() || !Setting::get('verification_required')) {
                $auth_token = $user->createToken('authToken')->plainTextToken;
            }

            $default_address = $user->addresses()->isPrimary()->first();

            if($default_address)
                $default_address = ['id' => $default_address->id,'label' => $default_address->label,'full_address' =>$default_address->street];
            
            $data = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'phone_verified' => $user->hasVerifiedPhone(),
                    'verification_required' => Setting::get('verification_required')
                ],
                'auth_token' => $auth_token ?? null,
                'default_address' => $default_address,
                'currency_symbol' => Setting::get('currency_symbol')
            ];

            return $this->successResponse($data);
        } else {
            return $this->errorResponse(["Invalid Login Credentials!"]);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string|unique:users,phone'
        ]);

        if ($validator->fails()) {
            return $this->validationFailed($validator->errors());
        }

        try {
            $input = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => Str::of($request->phone)->replace(' ', ''),
                'password' => \Hash::make($request->password),
            ];

            $user = new User($input);
            $user->save();

            $user->assignRole('Customer');

            if (!Setting::get('verification_required')) {
                $auth_token = $user->createToken('authToken')->plainTextToken;
            } else {
                $this->processSMS('OTP', $request->phone, null, $user);
            }

            $data = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->phone,
                    'avatar' => $user->avatar,
                    'verification_required' => Setting::get('verification_required')
                ],
                'auth_token' => $auth_token ?? null,
                'default_address' => null,
                'currency_symbol' => Setting::get('currency_symbol')
            ];

            return $this->successResponse($data, 201);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            $message = 'Your Verification code is: ' . $user->verification_code;
            $response = $this->processSMS(null, $request->phone, $message);

            if ($response) {
                $response = [
                    'otp' => true,
                ];
            } else {
                $response = [
                    'otp' => false,
                ];
            }
            return $this->successResponse($response);
        } else {
            return $this->errorResponse('User not found');
        }
    }
    
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
            'phone' => 'required',
        ]);
        
        $user = User::where([ ['email', $request->email],['phone', $request->phone], ['verification_code', $request->code] ])->first();

        if ($user) {
            $user->markPhoneAsVerified();
            $auth_token = $user->createToken('authToken')->plainTextToken;

            $data = [
                'auth_token' => $auth_token,
            ];
            return $this->successResponse($data);
        } else {
            return $this->errorResponse('Incorrect Verification Code');
        }
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationFailed($validator->errors());
        }
        
        $user = User::where('email', $request->email)->orWhere('phone', $request->email)->get()->first();

        if ($user) {

            $password = $this->readable_random_string(8);
            $hashed_password = \Hash::make($password);

            \Mail::to($user->email)->send(new ResetPassword(['password' => $password]));

            $user->password = $hashed_password;
            $user->save();

            return $this->successResponse("success");
        } else {
            return $this->errorResponse(["Invalid Login Credentials!"]);
        }
    }

    public function users()
    {
        $response = User::all();
        return $this->successResponse($response);
    }

    public function fcm_settings()
    {
        $response = Setting::get(['fcm_app_id','fcm_project_id','fcm_sender_id','fcm_web_certificate','fcm_web_api_key']);
        return $this->successResponse($response);
    }

    public function map_settings()
    {
        $response = Setting::get(['google_map_api_key']);
        return $this->successResponse($response);
    }

    public function razorpay_settings()
    {
        $response = Setting::get(['razorpay_api_public_key']);
        return $this->successResponse($response);
    }

    public function paypal_settings()
    {
        $response = Setting::get(['pg_paypal_key']);
        return $this->successResponse($response);
    }

    public function payment_settings()
    {
        $response = Setting::get(['currency_code','pg_cod_active','pg_paypal_active','pg_razorpay_active','pg_paypal_key','pg_razorpay_client_key']);
        return $this->successResponse($response);
    }
}
