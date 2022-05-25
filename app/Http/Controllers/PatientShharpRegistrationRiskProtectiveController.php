<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationRiskProtective;

class PatientShharpRegistrationRiskProtectiveController extends Controller
{
    public function generateQuestion(Request $request){
        $validator = Validator::make($request->all(), [
               'added_by' => 'required|integer',
               'Question' => 'required|string',
               'Type' => 'required|string',
               'Options1' => 'required|string',
               'Options2' => 'required|string' 
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           if($request->Type=='Risk Factors'){
            $module = [
                'added_by' => $request->added_by,
                'Question' => $request->Question,
                'Options1' => $request->Options1,
                'Options2' => $request->Options2,
                'Type' => $request->Type,
                'status' => "1"
            ];
            PatientShharpRegistrationRiskProtective::firstOrCreate($module);
            return response()->json(["message" => "Risk Factor Created Successfully!", "code" => 200]);
    }
    else if($request->Type=='Protective Factors'){
        $module = [
            'added_by' => $request->added_by,
            'Question' => $request->Question,
            'Options1' => $request->Options1,
            'Options2' => $request->Options2,
            'Type' => $request->Type,
            'status' => "1"
        ];
        PatientShharpRegistrationRiskProtective::firstOrCreate($module);
        return response()->json(["message" => "Risk Factor Created Successfully!", "code" => 200]);
}
        else{
           
            return response()->json(["message" => "Type Not Matches!", "code" => 200]);
        }
       }

       public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
       $list =PatientShharpRegistrationRiskProtective::select('id','added_by','Type','status','Question','Options1','Options2','status')
       ->where('status','=', '1')
       ->where('Type','=', $request->Type)
       ->get();
       return response()->json(["message" => "Test List", 'list' => $list, "code" => 200]);
    }
}
