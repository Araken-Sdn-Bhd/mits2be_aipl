<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PsychologyReferral;
use Validator;
use DateTime;
use DateTimeZone;
use App\Models\UserDiagnosis;

class PsychologyReferralController extends Controller
{
    public function store(Request $request)
    {
    if ($request->status=='0') {
        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);

        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $psychologyreferral = [
        'added_by' => $request->added_by,
        'patient_id' => $request->patient_id,
        'diagnosis_id' => $request->diagnosis_id,
        'patient_acknowledged' => $request->patient_acknowledged,
        'reason_referral_assessment' => $request->reason_referral_assessment,
        'reason_referral_assessment_other' => $request->reason_referral_assessment_other,
        'reason_referral_intervention' => $request->reason_referral_intervention,
        'reason_referral_intervention_other' => $request->reason_referral_intervention_other,
        'case_formulation' => $request->case_formulation,
        'referring_doctor' => $request->referring_doctor,
        'designation' => $request->designation,
        'date' => $request->date,
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
        'appointment_details_id' => $request->appId,
        'created_at' => $date->format('Y-m-d H:i:s'),
        'additional_diagnosis' => $additional_diagnosis,
        'additional_code_id' => $request->additional_code_id,
        'additional_sub_code_id' => $additional_sub_code_id,
        ];

        $validatePsychologyReferral = [];

        if ($request->category_services == 'assistance' || $request->category_services == 'external') {
            $validatePsychologyReferral['services_id'] = 'required';
            $psychologyreferral['services_id'] =  $request->services_id;
        } elseif ($request->category_services == 'clinical-work') {
            $validatePsychologyReferral['code_id'] = 'required';
            $psychologyreferral['code_id'] =  $request->code_id;
            $validatePsychologyReferral['sub_code_id'] = 'required';
            $psychologyreferral['sub_code_id'] =  $sub_code_id;
        }
        $validator = Validator::make($request->all(), $validatePsychologyReferral);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->id) {
            PsychologyReferral::where(['id' => $request->id])->update($psychologyreferral);
            return response()->json(["message" => "Successfully updated", "code" => 200]);
        } else {
            $HOD=PsychologyReferral::create($psychologyreferral);

            return response()->json(["message" => "Psychology Referral Form Created Successfully!", "code" => 200]);
        }
    } elseif ($request->status=='1') {
        //dd('ini');
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'diagnosis_id' => 'required',
            'reason_referral_assessment' => '',
            'reason_referral_assessment_other' => '',
            'reason_referral_intervention' => '',
            'reason_referral_intervention_other' => '',
            'case_formulation' => '',
            'referring_doctor' => '',
            'designation' => '',
            'date' => '',
            'location_services' => '',
            'services_id' => '',
            'code_id' => '',
            'sub_code_id' => '',
            'type_diagnosis_id' => '',
            'category_services' => '',
            'complexity_services' => '',
            'outcome' => '',
            'medication_des' => '',
            'status' => '',
            'id' => '',
           'appId' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));

        $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
        $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
        $sub_code_id=str_replace('"','',$request->sub_code_id);

        $psychologyreferral = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'diagnosis_id' => $request->diagnosis_id,
            'patient_acknowledged' => $request->patient_acknowledged,
            'reason_referral_assessment' => $request->reason_referral_assessment,
            'reason_referral_assessment_other' => $request->reason_referral_assessment_other,
            'reason_referral_intervention' => $request->reason_referral_intervention,
            'reason_referral_intervention_other' => $request->reason_referral_intervention_other,
            'case_formulation' => $request->case_formulation,
            'referring_doctor' => $request->referring_doctor,
            'designation' => $request->designation,
            'date' => $request->date,
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
            'appointment_details_id' => $request->appId,
            'created_at' => $date->format('Y-m-d H:i:s'),
            'additional_diagnosis' => $additional_diagnosis,
            'additional_code_id' => $request->additional_code_id,
            'additional_sub_code_id' => $additional_sub_code_id,
            ];

            $validatePsychologyReferral = [];

            if ($request->category_services == 'assistance' || $request->category_services == 'external') {
                $validatePsychologyReferral['services_id'] = 'required';
                $psychologyreferral['services_id'] =  $request->services_id;
            } elseif ($request->category_services == 'clinical-work') {
                $validatePsychologyReferral['code_id'] = 'required';
                $psychologyreferral['code_id'] =  $request->code_id;
                $validatePsychologyReferral['sub_code_id'] = 'required';
                $psychologyreferral['sub_code_id'] =  $sub_code_id;
            }
            $validator = Validator::make($request->all(), $validatePsychologyReferral);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $user_diagnosis = [
                'app_id' => $request->appId,
                'patient_id' =>  $request->patient_id,
                'diagnosis_id' =>  $request->diagnosis_id,
                'add_diagnosis_id' => $additional_diagnosis,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'add_code_id'=> $request-> additional_code_id,
                'add_sub_code_id' => $additional_sub_code_id,
                'outcome_id' =>  $request->outcome,
                'category_services' =>  $request->category_services,
                'remarks' => 'psychology_referral',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserDiagnosis::create($user_diagnosis);
            if ($request->id) {
                PsychologyReferral::where(['id' => $request->id])->update($psychologyreferral);
                return response()->json(["message" => "Successfully updated", "code" => 200]);
            } else {
                $HOD=PsychologyReferral::create($psychologyreferral);

                return response()->json(["message" => "Psychology Referral Form Created Successfully!", "code" => 200]);
            }
    }
}
}

