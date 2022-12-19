<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserBlock;
use Validator;

class UserBlockController extends Controller
{
    //
    public function getUserBlockList()
    {
        $response = UserBlock::where('no_of_attempts', '!=', '0')
        ->select('user_block.id', 'staff_management.name', 'staff_management.email', 'hospital_branch__details.hospital_branch_name', 'user_block.created_at', 'user_block.block_untill')
        ->join('users', 'user_block.user_id', '=', 'users.id')
        ->join('staff_management', 'staff_management.email', '=', 'users.email')
        ->join('hospital_branch__details', 'hospital_branch__details.id', '=', 'staff_management.branch_id')
        ->get();
        return response()->json(["message" => "List Fetched Successfully", "list" => $response, "code" => 200]);
    }

    public function updateUserBlockList(request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        UserBlock::where('id',$request->id)->delete();

        return response()->json(["message" => "User Successfully Unblock", "code" => 200]);
    }


}
