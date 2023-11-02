<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Responser\JsonResponser;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ProcessAuditLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validateRequest = $this->validateRequest($request);

        if($validateRequest->fails()){
            return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
        }
       
       try {
            $credentials = request(['email', 'password']);

            $loginCheck = auth()->attempt($credentials);

            if (!$loginCheck) {
                return JsonResponser::send(true, "Incorrect email or password", [], 400);
            }

            $currentUserInstance = auth()->user();

            $token = JWTAuth::fromUser($currentUserInstance);

            // This will check if email has been verified
            if (!auth()->user()->is_verified) {
                return JsonResponser::send(true, 'Account not verified. Kindly verify your email', null);
            }

            // This will check if user has been deactivated
            if (!auth()->user()->is_active) {
                return JsonResponser::send(true, 'Your account has been deactivated. Please contact the administrator', null);
            }
            $user = User::with('usershipping', 'userbilling')->where('id', auth()->user()->id)->first();

            // Data to return
            $data = [
                "user" => $user,
                'accessToken' => $token,
                'tokenType' => 'Bearer',   
            ];

            $dataToLog = [
                'causer_id' => auth()->user()->id,
                'action_id' => $user->id,
                'action_type' => "Models\User",
                'log_name' => "User logged in successfully",
                'action' => "Update",
                'description' => "{$user->firstname} {$user->lastname} logged in successfully",
            ];

            ProcessAuditLog::storeAuditLog($dataToLog);

            return JsonResponser::send(false, 'You are logged in successfully', $data);
       } catch (\Throwable $error) {
            return JsonResponser::send(true, $error->getMessage(), [], 500);
       }
    }

    public function validateRequest($request)
    {
        $rules = [
            'email' => 'required|exists:users',
            'password' => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);
        return $validate;
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {

        $currentUserInstance = auth()->user();

        $dataToLog = [
            'causer_id' => auth()->user()->id,
            'action_id' => auth()->user()->id,
            'action_type' => "Models\User",
            'log_name' => "User logged out successfully",
            'description' => "{$currentUserInstance->lastname} {$currentUserInstance->firstname} Logged out successfully",
        ];

        ProcessAuditLog::storeAuditLog($dataToLog);

        auth()->logout();

        return JsonResponser::send(false, 'Successfully logged out', null);
    }
}
