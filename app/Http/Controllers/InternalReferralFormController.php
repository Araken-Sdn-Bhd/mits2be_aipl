<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalReferralForm;
use Validator;

class InternalReferralFormController extends Controller
{
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_mrn_id' => 'required|integer',
             'diagnosis' => 'required|string',
             'reason_for_referral' => '',
             'summary' => '',
             'management' => '',
             'medication' => '',
             'name' => '',
             'designation' => 'required|string',
             'hospital' => '',
 
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => ''
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
        
            $internalform = [
            'added_by' => $request->added_by,
            'patient_mrn_id' => $request->patient_mrn_id,
 
            'diagnosis' => $request->diagnosis,
            'reason_for_referral' => $request->reason_for_referral,
            'summary' => $request->summary,
            'management' => $request->management,
            'medication' => $request->medication,
            'name' => $request->name,
            'designation' => $request->designation,
            'hospital' => $request->hospital,
           
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1"
            ];
 
            $validateInternalForm = [];
 
         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateInternalForm['services_id'] = 'required';
             $internalform['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateInternalForm['code_id'] = 'required';
             $internalform['code_id'] =  $request->code_id;
             $validateInternalForm['sub_code_id'] = 'required';
             $internalform['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateInternalForm);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
         InternalReferralForm::firstOrCreate($internalform);  
         return response()->json(["message" => "Internal Referral Form Created Successfully!", "code" => 200]);
        
    }
}
