<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExternalReferralForm;
use Validator;

class ExternalReferralFormController extends Controller
{
    //
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_mrn_id' => 'required|integer',
             'history' => 'required|string',
             'examination' => 'required|string',
             'diagnosis' => 'required|string',
             'result_of_investigation' => '',
             'treatment' => '',
             'purpose_of_referral' => '',
 
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'name' => 'required|string',
             'designation' => 'required|string',
             'hospital' => 'required|string',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
        
            $externalform = [
            'added_by' => $request->added_by,
            'patient_mrn_id' => $request->patient_mrn_id,

            'history' => $request->history,
            'examination' => $request->examination,
 
            'diagnosis' => $request->diagnosis,
            'result_of_investigation' => $request->result_of_investigation,
            'treatment' => $request->treatment,
            'purpose_of_referral' => $request->purpose_of_referral,
           
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'name' => $request->name,
            'designation' => $request->designation,
            'hospital' => $request->hospital,
            'status' => "1"
            ];
 
            $validateExternalForm = [];
 
         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateExternalForm['services_id'] = 'required';
             $externalform['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateExternalForm['code_id'] = 'required';
             $externalform['code_id'] =  $request->code_id;
             $validateExternalForm['sub_code_id'] = 'required';
             $externalform['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateExternalForm);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
         ExternalReferralForm::firstOrCreate($externalform);  
         return response()->json(["message" => "External Referral Form Created Successfully!", "code" => 200]);
        
    }
}
