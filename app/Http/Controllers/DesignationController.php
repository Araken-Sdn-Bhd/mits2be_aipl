<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use Validator;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    public function addDesignation(Request $request){
        $validator = Validator::make($request->all(), [
            'designation_name' => 'required|string|unique:designation'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $designation = [
            'designation_name' =>  $request->designation_name,
        ];
        try {
            $HOD = Designation::firstOrCreate($designation);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Designation' => $designation, "code" => 200]);
        }
        return response()->json(["message" => "Designation Created", "code" => 200]);
    }

    public function getDesignationList()
    {
       $list =Designation::select('id', 'designation_name')
       ->where('designation_status','=', '1')
       ->get();
       return response()->json(["message" => "Designation List", 'list' => $list, "code" => 200]);
    }
}
