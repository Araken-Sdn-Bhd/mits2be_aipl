<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PatientCounsellorClerkingNotes;
use DateTime;
use DateTimeZone;
use App\Models\UserDiagnosis;

class PatientCounsellorClerkingNotesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'patient_mrn_id' => 'required|integer',
            'appId' => '',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($request->status == "0") {
            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'appointment_details_id' => $request->appId,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'clinical_summary' =>  $request->clinical_summary,
                    'background_history' =>  $request->background_history,
                    'clinical_notes' =>  $request->clinical_notes,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'management' =>  $request->management,
                    'location_services_id' =>  $request->location_services_id,
                    'type_diagnosis_id' =>  $request->type_diagnosis_id,
                    'category_services' =>  $request->category_services,
                    'additional_diagnosis' => $additional_diagnosis,
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "0",
                    'created_at' => $date->format('Y-m-d H:i:s'),

                ];
                if($request->id) {
                    PatientCounsellorClerkingNotes::where(
                                ['id' => $request->id]
                            )->update($psychiatryclerking);
                            return response()->json(["message" => "Counselling Clerking Note updated successfully", "code" => 200]);
                } else {
                    PatientCounsellorClerkingNotes::create($psychiatryclerking);
                    return response()->json(["message" => "Counselling Clerking Note created successfully", "code" => 200]);
                }
            } else if ($request->category_services == 'clinical-work') {
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'clinical_summary' =>  $request->clinical_summary,
                    'background_history' =>  $request->background_history,
                    'clinical_notes' =>  $request->clinical_notes,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'management' =>  $request->management,
                    'location_services_id' =>  $request->location_services_id,
                    'type_diagnosis_id' =>  $request->type_diagnosis_id,
                    'category_services' =>  $request->category_services,
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_subcode' => $additional_subcode,
                    'additional_code_id' => $request->additional_code_id,
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "0",
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'appointment_details_id' => $request->appId,
                ];

                if($request->id) {
                    PatientCounsellorClerkingNotes::where(
                                ['id' => $request->id]
                            )->update($psychiatryclerking);
                            return response()->json(["message" => "Counselling Clerking Note updated successfully", "code" => 200]);
                } else {
                    PatientCounsellorClerkingNotes::create($psychiatryclerking);
                    return response()->json(["message" => "Counselling Clerking Note created successfully", "code" => 200]);
                }
            }
        }
        else if ($request->status == "1") {
            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'appointment_details_id' => $request->appId,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'clinical_summary' =>  $request->clinical_summary,
                    'background_history' =>  $request->background_history,
                    'clinical_notes' =>  $request->clinical_notes,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'management' =>  $request->management,
                    'location_services_id' =>  $request->location_services_id,
                    'type_diagnosis_id' =>  $request->type_diagnosis_id,
                    'category_services' =>  $request->category_services,
                    'additional_diagnosis' => $additional_diagnosis,
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s'),

                ];
                $user_diagnosis = [
                    'app_id' => $request->appId,
                    'patient_id' =>  $request->patient_mrn_id,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'add_diagnosis_id' => $additional_diagnosis,
                    'outcome_id' =>  $request->outcome_id,
                    'category_services' =>  $request->category_services,
                    'remarks' => 'patient_counsellor_clerking_notes',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                UserDiagnosis::create($user_diagnosis);
                if($request->id) {
                    PatientCounsellorClerkingNotes::where(
                                ['id' => $request->id]
                            )->update($psychiatryclerking);
                            return response()->json(["message" => "Counselling Clerking Note updated successfully", "code" => 200]);
                } else {
                    PatientCounsellorClerkingNotes::create($psychiatryclerking);
                    return response()->json(["message" => "Counselling Clerking Note created successfully", "code" => 200]);
                }
            } else if ($request->category_services == 'clinical-work') {

                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'clinical_summary' =>  $request->clinical_summary,
                    'background_history' =>  $request->background_history,
                    'clinical_notes' =>  $request->clinical_notes,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'management' =>  $request->management,
                    'location_services_id' =>  $request->location_services_id,
                    'type_diagnosis_id' =>  $request->type_diagnosis_id,
                    'category_services' =>  $request->category_services,
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_subcode' => $additional_subcode,
                    'additional_code_id' => $request->additional_code_id,
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'appointment_details_id' => $request->appId,
                ];
                
                $user_diagnosis = [
                    'app_id' => $request->appId,
                    'patient_id' =>  $request->patient_mrn_id,
                    'diagnosis_id' =>  $request->diagnosis_id,
                    'add_diagnosis_id' => $additional_diagnosis,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
                    'add_code_id'=> $request->additional_code_id,
                    'add_sub_code_id' => $additional_subcode,
                    'outcome_id' =>  $request->outcome_id,
                    'category_services' =>  $request->category_services,
                    'remarks' => 'patient_counsellor_clerking_notes',
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                UserDiagnosis::create($user_diagnosis);
                if($request->id) {
                    PatientCounsellorClerkingNotes::where(
                                ['id' => $request->id]
                            )->update($psychiatryclerking);
                            return response()->json(["message" => "Counselling Clerking Note updated successfully", "code" => 200]);
                } else {
                    PatientCounsellorClerkingNotes::create($psychiatryclerking);
                    return response()->json(["message" => "Counselling Clerking Note created successfully", "code" => 200]);
                }
            }
        }
    }
}

