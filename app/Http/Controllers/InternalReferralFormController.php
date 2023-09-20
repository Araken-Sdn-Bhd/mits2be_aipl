<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalReferralForm;
use Validator;
use App\Models\UserDiagnosis;

class InternalReferralFormController extends Controller
{
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_mrn_id' => 'required|integer',
             'appId' => ''
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }

         if ($request->status == "1"){

            $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
            $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
            $sub_code_id=str_replace('"','',$request->sub_code_id);

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

            'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
            'add_sub_code_id' => $additional_sub_code_id, //newly added
            'add_code_id' => $request->additional_code_id, //newly added

            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id'=> $request->appId,
            ];

            $validateInternalForm = [];

         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateInternalForm['services_id'] = 'required';
             $internalform['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateInternalForm['code_id'] = 'required';
             $internalform['code_id'] =  $request->code_id;
             $validateInternalForm['sub_code_id'] = 'required';
             $internalform['sub_code_id'] =  $sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateInternalForm);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
         $user_diagnosis = [
            'app_id' => $request->appId,
            'patient_id' =>  $request->patient_mrn_id,
            'diagnosis_id' =>  $request->diagnosis,
            'add_diagnosis_id' => $additional_diagnosis,
            'code_id' =>  $request->code_id,
            'sub_code_id' =>  $sub_code_id,
            'add_code_id'=> $request-> additional_code_id,
            'add_sub_code_id' => $additional_sub_code_id,
            'outcome_id' =>  $request->outcome,
            'category_services' =>  $request->category_services,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        UserDiagnosis::create($user_diagnosis);
         InternalReferralForm::where('id',$request->pid)->update($internalform);
         return response()->json(["message" => "Internal Referral Form Created Successfully!", "code" => 200]);
        } else if ($request->status == "0"){
            if($request->pid!=0){
                $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
                $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
                $sub_code_id=str_replace('"','',$request->sub_code_id);
    
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
    
                    'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
                    'add_sub_code_id' => $additional_sub_code_id, //newly added
                    'add_code_id' => $request->additional_code_id, //newly added
    
                    'location_services' => $request->location_services,
                    'services_id' => $request->services_id,
                    'code_id' => $request->code_id,
                    'sub_code_id' => $sub_code_id,
                    'type_diagnosis_id' => $request->type_diagnosis_id,
                    'category_services' => $request->category_services,
                    'complexity_services' => $request->complexity_services,
                    'outcome' => $request->outcome,
                    'medication_des' => $request->medication_des,
                    'status' => "0",
                    'appointment_details_id'=> $request->appId,
                    ];
    
                    $validateInternalForm = [];
    
                 if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                     $validateInternalForm['services_id'] = 'required';
                     $internalform['services_id'] =  $request->services_id;
                 } else if ($request->category_services == 'clinical-work') {
                     $validateInternalForm['code_id'] = 'required';
                     $internalform['code_id'] =  $request->code_id;
                     $validateInternalForm['sub_code_id'] = 'required';
                     $internalform['sub_code_id'] =  $sub_code_id;
                 }
                 $validator = Validator::make($request->all(), $validateInternalForm);
                 if ($validator->fails()) {
                     return response()->json(["message" => $validator->errors(), "code" => 422]);
                 }
    
                 InternalReferralForm::where('id',$request->pid)->update($internalform);
                 return response()->json(["message" => "Internal Referral Form Created Successfully!", "code" => 200]);
            }else{
                $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
                $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
                $sub_code_id=str_replace('"','',$request->sub_code_id);
    
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
    
                    'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
                    'add_sub_code_id' => $additional_sub_code_id, //newly added
                    'add_code_id' => $request->additional_code_id, //newly added
    
                    'location_services' => $request->location_services,
                    'services_id' => $request->services_id,
                    'code_id' => $request->code_id,
                    'sub_code_id' => $sub_code_id,
                    'type_diagnosis_id' => $request->type_diagnosis_id,
                    'category_services' => $request->category_services,
                    'complexity_services' => $request->complexity_services,
                    'outcome' => $request->outcome,
                    'medication_des' => $request->medication_des,
                    'status' => "0",
                    'appointment_details_id'=> $request->appId,
                    ];
    
                    $validateInternalForm = [];
    
                 if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                     $validateInternalForm['services_id'] = 'required';
                     $internalform['services_id'] =  $request->services_id;
                 } else if ($request->category_services == 'clinical-work') {
                     $validateInternalForm['code_id'] = 'required';
                     $internalform['code_id'] =  $request->code_id;
                     $validateInternalForm['sub_code_id'] = 'required';
                     $internalform['sub_code_id'] =  $sub_code_id;
                 }
                 $validator = Validator::make($request->all(), $validateInternalForm);
                 if ($validator->fails()) {
                     return response()->json(["message" => $validator->errors(), "code" => 422]);
                 }
    
                 InternalReferralForm::firstOrCreate($internalform);
                 return response()->json(["message" => "Internal Referral Form Created Successfully!", "code" => 200]);
            }


        }
    }
}
