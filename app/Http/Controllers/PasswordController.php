<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Validator;
use Illuminate\Support\Facades\Crypt;

class PasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|string',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 404]);
        }
        $encyptUserId = $request->userid;
        $password = $request->password;
        
        try {
            $user_id = Crypt::decryptString($encyptUserId);
            User::where('id', $user_id)->update(['password' => bcrypt($password)]);
            return response()->json(["message" => 'Password Changed Successfully.', "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 500]);
        }
        //dd($user_id);
    }

    public function verifyAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 404]);
        }
        $encyptUserId = $request->userid;
        // $password = $request->password;
        
        try {
            // dd($encyptUserId);
            $user_id = Crypt::decryptString($encyptUserId);
            // dd($user_id);
            User::where('id', $user_id)->update(['email_verified_at' => date('Y-m-d H:i:s')]);
            return response()->json(["message" => 'Account verified Successfully.', "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 500]);
        }
        //dd($user_id);
    }
}
