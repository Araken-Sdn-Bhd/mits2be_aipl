<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExternalReferralForm;
use Validator;
use Exception;

class ExternalReferralFormController extends Controller
{
    //
    public function store(Request $request)
    {
    if ($request->status=='0') {
        
        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);

        $externalform = [
        'added_by' => $request->added_by,
        'patient_mrn_id' => $request->patient_mrn_id,

        'history' => $request->history,
        'examination' => $request->examination,

        'diagnosis' => $request->diagnosis,
        'result_of_investigation' => $request->result_of_investigation,
        'treatment' => $request->treatment,
        'purpose_of_referral' => $request->purpose_of_referral,
        'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
        'add_sub_code_id' => $additional_sub_code_id, //newly added
        'add_code_id' => $request->additional_code_id, //newly added
        'location_services' => $request->location_services,
        'services_id' => $request->services_id,
        'code_id' => $request->code_id,
        'sub_code_id' => $sub_code_id,
        'type_diagnosis_id' => $request->type_diagnosis_id,
        'category_services' => $request->category_services,
        'complexity_services' => $request->complexity_of_services,
        'outcome' => $request->outcome,
        'medication_des' => $request->medication_des,
        'name' => $request->name,
        
        'designation' => $request->designation,
        'hospital' => $request->hospital,
        'status' => "0",
         'appointment_details_id' => $request->appId,
        ];

        $validateExternalForm = [];

        if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
            $validateExternalForm['services_id'] = 'required';
            $externalform['services_id'] =  $request->services_id;
        } elseif ($request->category_services == 'clinical-work') {
            $validateExternalForm['code_id'] = 'required';
            $externalform['code_id'] =  $request->code_id;
            $validateExternalForm['sub_code_id'] = 'required';
            $externalform['sub_code_id'] =  $sub_code_id;
        }
        $validator = Validator::make($request->all(), $validateExternalForm);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->id) {
            ExternalReferralForm::where(['id' => $request->id])->update($externalform);
            return response()->json(["message" => "Successfully updated", "code" => 200]);
        } else {
            $HOD=ExternalReferralForm::create($externalform);

            return response()->json(["message" => "External Referral Form Created Successfully!", "code" => 200]);
        }
    } elseif ($request->status=='1') {
        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_mrn_id' => 'required|integer',
            'history' => 'required|string',
            'examination' => '',
            'diagnosis' => '',
            'result_of_investigation' => '',
            'treatment' => '',
            'purpose_of_referral' => '',
            'location_services' => '',
            'services_id' => '',
            'code_id' => '',
            'sub_code_id' => '',
            'type_diagnosis_id' => '',
            'category_services' => '',
            'complexity_services' => '',
            'outcome' => '',
            'medication_des' => '',
            'name' => '',
            'designation' => '',
            'hospital' => '',
            'id' => '',
            'appId' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $ExternalForm = [
        'added_by' => $request->added_by,
        'patient_mrn_id' => $request->patient_mrn_id,

        'history' => $request->history,
        'examination' => $request->examination,

        'diagnosis' => $request->diagnosis,
        'result_of_investigation' => $request->result_of_investigation,
        'treatment' => $request->treatment,
        'purpose_of_referral' => $request->purpose_of_referral,
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
        'name' => $request->name,
        'designation' => $request->designation,
        'hospital' => $request->hospital,
        'status' => "1",
         'appointment_details_id' => $request->appId,
        ];

        $validateExternalForm = [];
        if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
            $validateExternalForm['services_id'] = 'required';
            $ExternalForm['services_id'] =  $request->services_id;
        } elseif ($request->category_services == 'clinical-work') {
            $validateExternalForm['code_id'] = 'required';
            $ExternalForm['code_id'] =  $request->code_id;
            $validateExternalForm['sub_code_id'] = 'required';
            $ExternalForm['sub_code_id'] =  $sub_code_id;
        }
        $validator = Validator::make($request->all(), $validateExternalForm);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
    }
    if ($request->id) {
        ExternalReferralForm::where(['id' => $request->id])->update($ExternalForm);
        return response()->json(["message" => "Successfully updated", "code" => 200]);
    } else {
        $HOD=ExternalReferralForm::create($ExternalForm);

        return response()->json(["message" => "External Referral Form Created Successfully!", "code" => 200]);
    }
}
}
