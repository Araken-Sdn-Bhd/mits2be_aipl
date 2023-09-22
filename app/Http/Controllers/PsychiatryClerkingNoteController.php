<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PsychiatryClerkingNote;
use App\Models\UserDiagnosis;
use DateTime;
use DateTimeZone;

class PsychiatryClerkingNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'appointment_details_id' => '',
        ]);

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->status == "1") {
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $psychiatryclerking = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'chief_complain' =>  $request->chief_complain,
                'presenting_illness' =>  $request->presenting_illness,
                'background_history' =>  $request->background_history,
                'general_examination' =>  $request->general_examination,
                'mental_state_examination' =>  $request->mental_state_examination,
                'diagnosis_id' =>  $request->diagnosis_id,
                'management' =>  $request->management,
                'discuss_psychiatrist_name' =>  $request->discuss_psychiatrist_name,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'location_services_id' =>  $request->location_services_id,
                'type_diagnosis_id' =>  $request->type_diagnosis_id,
                'category_services' =>  $request->category_services,
                'complexity_services_id' =>  $request->complexity_services_id,
                'outcome_id' =>  $request->outcome_id,
                'medication_des' =>  $request->medication_des,
                'status' => "1",
                'created_at' => date('Y-m-d H:i:s'),
                'appointment_details_id' => $request->appId,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_code_id'=> $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
            ];

            $validatePsychiatricclerking = [];

                    if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                        $validatePsychiatricclerking['services_id'] = 'required';
                        $psychiatryclerking['services_id'] =  $request->services_id;
                    } else if ($request->category_services == 'clinical-work') {
                        $validatePsychiatricclerking['code_id'] = 'required';
                        $psychiatryclerking['code_id'] =  $request->code_id;
                        $validatePsychiatricclerking['sub_code_id'] = 'required';
                        $psychiatryclerking['sub_code_id'] =  $sub_code_id;
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
                        'remarks' => 'psychiatry_clerking_note',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    UserDiagnosis::create($user_diagnosis);

            if ($request->id){
                PsychiatryClerkingNote::where(
                    ['id' => $request->id]
                )->update($psychiatryclerking);
                return response()->json(["message" => "Psychiatry clerking note updated", "code" => 200]);
            }else {
                PsychiatryClerkingNote::create($psychiatryclerking);
                return response()->json(["message" => "Psychiatry clerking note created", "code" => 200]);
            }
        } else if ($request->status == "0") {
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $psychiatryclerking = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'chief_complain' =>  $request->chief_complain,
                'presenting_illness' =>  $request->presenting_illness,
                'background_history' =>  $request->background_history,
                'general_examination' =>  $request->general_examination,
                'mental_state_examination' =>  $request->mental_state_examination,
                'diagnosis_id' =>  $request->diagnosis_id,
                'management' =>  $request->management,
                'discuss_psychiatrist_name' =>  $request->discuss_psychiatrist_name,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'location_services_id' =>  $request->location_services_id,
                'type_diagnosis_id' =>  $request->type_diagnosis_id,
                'category_services' =>  $request->category_services,
                'complexity_services_id' =>  $request->complexity_services_id,
                'outcome_id' =>  $request->outcome_id,
                'medication_des' =>  $request->medication_des,
                'status' => "0",
                'created_at' => date('Y-m-d H:i:s'),
                'appointment_details_id' => $request->appId,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_code_id'=> $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
            ];

            if ($request->id){
                PsychiatryClerkingNote::where(
                    ['id' => $request->id]
                )->update($psychiatryclerking);
                return response()->json(["message" => "Psychiatry clerking note updated", "code" => 200]);
            }else {
                PsychiatryClerkingNote::create($psychiatryclerking);
                return response()->json(["message" => "Psychiatry clerking note created", "code" => 200]);
            }
        }
    }
}
