<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PsychiatryClerkingNote;

class PsychiatryClerkingNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'chief_complain' => '',
            'patient_mrn_id' => 'required|integer',
            'presenting_illness' => '',
            'background_history' => '',
            'general_examination' => '',
            'mental_state_examination' => '',
            'diagnosis_id' => 'required',
            'management' => '',
            'discuss_psychiatrist_name' => '',
            'date' => 'required',
            'time' => 'required',
            'location_services_id' => 'required|integer',
            'type_diagnosis_id' => 'required|integer',
            'category_services' => 'required|string',
            'complexity_services_id' => '',
            'outcome_id' => '',
            'medication_des' => ''
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->category_services=='assisstance'|| $request->category_services=='external')
        {
            $validator = Validator::make($request->all(), [
            'services_id' => 'required'
            ]);
             if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            
             $psychiatryclerking = [
             'services_id' =>  $request->services_id,
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
             'status' => "1"
             ];

        try {
            $HOD = PsychiatryClerkingNote::firstOrCreate($psychiatryclerking);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'psychiatryclerking' => $psychiatryclerking, "code" => 200]);
            } 
         return response()->json(["message" => "Psychiatry clerking Successfully00", "code" => 200]);
        }

       else if ($request->category_services=='clinical-work')
        {
            $validator = Validator::make($request->all(), [
            'code_id' => 'required|integer',
            'sub_code_id' => 'required|integer'
            ]);
             if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

             $psychiatryclerking = [
             'services_id' =>  $request->services_id,
             'code_id' =>  $request->code_id,
             'sub_code_id' =>  $request->sub_code_id,
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
             'status' => "1"
             ];

        try {
            $HOD = PsychiatryClerkingNote::firstOrCreate($psychiatryclerking);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'psychiatryclerking' => $psychiatryclerking, "code" => 200]);
            } 
         return response()->json(["message" => "Psychiatry clerking Successfully11", "code" => 200]);
        }

    }
    
}
