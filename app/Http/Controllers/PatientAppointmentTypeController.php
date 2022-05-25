<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAppointmentType;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientAppointmentTypeController extends Controller
{
     public function addPatientType(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_type_name' => 'required|string|unique:patient_appointment_type'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $designation = [
            'appointment_type_name' =>  $request->appointment_type_name,
        ];
        try {
            $HOD = PatientAppointmentType::firstOrCreate($designation);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Patient Appointment Type' => $designation, "code" => 200]);
        }
        return response()->json(["message" => "Patient Appointment Type Created", "code" => 200]);
    }

    public function getAppointmentPatientTypeList()
    {
       $list =PatientAppointmentType::select('id', 'appointment_type_name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Patient Appointment Type List", 'list' => $list, "code" => 200]);
    }
}
