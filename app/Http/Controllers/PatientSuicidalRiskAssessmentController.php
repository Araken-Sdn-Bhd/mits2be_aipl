<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientSuicidalRiskAssesment;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientSuicidalRiskAssessmentController extends Controller
{
    public function store(Request $request)
    {
    if($request->Type=='Suicidal Assessment'){
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'Type' => 'required|string',
            'risk_level' => 'required|string',
            'risk' => 'required|string',
            'suicidal_intent' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module = [
            'added_by' => $request->added_by,
            'Type' => $request->Type,
            'risk_level' => $request->risk_level,
            'risk' => $request->risk,
            'suicidal_intent' => $request->suicidal_intent,
            'status' => "1"
        ];
        PatientSuicidalRiskAssesment::firstOrCreate($module);
        return response()->json(["message" => "Test Created Successfully!", "code" => 200]);
    }
} 

public function getPatientOnlineTestList(Request $request)
{
    $validator = Validator::make($request->all(), [
        'Type' => 'required|string'
    ]);
    if ($validator->fails()) {
        return response()->json(["message" => $validator->errors(), "code" => 422]);
    }
   $list =PatientSuicidalRiskAssesment::select('id','added_by','Type','status','risk_level','risk','suicidal_intent')
   ->where('status','=', '1')
   ->where('Type','=', $request->Type)
   ->get();
   return response()->json(["message" => "Test List", 'list' => $list, "code" => 200]);
}

public function getPatientOnlineTestListById(Request $request)
{
    $validator = Validator::make($request->all(), [
        'Type' => 'required|string',
        'id' => 'required|integer'
    ]);
    if ($validator->fails()) {
        return response()->json(["message" => $validator->errors(), "code" => 422]);
    }
   $list =PatientSuicidalRiskAssesment::select('id','added_by','Type','status','risk_level','risk','suicidal_intent')
   ->where('status','=', '1')
   ->where('Type','=', $request->Type)
   ->where('id','=', $request->id)
   ->get();
   return response()->json(["message" => "Test List", 'list' => $list, "code" => 200]);
}

public function remove(Request $request)
{
     $validator = Validator::make($request->all(), [
        'added_by' => 'required|integer',
        'Type' => 'required|string',
        'id' => 'required|integer'
     ]);
     if ($validator->fails()) {
         return response()->json(["message" => $validator->errors(), "code" => 422]);
     }

     PatientSuicidalRiskAssesment::where(
         ['id' => $request->id]
     )->where(['Type' => $request->Type])->update([
        'status' => "0"
     ]);

     return response()->json(["message" => "Test Deleted Successfully!", "code" => 200]);
}

public function update(Request $request)
{
if($request->Type=='Suicidal Assessment'){
     $validator = Validator::make($request->all(), [
        'added_by' => 'required|integer',
        'Type' => 'required|string',
        'risk_level' => 'required|string',
        'risk' => 'required|string',
        'suicidal_intent' => 'required|string',
        'id' => 'required|integer'
     ]);
     if ($validator->fails()) {
         return response()->json(["message" => $validator->errors(), "code" => 422]);
     }

     PatientSuicidalRiskAssesment::where(
         ['id' => $request->id]
     )->where(['Type' => $request->Type])->update([
        'added_by' => $request->added_by,
            'Type' => $request->Type,
            'risk_level' => $request->risk_level,
            'risk' => $request->risk,
            'suicidal_intent' => $request->suicidal_intent,
            'status' => "1"
     ]);

     return response()->json(["message" => "Test Updated Successfully!", "code" => 200]);
}
}

}
