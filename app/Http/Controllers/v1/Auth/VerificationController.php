<?php

namespace App\Http\Controllers\v1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Responser\JsonResponser;
use App\Notifications\EnableOTPNotification;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Notification;

class VerificationController extends Controller
{
    /**
     * API Verify User email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyUser($code)
    {
        $check = DB::table('user_verifications')->where('token', $code)->first();

        if (!is_null($check)) {
            $user = User::where("id", $check->user_id)->with("userbilling")->with("usershipping")->first();

            if ($user->is_verified == 1) {
                return JsonResponser::send(true, "Account already verified.", null, 400);
            }

            $user->update([
                'is_verified' => 1,
                'can_login' => 1,
                'is_active' => 1,
                'is_completed' => 1,
            ]);

            $token = JWTAuth::fromUser($user);

            $data = [
                'accessToken' => $token,
                'tokenType' => 'Bearer',
                "user" => $user
            ];
            DB::table('user_verifications')->where('user_id', $check->user_id)->delete();

            return JsonResponser::send(false, "Account Verification successful.", $data);
        }

        return JsonResponser::send(true, "Verification code is invalid.", null, 400);
    }

    public function verifyOTP(Request $request)
    {
        $check = DB::table('user_verifications')->where('otp', $request->otp_code)->first();

        if (!is_null($check)) {
            $user = User::where("id", $check->user_id)->first();

            if ($user->is_verified == 1) {
                return JsonResponser::send(true, "Account already verified.", null, 400);
            }

            $user->update(['is_verified' => 1, 'can_login' => 1, 'email_verified_at' => Carbon::now()]);

            $token = JWTAuth::fromUser($user);

            $data = [
                'accessToken' => $token,
                'tokenType' => 'Bearer',
                "user" => $user
            ];
            DB::table('user_verifications')->where('user_id', $check->user_id)->delete();

            return JsonResponser::send(false, "Account Verification successful.", $data);
        }

        return JsonResponser::send(true, "Verification code is invalid.", null, 400);
    }

    public function update2fa()
    {
        $user = auth()->user();
        
        if (!$user) {
            return JsonResponser::send(true, "User not found", null, 404);
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

        Notification::route('mail', $user->email)->notify(new EnableOTPNotification($maildata));
        return JsonResponser::send(false, "OTP sent successfully.", null);
    }


    public function enable2fa(Request $request)
    {
        $check = DB::table('user_verifications')->where('otp', $request->otp_code)->first();

        if (!is_null($check)) {
            $user = User::where("id", $check->user_id)->first();

           
            $user->update(['two_fa' => $request->twofa]);


            DB::table('user_verifications')->where('user_id', $check->user_id)->delete();

            return JsonResponser::send(false, "Account updated successful.", $user);
        }

        return JsonResponser::send(true, "Verification code is invalid.", null, 400);
    }



    
}