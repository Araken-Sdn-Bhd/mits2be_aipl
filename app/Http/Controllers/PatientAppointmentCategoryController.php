<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAppointmentCategory;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientAppointmentCategoryController extends Controller
{
     public function addPatientCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_category_name' => 'required|string|unique:patient_appointment_category'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $designation = [
            'appointment_category_name' =>  $request->appointment_category_name,
        ];
        try {
            $HOD = PatientAppointmentCategory::firstOrCreate($designation);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Patient Appointment Category' => $designation, "code" => 200]);
        }
        return response()->json(["message" => "Patient Appointment Category Created", "code" => 200]);
    }

    public function getAppointmentPatientCategoryList()
    {
       $list =PatientAppointmentCategory::select('id', 'appointment_category_name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Patient Appointment Category List", 'list' => $list, "code" => 200]);
    }
}
