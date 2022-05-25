<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientClinicalInfo;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientClinicalInfoController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'patient_mrn_id' => 'required|integer',
            'temperature' => 'required|string',
            'blood_pressure' => 'required|string',
            'pulse_rate' => 'required|string',
            'weight' => 'required|string',
            'height' => 'required|string',
            'bmi' => 'required|string',
            'waist_circumference' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'patient_mrn_id' => $request->patient_mrn_id,
            'temperature' => $request->temperature,
            'blood_pressure' => $request->blood_pressure,
            'pulse_rate' => $request->pulse_rate,
            'weight' => $request->weight,
            'height' => $request->height,
            'bmi' => $request->bmi,
            'waist_circumference' => $request->waist_circumference,
            'status' => "1"
        ];
        PatientClinicalInfo::create($module);
        return response()->json(["message" => "Patient Clinical Information Created Successfully!", "code" => 200]);
    }

    public function getPatientClinicalList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientClinicalInfo::select('id', 'temperature', 'blood_pressure', 'pulse_rate', 'weight', 'height', 'bmi', 'waist_circumference', DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as date_time"))
            ->where('patient_id', $request->patient_id)
            ->where('status', '=', '1')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(["message" => "Patient Clinical Information List", 'list' => $list, "code" => 200]);
    }
    public function getPatientClinicalListOfPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientClinicalInfo::select('id', 'temperature', 'blood_pressure', 'pulse_rate', 'weight', 'height', 'bmi', 'waist_circumference',  DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as date_time"))
            ->where('patient_id', $request->patient_id)
            ->where('status', '=', '1')
            ->orderBy('created_at', 'desc')
            ->get()->first();
        return response()->json(["message" => "Patient Clinical Information List", 'list' => $list, "code" => 200]);
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        PatientClinicalInfo::where(
            ['id' => $request->id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Patient Clinical Info Deleted Successfully!", "code" => 200]);
    }
}
