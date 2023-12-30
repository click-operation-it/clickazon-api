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
use Hash, DB;

class ProfileController extends Controller
{

public function updateProfile(Request $request)
    {
        try {
            $currentUserInstance = auth()->user();

            $user = User::find($currentUserInstance->id);

            if(is_null($user)){
                return JsonResponser::send(true, "Record not found", []);
            }

            if(!is_null($request->profile_picture)){
                $attachment = FileUploadHelper::singleStringFileUpload($request->profile_picture, "Profile");
            }else{
                $attachment = $user->image;
            }
			
            $user->update([
                'firstname' => $request->firstname ?? $user->firstname,
                'lastname' => $request->lastname ?? $user->lastname,
                'image' => $attachment
            ]);

            $dataToLog = [
                'causer_id' => $currentUserInstance->id,
                'action_id' => $user->id,
                'action_type' => "Models\User",
                'log_name' => "Record updated successfully",
                'description' => "{$user->firstname} {$user->lastname} record updated successfully",
            ];

            ProcessAuditLog::storeAuditLog($dataToLog);

            return JsonResponser::send(false, "Record updated successfully", $user);

        } catch (\Throwable $error) {
            return JsonResponser::send(true, $error->getMessage(), []);
        }
    }

}