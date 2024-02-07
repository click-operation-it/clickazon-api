<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Responser\JsonResponser;
use App\Helpers\ProcessAuditLog;
use App\Mail\VerifyEmail;
use App\Notifications\EmailVerificationNotification;
use SbscPackage\Ecommerce\Services\Paystack;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Notifications\PendingUserNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Notification;
use Carbon\Carbon;


class RegisterController extends Controller
{
    
    public function store(Request $request)
    {
        /**
         * Validate Data
         */
        $validate = $this->validateRegister($request);
        /**
         * if validation fails
         */
        if ($validate->fails()) {
            return JsonResponser::send(true, $validate->errors()->first(), $validate->errors()->all());
        }

        try {
            DB::beginTransaction();

            $recordExit = DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phoneno' => $request->phoneno,
                'email' => $request->email,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
            ]);

            // $token = $user->createToken('API_TOKEN')->plainTextToken;

            $verification_code = Str::random(30); //Generate verification code
            $otpCode = random_int(10000, 99999); //generate random num
            DB::table('user_verifications')->insert(['user_id' => $user->id, 'otp' => $otpCode, 'token'=>$verification_code, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $verification_code,
                'created_at' => Carbon::now()
            ]);

            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'phoneno' => $user->phoneno,
                'email' => $user->email,
                'token' => $verification_code,
            ];

            if (isset($request->userRole)) {
                $userRole = $request->user_role;
            } else {
                $userRole = DB::table('roles')->where('slug', 'ecommercecustomer')->first();
            }

            if (isset($userRole)){
                $user->attachRole($userRole->id);
            }

            Notification::route('mail', $request->email)->notify(new PendingUserNotification($data));

            $sanctumToken = $user->createToken('tokens')->plainTextToken;

            $dataToLog = [
                'causer_id' => $user->id,
                'action_id' => $user->id,
                'action_type' => "Models\User",
                'log_name' => "Account created successfully",
                'description' => "{$user->firstname} {$user->lastname} account created successfully",
            ];

            ProcessAuditLog::storeAuditLog($dataToLog);

            DB::commit();
            return JsonResponser::send(false, "Account created successfully", $user, 200);

        } catch (\Throwable $error) {
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }


    public function ecommerceCustomerSignup(Request $request)
    {
        /**
         * Validate Data
         */
        $validate = $this->validateRegister($request);
        /**
         * if validation fails
         */
        if ($validate->fails()) {
            return JsonResponser::send(true, $validate->errors()->first(), $validate->errors()->all());
        }

        try {
            DB::beginTransaction();

            $recordExit = DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            $paystackData = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phone' => $request->phoneno,
                'email' => $request->email,
            ];

            $result = Paystack::createCustomer($paystackData);
            if ($result["status"] !== true) {
                return JsonResponser::send(true, $result['message'], [], 400);
            }

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'phoneno' => $request->phoneno,
                'email' => $request->email,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'customer_code' => $result['data']['customer_code'],
                'password' => Hash::make($request->password)
            ]);

            // $token = $user->createToken('API_TOKEN')->plainTextToken;

            $verification_code = Str::random(30); //Generate verification code
            $otpCode = random_int(10000, 99999); //generate random num
            DB::table('user_verifications')->insert(['user_id' => $user->id, 'otp' => $otpCode, 'token'=>$verification_code, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

            DB::table('password_reset_tokens')->insert([
                'user_id' => $user->id,
                'email' => $request->email,
                'token' => $verification_code,
                'created_at' => Carbon::now()
            ]);

            $data = [
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'phoneno' => $user->phoneno,
                'email' => $user->email,
                'verification_code' => $verification_code,
                'otpCode' => $otpCode
            ];

            $userRole = DB::table('roles')->where('slug', 'ecommercecustomer')->first();

            if (isset($userRole)){
                $user->attachRole($userRole->id);
            }

            // Notification::route('mail', $request->email)->notify(new EmailVerificationNotification($data));

            Mail::to($request->email)->send(new VerifyEmail($data));

            $sanctumToken = $user->createToken('tokens')->plainTextToken;

            $dataToLog = [
                'causer_id' => $user->id,
                'action_id' => $user->id,
                'action_type' => "Models\User",
                'action' => 'Create',
                'log_name' => "Account created successfully",
                'description' => "{$user->firstname} {$user->lastname} account created successfully",
            ];

            ProcessAuditLog::storeAuditLog($dataToLog);

            DB::commit();
            return JsonResponser::send(false, "Account created successfully", $user, 200);

        } catch (\Throwable $error) {
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }

    /**
     * Resend Email Token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendCode(Request $request)
    {
        /**
         * Validate Data
         */
        $validate = $this->validateResendCode($request);
        /**
         * if validation fails
         */
        if ($validate->fails()) {
            return JsonResponser::send(true, "Validation Failed", $validate->errors()->all());
        }

        $email = $request->email;
        $user = User::where("email", $email)->first();
        if (!$user) {
            return JsonResponser::send(true, "User not found", null, 404);
        }

        if ($user->is_verified) {
            return JsonResponser::send(true, "Account already verified", null, 400);
        }

        $verification_code = Str::random(30); //Generate verification code
        $otpCode = random_int(10000, 99999); //generate random num
        DB::table('user_verifications')->insert(['user_id' => $user->id, 'otp' => $otpCode, 'token'=>$verification_code, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);

        $maildata = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'phoneno' => $user->phoneno,
            'email' => $user->email,
            'token' => $verification_code,
            'otpCode' => $otpCode
        ];

        Notification::route('mail', $request->email)->notify(new PendingUserNotification($maildata));
        return JsonResponser::send(false, "Verification link sent successfully.", null);
    }
    /**
     * Validate register request
     */
    protected function validateRegister($request)
    {
        $rules =  [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phoneno' => 'required|max:12|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'confirmPassword' => 'same:password'
        ];

        $validatedData = Validator::make($request->all(), $rules);
        return $validatedData;
    }


     /**
     * Validate resend code request
     */
    protected function validateResendCode($request)
    {
        $rules =  [
            'email' => 'required|email|max:255',
        ];

        $validatedData = Validator::make($request->all(), $rules);
        return $validatedData;
    }
}
