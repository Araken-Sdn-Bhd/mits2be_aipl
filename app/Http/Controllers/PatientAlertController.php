<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAlert;
use Exception;
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
        $alert_id = $request->alert_id;
        $checkpatientid = PatientAlert::select('id')
            ->where('patient_id', $patient_id)
            ->pluck('id');
            if ($request->alert_id==0) {
                
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
        
                $sd = PatientAlert::where('id', $request->alert_id)->where('patient_id', $request->patient_id)->update($alertupdate);
                if ($sd)
                    return response()->json(["message" => "Patient Alert Updated Successfully!", "code" => 200]);
            }
       
    }

    public function alertListbyPatientId(Request $request)
    {
        $users = DB::table('patient_alert')
            ->join('users', 'patient_alert.added_by', '=', 'users.id')
            ->select('patient_alert.id','patient_alert.message',DB::raw("DATE_FORMAT(patient_alert.created_at, '%d-%m-%Y') as created"),DB::raw("DATE_FORMAT(patient_alert.updated_at, '%d-%m-%Y') as updated"),'users.name')
            ->where('patient_alert.patient_id', $request->patient_id)
            ->get();
        return response()->json(["message" => "Alert List", "list" => $users,  "code" => 200]);
    }

    public function alertListbyAlertId(Request $request)
    {
        return PatientAlert::select( 'message',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as created"))->where('id', $request->alert_id)->get();
    }
    public function resolved(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alert_id' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        PatientAlert::where(
            ['id' => $request->alert_id]
        )->update([
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return response()->json(["message" => "Resolved Successfully.", "code" => 200]);
       
    }

    public function alertLastbyPatientId(Request $request)
    {
        $users = DB::table('patient_alert')
            ->join('users', 'patient_alert.added_by', '=', 'users.id')
            ->select('patient_alert.id','patient_alert.message',DB::raw("DATE_FORMAT(patient_alert.created_at, '%d-%m-%Y') as created"),DB::raw("DATE_FORMAT(patient_alert.updated_at, '%d-%m-%Y') as updated"),'users.name')
            ->where('patient_alert.patient_id', $request->patient_id)
            ->orderBy('patient_alert.created_at', 'desc')->limit(1)
            ->get();
        return response()->json(["message" => "Alert List", "list" => $users,  "code" => 200]);
    }

}
