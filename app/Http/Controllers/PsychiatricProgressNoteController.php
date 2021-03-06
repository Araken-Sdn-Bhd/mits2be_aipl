<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PsychiatricProgressNote;

class PsychiatricProgressNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required',
            'patient_mrn_id' => 'required|integer',
            'diagnosis' => '',
            'clinical_notes' => '',
            'management' => '',
            'location_services_id' => 'required|integer',
            'type_diagnosis_id' => 'required|integer',
            'category_services' => 'required|string',
            'complexity_services_id' => 'required|integer',
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
            
             $psychiatryprogressnote = [
             'services_id' =>  $request->services_id,
             'added_by' =>  $request->added_by,
             'patient_mrn_id' =>  $request->patient_mrn_id,
             'diagnosis' =>  $request->diagnosis,
             'clinical_notes' =>  $request->clinical_notes,
             'management' =>  $request->management,
             'location_services_id' =>  $request->location_services_id,
             'type_diagnosis_id' =>  $request->type_diagnosis_id,
             'category_services' =>  $request->category_services,
             'complexity_services_id' =>  $request->complexity_services_id,
             'outcome_id' =>  $request->outcome_id,
             'medication_des' =>  $request->medication_des,
             'status' => "1"
             ];

        try {
            $HOD = PsychiatricProgressNote::Create($psychiatryprogressnote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'psychiatryclerking' => $psychiatryprogressnote, "code" => 200]);
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

             $psychiatryprogressnote = [
             'code_id' =>  $request->code_id,
             'sub_code_id' =>  $request->sub_code_id,
             'added_by' =>  $request->added_by,
             'patient_mrn_id' =>  $request->patient_mrn_id,
             'diagnosis' =>  $request->diagnosis,
             'clinical_notes' =>  $request->clinical_notes,
             'management' =>  $request->management,
             'location_services_id' =>  $request->location_services_id,
             'type_diagnosis_id' =>  $request->type_diagnosis_id,
             'category_services' =>  $request->category_services,
             'complexity_services_id' =>  $request->complexity_services_id,
             'outcome_id' =>  $request->outcome_id,
             'medication_des' =>  $request->medication_des,
             'status' => "1"
             ];

        try {
            $HOD = PsychiatricProgressNote::Create($psychiatryprogressnote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'psychiatryprogressnote' => $psychiatryclerking, "code" => 200]);
            } 
         return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
        }

    }
}
