<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Occt_Referral_Form;
use Validator;

class OcctReferralFormController extends Controller
{
    public function store(Request $request)
    {
        if($request->status=='0'){
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
                'sub_code_id' => $request->sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "0",
                'appointment_details_id' => $request->appId,
                ];
                $validateOcctForm = [];
                if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateOcctForm['services_id'] = 'required';
                $occtform['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateOcctForm['code_id'] = 'required';
                $occtform['code_id'] =  $request->code_id;
                $validateOcctForm['sub_code_id'] = 'required';
                $occtform['sub_code_id'] =  $request->sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateOcctForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            Occt_Referral_Form::firstOrCreate($occtform);
            return response()->json(["message" => "Occt Form Created Successfully!", "code" => 200]);
        } else if($request->status=='1') {
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
                'sub_code_id' => $request->sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "1",
                'appointment_details_id' => $request->appId,
                ];
                $validateOcctForm = [];
                if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateOcctForm['services_id'] = 'required';
                $occtform['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateOcctForm['code_id'] = 'required';
                $occtform['code_id'] =  $request->code_id;
                $validateOcctForm['sub_code_id'] = 'required';
                $occtform['sub_code_id'] =  $request->sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateOcctForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
        }
        Occt_Referral_Form::firstOrCreate($occtform);
        return response()->json(["message" => "Occt Form Created Successfully!", "code" => 200]);
    }
}


