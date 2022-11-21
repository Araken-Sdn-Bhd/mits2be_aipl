<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PatientCounsellorClerkingNotes;
use DateTime;
use DateTimeZone;

class PatientCounsellorClerkingNotesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'diagnosis_id' => 'required|integer',
            'patient_mrn_id' => 'required|integer',
            'clinical_summary' => '',
            'background_history' => '',
            'clinical_notes' => '',
            'management' => '',
            'location_services_id' => 'required|integer',
            'type_diagnosis_id' => 'required|integer',
            'category_services' => 'required|string',
            'complexity_services_id' => '',
            'outcome_id' => '',
            'medication_des' => '',
            'id' => '',
            'appointment_details_id' => '',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if($request->id){

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
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s')
                ];
    
                try {
                    PatientCounsellorClerkingNotes::where(
                        ['id' => $request->id]
                    )->update($psychiatryclerking);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Counsellclerking' => $psychiatryclerking, "code" => 200]);
                }
                return response()->json(["message" => "Counsellclerking clerking Successfully00", "code" => 200]);
            } else if ($request->category_services == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'appointment_details_id' => $request->appId,
                    
                ];
    
                try {
                    PatientCounsellorClerkingNotes::where(
                        ['id' => $request->id]
                    )->update($psychiatryclerking);
                    // $HOD = PatientCounsellorClerkingNotes::firstOrCreate($psychiatryclerking);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'counsellclerking' => $psychiatryclerking, "code" => 200]);
                }
                return response()->json(["message" => "Counsellclerking clerking Successfully11", "code" => 200]);
            }

        }else{
        
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
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    
                ];
    
                try {
                   
                    $HOD = PatientCounsellorClerkingNotes::Create($psychiatryclerking);
                   
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Counsellclerking' => $psychiatryclerking, "code" => 200]);
                }
                return response()->json(["message" => "Counsellclerking clerking Successfully00", "code" => 200]);
            } else if ($request->category_services == 'clinical-work') {
               
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $psychiatryclerking = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                    'complexity_services_id' =>  $request->complexity_services_id,
                    'outcome_id' =>  $request->outcome_id,
                    'medication_des' =>  $request->medication_des,
                    'status' => "1",
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'appointment_details_id' => $request->appId,
                ];
    
                try {
                    $HOD = PatientCounsellorClerkingNotes::firstOrCreate($psychiatryclerking);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'counsellclerking' => $psychiatryclerking, "code" => 200]);
                }
                return response()->json(["message" => "Counsellclerking clerking Successfully11", "code" => 200]);
            }
        }

      
    }
}
