<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAlert;
use Validator;
use Illuminate\Support\Facades\DB;


class PatientAlertController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'message' => 'required|string'
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
        $patient_id = $request->patient_id;
        $checkpatientid = PatientAlert::select('id')
            ->where('patient_id', $patient_id)
            ->pluck('id');

            if (count($checkpatientid) == 0) {
                
                   $alert = [
                       'added_by' =>  $request->added_by,
                       'patient_id' =>  $request->patient_id,
                       'message' =>  $request->message,
                   ];
                   try {
                       $HOD = PatientAlert::create($alert);
                   } catch (Exception $e) {
                       return response()->json(["message" => $e->getMessage(), 'Patient Alert' => $alert, "code" => 200]);
                   }
                   return response()->json(["message" => "Patient Alert Created", "code" => 200]);
            } else {
                
                $alertupdate = [
                    'added_by' => $request->added_by,
                    'patient_id' => $request->patient_id,
                    'message' => $request->message
                ];
        
                $sd = PatientAlert::where('patient_id', $request->patient_id)->update($alertupdate);
                if ($sd)
                    return response()->json(["message" => "Patient Alert Updated Successfully!", "code" => 200]);
            }
       
    }

    public function alertListbyPatientId(Request $request)
    {
        return PatientAlert::select( '*')->where('patient_id', $request->patient_id)->get();
    }

}
