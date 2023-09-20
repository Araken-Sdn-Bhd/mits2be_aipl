<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CounsellingProgressNote;
use App\Models\UserDiagnosis;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CounsellingProgressNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required',
            'id' => ''
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($request->status == "0") {
            $counsellingprogess = [
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'therapy_date' =>  $request->therapy_date,
                'therapy_time' =>  $request->therapy_time,
                'diagnosis_id' =>  $request->diagnosis_id,
                'frequency_session' =>  $request->frequency_session,
                'frequency_session_other' =>  $request->frequency_session_other,
                'model_therapy' =>  $request->model_therapy,
                'model_therapy_other' =>  $request->model_therapy_other,
                'mode_therapy' =>  $request->mode_therapy,
                'mode_therapy_other' =>  $request->mode_therapy_other,
                'comment_therapy_session' =>  $request->comment_therapy_session,
                'patent_condition' =>  $request->patent_condition,
                'patent_condition_other' =>  $request->patent_condition_other,
                'comment_patent_condition' =>  $request->comment_patent_condition,
                'session_details' =>  $request->session_details,
                'session_issues' =>  $request->session_issues,
                'conduct_session' =>  $request->conduct_session,
                'outcome_session' =>  $request->outcome_session,
                'transference_session' =>  $request->transference_session,
                'duration_session' =>  $request->duration_session,
                'other_comment_session' =>  $request->other_comment_session,
                'name' =>  $request->name,
                'designation' => $request->designation,
                'date_session' => $request->date_session,
                'location_services_id' => $request->location_services_id,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services_id' => $request->complexity_services_id,
                'outcome_id' => $request->outcome_id,
                'medication_des' => $request->medication_des,
                'status' => "0",
                'appointment_details_id' => $request->appId,
                'additional_code_id' => $request->additional_code_id,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_subcode' => $additional_subcode,
            ];


            $validateCounsellingprogress = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateCounsellingprogress['services_id'] = '';
                $counsellingprogess['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateCounsellingprogress['code_id'] = '';
                $counsellingprogess['code_id'] =  $request->code_id;
                $validateCounsellingprogress['sub_code_id'] = '';
                $counsellingprogess['sub_code_id'] =  $sub_code_id;
            }

            if ($request->id) {
                CounsellingProgressNote::where(
                    ['id' => $request->id]
                )->update($counsellingprogess);
                return response()->json(["message" => "Counselling Progress Note updated successfully!", "code" => 200]);
            } else {
                CounsellingProgressNote::create($counsellingprogess);
                return response()->json(["message" => "Counselling Progress Note successfully created!", "code" => 200]);
            }
        } else if ($request->status == "1") {
            $counsellingprogess = [
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'therapy_date' =>  $request->therapy_date,
                'therapy_time' =>  $request->therapy_time,
                'diagnosis_id' =>  $request->diagnosis_id,
                'frequency_session' =>  $request->frequency_session,
                'frequency_session_other' =>  $request->frequency_session_other,
                'model_therapy' =>  $request->model_therapy,
                'model_therapy_other' =>  $request->model_therapy_other,
                'mode_therapy' =>  $request->mode_therapy,
                'mode_therapy_other' =>  $request->mode_therapy_other,
                'comment_therapy_session' =>  $request->comment_therapy_session,
                'patent_condition' =>  $request->patent_condition,
                'patent_condition_other' =>  $request->patent_condition_other,
                'comment_patent_condition' =>  $request->comment_patent_condition,
                'session_details' =>  $request->session_details,
                'session_issues' =>  $request->session_issues,
                'conduct_session' =>  $request->conduct_session,
                'outcome_session' =>  $request->outcome_session,
                'transference_session' =>  $request->transference_session,
                'duration_session' =>  $request->duration_session,
                'other_comment_session' =>  $request->other_comment_session,
                'name' =>  $request->name,
                'designation' => $request->designation,
                'date_session' => $request->date_session,
                'location_services_id' => $request->location_services_id,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services_id' => $request->complexity_services_id,
                'outcome_id' => $request->outcome_id,
                'medication_des' => $request->medication_des,
                'status' => "1",
                'appointment_details_id' => $request->appId,
                'additional_code_id' => $request->additional_code_id,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_subcode' => $additional_subcode,
            ];


            $validateCounsellingprogress = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateCounsellingprogress['services_id'] = 'required';
                $counsellingprogess['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateCounsellingprogress['code_id'] = 'required';
                $counsellingprogess['code_id'] =  $request->code_id;
                $validateCounsellingprogress['sub_code_id'] = 'required';
                $counsellingprogess['sub_code_id'] =  $sub_code_id;
            }
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
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserDiagnosis::create($user_diagnosis);
            if($request->id) {
                CounsellingProgressNote::where(
                            ['id' => $request->id]
                        )->update($counsellingprogess);
                        return response()->json(["message" => "Psychiatry progress note updated", "code" => 200]);
            } else {
                CounsellingProgressNote::create($counsellingprogess);
                return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
        }
        }
    }
}
