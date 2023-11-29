<?php

namespace App\Http\Controllers\v1\Auth;

use App\Models\User;
use App\Mail\ResetPassword;
use App\Mail\UpdatePasswordEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetNotification;
use App\Notifications\PasswordUpdateNotification;
use Illuminate\Support\Facades\Validator;
use Hash, Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Notification;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * API Recover Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recover(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];
        $validator = Validator::make($request->only("email"), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->messages(), 'data' => null], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json([
                'error' => true,
                'message' =>  $error_message,
                'data' => null
            ], 400);
        }

        try {
            $email = $request->email;
            $verification_code = Str::random(30); //Generate verification code
            $otpCode = random_int(10000, 99999); //generate random num
            DB::table('user_verifications')->insert(['user_id' => $user->id, 'otp' => $otpCode, 'token' => $verification_code, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $data = [
                'name' => $user->firstname,
                'email' => $email,
                'verification_code' => $verification_code,
                'subject' => "Reset Password Notification",
            ];
            Notification::route('mail', $user->email)->notify(new PasswordResetNotification($data));

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json([
                'error' => true,
                'message' => $error_message,
                'data' => null,
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'A reset email has been sent! Please check your email.',
            'data' => null
        ]);
    }

    /**
     * API Recover Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $rules = [
            'password' =>  [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                
            // 'password' => 'required|min:8|confirmed',
            "email" => "required|email",
            "token" => "required"
        ];
        DB::beginTransaction();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $token = DB::table('user_verifications')->where('token', $request->token)->first();

        if (!$token) {
            return response()->json([
                'error' => true,
                'message' => "Invalid token",
                'data' => null
            ], 400);
        }

        $password = $request->password;
        $userdata = User::where('id', $token->user_id)->first();
        
        $hashedPasword = $userdata->password;
        // check if new password is not the same with old password
        if (Hash::check($password, $hashedPasword)) {
            return response()->json([
                'error' => true,
                'message' => 'New password cannot be the same as old password',
                'data' => null
            ]);
        }
            
            
        $updatePassword = $userdata->update([
            'password' => Hash::make($password),
        ]);
        DB::table('user_verifications')->where('token', $request->token)->delete();
        if (!$updatePassword) {
            return response()->json([
                'error' => true,
                'message' => 'Error occured password was not updated',
                'data' => null
            ]);
        } else {
            $data = [
                'email' => $userdata->email,
                'name' => $userdata->firstname,
                'subject' => "Password Updated Successfully.",
            ];

            // Mail::to($request->email)->send(new UpdatePasswordEmail($data));
            Notification::route('mail', $user->email)->notify(new PasswordUpdateNotification($data));

            DB::commit();
            return response()->json([
                'error' => false,
                'message' => 'Password Updated! Please login with your new password',
                'data' => null
            ]);
        }
    }

    /**
     * API Create Password with Email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPassword(Request $request)
    {
        $rules = [
            'password' =>  [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                
            // 'password' => 'required|min:8|confirmed',
            "email" => "required|email",
        ];
        DB::beginTransaction();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $password = $request->password;
        $userdata = User::where('email', $request->email)->first();

        if (is_null($userdata)) {
            return response()->json([
                'error' => true,
                'message' => "Invalid email address",
                'data' => null
            ], 400);
        }

        $ecommerceCustomerRole = config('roles.models.role')::where('name', '=', 'Ecommerce Customer')->first();
        
        $hashedPasword = $userdata->password;
        // check if new password is not the same with old password
        if (Hash::check($password, $hashedPasword)) {
            return response()->json([
                'error' => true,
                'message' => 'New password cannot be the same as old password',
                'data' => null
            ]);
        }
               
        $updatePassword = $userdata->update([
            'password' => Hash::make($password),
        ]);

        $userdata->update([
            'is_active' => true,
            'can_login' => true,
            'is_verified' => true,
        ]);

        $userdata->attachRole($ecommerceCustomerRole);
        
        if (!$updatePassword) {
            return response()->json([
                'error' => true,
                'message' => 'Error occured password was not updated',
                'data' => null
            ]);
        } else {
            $data = [
                'email' => $userdata->email,
                'name' => $userdata->firstname,
                'subject' => "Password Updated Successfully.",
            ];

            
            DB::commit();
            return response()->json([
                'error' => false,
                'message' => 'Password Updated! Please login with your new password',
                'data' => null
            ]);
        }
    }
}
