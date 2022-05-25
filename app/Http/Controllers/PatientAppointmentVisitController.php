<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAppointmentVisit;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientAppointmentVisitController extends Controller
{
     public function addPatientVisit(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_visit_name' => 'required|string|unique:patient_appointment_visit'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $designation = [
            'appointment_visit_name' =>  $request->appointment_visit_name,
        ];
        try {
            $HOD = PatientAppointmentVisit::firstOrCreate($designation);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Patient Appointment Visit' => $designation, "code" => 200]);
        }
        return response()->json(["message" => "Patient Appointment Visit Created", "code" => 200]);
    }

    public function getAppointmentPatientVisitList()
    {
       $list =PatientAppointmentVisit::select('id', 'appointment_visit_name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Patient Appointment Visit List", 'list' => $list, "code" => 200]);
    }
}
