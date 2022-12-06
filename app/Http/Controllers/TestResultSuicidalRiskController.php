<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResultSuicidalRisk;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;
class TestResultSuicidalRiskController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'result' => 'required|string',
            'user_ip_address' => 'required' 
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $testsuicidal = [
               'added_by' =>  $request->added_by,
               'patient_id' =>  $request->patient_id,
               'result' =>  $request->result,
               'user_ip_address' =>  $request->user_ip_address,
           ];
           try {
               $HOD = TestResultSuicidalRisk::create($testsuicidal);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'Test Suicidal' => $testsuicidal, "code" => 200]);
           }
           return response()->json(["message" => "Test Suicidal Risk Submitted", "code" => 200]);
       }

       public function prepareSuicidalRiskResult($resultSet)
    {
        $value = $this->prepareCBIResult($resultSet);
        if ($value >= 0 && $value <= 4) {
            return 'Minimal Depression';
        } else if ($value >= 5 && $value <= 9) {
            return 'Mild Depression';
        } else if ($value >= 10 && $value <= 14) {
            return 'Moderate Depression';
        } else if ($value >= 15 && $value <= 19) {
            return 'Moderately severe depression';
        } else {
            return 'Severe Depression';
        }
    }

}
