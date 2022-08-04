<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RehabReferralAndClinicalForm;

class RehabReferralAndClinicalFormController extends Controller
{
    //
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_mrn_id' => 'required|integer',
             'patient_referred_for' => 'required|string',
             'diagnosis' => 'required',
             'date_onset' => 'required|date',
             'date_of_referral' => 'required|date',
             'no_of_admission' => 'required',
             'latest_admission_date' => '',
             'current_medication' => '',
             'alerts' => '',
             'education_level' => '',
             'aggresion' => 'string',
             'suicidality' => 'required|string',
             'criminality' => 'required|string',
             'age_first_started' => '',
             'heroin' => 'required|string',
             'cannabis' => 'required|string',
             'ats' => 'required|string', 
             'inhalant' => 'required|string',
             'alcohol' => 'required|string',
             'tobacco' => 'required|string',
             'others' => 'required|string',
             'other_information' => '',
 
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'referral_name' => '',
             'designation' => ''
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
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'referral_name' => $request->referral_name,
            'designation' => $request->designation,
            'status' => "1"
            ];
 
            $validateRehabReferralAndClinicalForm = [];
 
         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateRehabReferralAndClinicalForm['services_id'] = 'required';
             $rehabreferralandclinicalform['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateRehabReferralAndClinicalForm['code_id'] = 'required';
             $rehabreferralandclinicalform['code_id'] =  $request->code_id;
             $validateRehabReferralAndClinicalForm['sub_code_id'] = 'required';
             $rehabreferralandclinicalform['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateRehabReferralAndClinicalForm);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
         RehabReferralAndClinicalForm::firstOrCreate($rehabreferralandclinicalform);  
         return response()->json(["message" => "Rehab Referral and Clinical Form Created Successfully!", "code" => 200]);
        
    }

}
