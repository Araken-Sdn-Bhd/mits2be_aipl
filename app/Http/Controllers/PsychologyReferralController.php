<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PsychologyReferral;
use Validator;

class PsychologyReferralController extends Controller
{
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_id' => 'required|integer',
             'diagnosis_id' => 'required',
             'patient_acknowledged' => '',
             'reason_referral_assessment' => '',
             'reason_referral_assessment_other' => '',
             'reason_referral_intervention' => '',
             'reason_referral_intervention_other' => '',
             'case_formulation' => '',
             'referring_doctor' => '',
             'designation' => '',
             'date' => '',

             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required|string',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'id' => '',
            'appointment_details_id' => '',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }


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
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id' => $request->appId,
            ];

            $validateConsultationDischarge = [];

         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateConsultationDischarge['services_id'] = 'required';
             $psychologyreferral['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateConsultationDischarge['code_id'] = 'required';
             $psychologyreferral['code_id'] =  $request->code_id;
             $validateConsultationDischarge['sub_code_id'] = 'required';
             $psychologyreferral['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateConsultationDischarge);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }

         PsychologyReferral::firstOrCreate($psychologyreferral);
         return response()->json(["message" => "Consultation Discharge Note Created Successfully!", "code" => 200]);

    }
}
