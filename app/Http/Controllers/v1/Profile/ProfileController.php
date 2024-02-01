<?php

namespace App\Http\Controllers\v1\Profile;

use App\Helpers\FileUploadHelper;
use App\Helpers\ProcessAuditLog;
use App\Helpers\UserMgtHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Responser\JsonResponser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordUpdateNotification;
use Illuminate\Support\Facades\Notification;
use Hash, DB;

class ProfileController extends Controller
{

    public function profile(){
        $currentUserInstance = User::with('userbilling', 'usershipping')->where('id', auth()->user()->id)->first();

        return JsonResponser::send(false, "Record found successfully", $currentUserInstance);
    }

    public function updateProfile(Request $request)
    {
        try {
            $currentUserInstance = auth()->user();

            $user = User::with('userbilling', 'usershipping')->where('id', $currentUserInstance->id)->first();

            if(is_null($user)){
                return JsonResponser::send(true, "Record not found", []);
            }

            if(!is_null($request->image)){
                $attachment = FileUploadHelper::singleStringFileUpload($request->image, "Profile");
            }else{
                $attachment = $user->image;
            }
            
            $user->update([
                'firstname' => $request->firstname ?? $user->firstname,
                'lastname' => $request->lastname ?? $user->lastname,
                'country' => $request->country ?? $user->country,
                'state' => $request->state ?? $user->state,
                'city' => $request->city ?? $user->city,
                'postal_code' => $request->postal_code ?? $user->postal_code,
                'address' => $request->address ?? $user->address,
                'image' => $attachment
            ]);

            $dataToLog = [
                'causer_id' => $currentUserInstance->id,
                'action_id' => $user->id,
                'action_type' => "Models\User",
                'action' => 'Update',
                'log_name' => "Record updated successfully",
                'description' => "{$user->firstname} {$user->lastname} record updated successfully",
            ];

            ProcessAuditLog::storeAuditLog($dataToLog);

            return JsonResponser::send(false, "Record updated successfully", $user);

        } catch (\Throwable $error) {
            return JsonResponser::send(true, $error->getMessage(), []);
        }
    }

    /**
     * API Reset Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
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
                
            'old_password' => 'required|min:8',
        ];
        DB::beginTransaction();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first(), 'data' => null], 400);
        }

        $password = $request->password;
        $userdata = User::find(auth()->user()->id);
        
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
            Notification::route('mail', $userdata->email)->notify(new PasswordUpdateNotification($data));

            DB::commit();
            return response()->json([
                'error' => false,
                'message' => 'Password Updated! Please login with your new password',
                'data' => null
            ]);
        }
    }

}