<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RehabReferralAndClinicalForm;
use App\Models\UserDiagnosis;
class RehabReferralAndClinicalFormController extends Controller
{
    public function store(Request $request)
    {
        if ($request->status=='0') {
            $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
            $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
            $sub_code_id=str_replace('"','',$request->sub_code_id);

            $rehabreferralandclinicalform = [
            'added_by' => $request->added_by,
            'patient_mrn_id' => $request->patient_mrn_id,
            'patient_referred_for' => $request->patient_referred_for,
            'diagnosis' => $request->diagnosis,
            'date_onset' => $request->date_onset,
            'date_of_referral' => $request->date_of_referral,
            'no_of_admission' => $request->no_of_admission,
            'latest_admission_date' => $request->latest_admission_date,
            'alerts' => $request->alerts,
            'education_level' => $request->education_level,
            'aggresion' => $request->aggresion,
            'suicidality' => $request->suicidality,
            'criminality' => $request->criminality,
            'age_first_started' => $request->age_first_started,
            'heroin' => $request->heroin,
            'cannabis' => $request->cannabis,
            'ats' => $request->ats,
            'inhalant' => $request->inhalant,
            'alcohol' => $request->alcohol,
            'tobacco' => $request->tobacco,
            'others' => $request->others,
            'other_information' => $request->other_information,
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'referral_name' => $request->referral_name,
            'designation' => $request->designation,
            'status' => "0",
            'current_medication' => $request->current_medication,
            'appointment_details_id' => $request->appId,
            'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
            'add_sub_code_id' => $additional_sub_code_id, //newly added
            'add_code_id' => $request->additional_code_id, //newly added
            ];
            $validateRehabReferralAndClinicalForm = [];
            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateRehabReferralAndClinicalForm['services_id'] = 'required';
                $rehabreferralandclinicalform['services_id'] =  $request->services_id;
            } elseif ($request->category_services == 'clinical-work') {
                $validateRehabReferralAndClinicalForm['code_id'] = 'required';
                $rehabreferralandclinicalform['code_id'] =  $request->code_id;
                $validateRehabReferralAndClinicalForm['sub_code_id'] = 'required';
                $rehabreferralandclinicalform['sub_code_id'] =  $sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateRehabReferralAndClinicalForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            if ($request->id) {
                RehabReferralAndClinicalForm::where(['id' => $request->id])->update($rehabreferralandclinicalform);
                return response()->json(["message" => "Successfully updated", "code" => 200]);
            } else {
                $HOD=RehabReferralAndClinicalForm::create($rehabreferralandclinicalform);
                return response()->json(["message" => "Rehab Referral and Clinical Form Created Successfully!", "code" => 200]);
            }
        }else if($request->status=='1'){
                $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
                $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
                $sub_code_id=str_replace('"','',$request->sub_code_id);

            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'patient_mrn_id' => 'required|integer',
                'patient_referred_for' => 'required|string',
                'diagnosis' => '',
                'date_onset' => '',
                'date_of_referral' => '',
                'no_of_admission' => '',
                'latest_admission_date' => '',
                'current_medication' => '',
                'alerts' => '',
                'education_level' => '',
                'aggresion' => '',
                'suicidality' => '',
                'criminality' => '',
                'age_first_started' => '',
                'heroin' => '',
                'cannabis' => '',
                'ats' => '',
                'inhalant' => '',
                'alcohol' => '',
                'tobacco' => '',
                'others' => '',
                'other_information' => '',
                'location_services' => '',
                'type_diagnosis_id' => '',
                'category_services' => '',
                'complexity_services' => '',
                'outcome' => '',
                'medication_des' => '',
                'referral_name' => '',
                'designation' => '',
                'id' => '',
               'appId' => '',
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
               $rehabreferralandclinicalform = [
               'added_by' => $request->added_by,
               'patient_mrn_id' => $request->patient_mrn_id,
               'patient_referred_for' => $request->patient_referred_for,
               'diagnosis' => $request->diagnosis,
               'date_onset' => $request->date_onset,
               'date_of_referral' => $request->date_of_referral,
               'no_of_admission' => $request->no_of_admission,
               'latest_admission_date' => $request->latest_admission_date,
               'alerts' => $request->alerts,
               'education_level' => $request->education_level,
               'aggresion' => $request->aggresion,
               'suicidality' => $request->suicidality,
               'criminality' => $request->criminality,
               'age_first_started' => $request->age_first_started,
               'heroin' => $request->heroin,
               'cannabis' => $request->cannabis,
               'ats' => $request->ats,
               'inhalant' => $request->inhalant,
               'alcohol' => $request->alcohol,
               'tobacco' => $request->tobacco,
               'others' => $request->others,
               'other_information' => $request->other_information,
               'location_services' => $request->location_services,
               'services_id' => $request->services_id,
               'code_id' => $request->code_id,
               'sub_code_id' => $sub_code_id,
               'type_diagnosis_id' => $request->type_diagnosis_id,
               'category_services' => $request->category_services,
               'complexity_services' => $request->complexity_services,
               'outcome' => $request->outcome,
               'medication_des' => $request->medication_des,
               'referral_name' => $request->referral_name,
               'designation' => $request->designation,
               'current_medication' => $request->current_medication,
               'status' => "1",
               'appointment_details_id' => $request->appId,
               'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
               'add_sub_code_id' => $additional_sub_code_id, //newly added
               'add_code_id' => $request->additional_code_id, //newly added
               ];
               $validateRehabReferralAndClinicalForm = [];
            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateRehabReferralAndClinicalForm['services_id'] = 'required';
                $rehabreferralandclinicalform['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateRehabReferralAndClinicalForm['code_id'] = 'required';
                $rehabreferralandclinicalform['code_id'] =  $request->code_id;
                $validateRehabReferralAndClinicalForm['sub_code_id'] = 'required';
                $rehabreferralandclinicalform['sub_code_id'] =  $sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateRehabReferralAndClinicalForm);
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
                'remarks' => 'rehab_referral_and_clinical_form',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserDiagnosis::create($user_diagnosis);
            if ($request->id) {
                RehabReferralAndClinicalForm::where(['id' => $request->id])->update($rehabreferralandclinicalform);
                return response()->json(["message" => "Successfully updated", "code" => 200]);
            } else {
                $HOD=RehabReferralAndClinicalForm::create($rehabreferralandclinicalform);
                return response()->json(["message" => "Rehab Referral and Clinical Form Created Successfully!", "code" => 200]);
            }
        }
    }
}
