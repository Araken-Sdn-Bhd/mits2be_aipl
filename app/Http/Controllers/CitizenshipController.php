<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Citizenship;
use Validator;
use Illuminate\Support\Facades\DB;

class CitizenshipController extends Controller
{
    public function addCitizenship(Request $request){
     $validator = Validator::make($request->all(), [
            'citizenship_name' => 'required|string|unique:citizenship'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $citizenship = [
            'citizenship_name' =>  $request->citizenship_name,
        ];
        try {
            $HOD = Citizenship::firstOrCreate($citizenship);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Citizenship' => $citizenship, "code" => 200]);
        }
        return response()->json(["message" => "Citizenship Created", "code" => 200]);
    }

    public function getCitizenshipList()
    {
       $list =Citizenship::select('id', 'citizenship_name')
       ->where('citizenship_status','=', '1')
       ->get();
       return response()->json(["message" => "Citizenship List", 'list' => $list, "code" => 200]);
    }
}
