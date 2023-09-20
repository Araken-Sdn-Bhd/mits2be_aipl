<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occt_Referral_Form;
use Validator;
use App\Models\UserDiagnosis;

class OcctReferralFormController extends Controller
{
    public function store(Request $request)
    {
    if ($request->status=='0') {
        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);
        $occtform = [
            'added_by' => $request->added_by,
            'patient_mrn_id' => $request->patient_mrn_id,
            'referral_location' => $request->referral_location,
            'date' => $request->date,
            'diagnosis_id' => $request->diagnosis_id,
            'referral_clinical_assessment' => $request->referral_clinical_assessment,
            'referral_clinical_assessment_other' => $request->referral_clinical_assessment_other,
            'referral_clinical_intervention' => $request->referral_clinical_intervention,
            'referral_clinical_intervention_other' => $request->referral_clinical_intervention_other,
            'referral_clinical_promotive_program' => $request->referral_clinical_promotive_program,
            'referral_name' => $request->referral_name,
            'referral_designation' => $request->referral_designation,
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,

            'sub_code_id' => $sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
            'add_sub_code_id' => $additional_sub_code_id, //newly added
            'add_code_id' => $request->additional_code_id, //newly added

            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "0",
            'appointment_details_id' => $request->appId,
            ];
        if($request->id){
            Occt_Referral_Form::where(['id' => $request->id])->update($occtform);
            return response()->json(["message" => "Successfully updated", "code" => 200]);
        }else{
            $HOD=Occt_Referral_Form::create($occtform);

        return response()->json(["message" => "Created", "code" => 200]);
        }
    } elseif ($request->status=='1') {
        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_mrn_id' => 'required|integer',
            'referral_location' => 'required|string',
            'date' => '',
            'diagnosis_id' => '',
             'referral_clinical_assessment' => '',
             'referral_clinical_assessment_other' => '',
             'referral_clinical_intervention' => '',
             'referral_clinical_intervention_other' => '',
             'referral_clinical_promotive_program' => '',
             'referral_name' => 'required|string',
             'referral_designation' => '',
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => '',
             'category_services' => '',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'id' => '',
            'appId' => '',
         ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $occtform = [
        'added_by' => $request->added_by,
        'patient_mrn_id' => $request->patient_mrn_id,
        'referral_location' => $request->referral_location,
        'date' => $request->date,
        'diagnosis_id' => $request->diagnosis_id,
        'referral_clinical_assessment' => $request->referral_clinical_assessment,
        'referral_clinical_assessment_other' => $request->referral_clinical_assessment_other,
        'referral_clinical_intervention' => $request->referral_clinical_intervention,
        'referral_clinical_intervention_other' => $request->referral_clinical_intervention_other,
        'referral_clinical_promotive_program' => $request->referral_clinical_promotive_program,
        'referral_name' => $request->referral_name,
        'referral_designation' => $request->referral_designation,
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
        'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
        'add_sub_code_id' => $additional_sub_code_id, //newly added
        'add_code_id' => $request->additional_code_id, //newly added
        'appointment_details_id' => $request->appId,
        ];
        $user_diagnosis = [
            'app_id' => $request->appId,
            'patient_id' =>  $request->patient_mrn_id,
            'diagnosis_id' =>  $request->diagnosis_id,
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
        if($request->id){
            Occt_Referral_Form::where(['id' => $request->id])->update($occtform);
            return response()->json(["message" => "Successfully updated", "code" => 200]);
        }else{
            $HOD=Occt_Referral_Form::create($occtform);

        return response()->json(["message" => "Created", "code" => 200]);
        }
    }
}
}


