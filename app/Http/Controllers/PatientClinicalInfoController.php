<?php

namespace App\Http\Controllers;

use App\Models\PatientAppointmentDetails;
use Illuminate\Http\Request;
use App\Models\PatientClinicalInfo;
use DateTime;
use DateTimeZone;
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
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
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
            'status' => "1",
            'created_at' => $date->format('Y-m-d H:i:s'),
        ];

        PatientAppointmentDetails::where(
            ['id' => $request->appointmentid]
        )->update([
            'appointment_status' =>  4,
        ]);

        PatientClinicalInfo::create($module);

        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $patient_id = PatientAppointmentDetails::where('id','=',$request->appointmentid)->first();

        $notifi=[
            'added_by' => $request->added_by,
            'staff_id' => $patient_id['assign_team'],
            'branch_id'=>$request->branch_id,
            'role'=>'',
            'patient_mrn' =>   $patient_id ['patient_mrn_id'],
            'url_route' => "/Modules/Patient/attendance-record",
            'created_at' => $date->format('Y-m-d H:i:s'),
            'message' =>  'New assigned patient',
        ];
        $HOD = Notifications::insert($notifi);

        
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
        $list = DB::table('patient_clinical_information')
            ->join('users', 'users.id', '=', 'patient_clinical_information.added_by')
            ->select('patient_clinical_information.id', 'patient_clinical_information.temperature',
            'patient_clinical_information.blood_pressure', 'patient_clinical_information.pulse_rate',
            'patient_clinical_information.weight', 'patient_clinical_information.height', 'patient_clinical_information.bmi',
            'patient_clinical_information.waist_circumference', DB::raw("DATE_FORMAT(patient_clinical_information.created_at, '%d/%m/%Y %H:%i') as date_time")
            ,'users.name')
            ->where('patient_clinical_information.patient_id', $request->patient_id)
            ->where('patient_clinical_information.status', '=', '1')
            ->orderBy('patient_clinical_information.created_at', 'desc')
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
