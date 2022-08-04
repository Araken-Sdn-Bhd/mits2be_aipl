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
    public function getCitizenshipListById(Request $request)
    {
       $list =Citizenship::select('id', 'citizenship_name')
       ->where('citizenship_status','=', '1')
       ->where('id','=', $request->id)
       ->get();
       return response()->json(["message" => "Citizenship List", 'list' => $list, "code" => 200]);
    }
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        Citizenship::where(
            ['id' => $request->id]
        )->update([
            'citizenship_status' => '0',
            'added_by' => $request->added_by
        ]);
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
       
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'citizenship_name' => 'required|string', 
            'citizenship_order' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        Citizenship::where(
            ['id' => $request->id]
        )->update([
            'citizenship_name' => $request->citizenship_name,
            'citizenship_order' => $request->citizenship_order
        ]);
        return response()->json(["message" => "Updated Successfully.", "code" => 200]);
       
    }
}
