<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientIndexForm;
use DateTime;
use DateTimeZone;

class PatientIndexFormController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => '',
            'patient_mrn_id' => '',
            'appointment_details_id' => '',
            'diagnosis' => '',
            'date_onset' => '',
            'date_of_diagnosis' => '',
            'date_of_referral' => '',
            'date_of_first_assessment' => '',
            'hospital' => '',
            'latest_admission_date' => '',
            'date_of_discharge' => '',
            'reason' => '',
            'adherence_to_medication' => '',
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
            'past_Medical' => '',
            'background_history' => '',
            'who_das_assessment' => '',
            'mental_state_examination' => '',
            'summary_of_issues' => '',
            'management_plan' => '',
            'location_of_services' => '',
            'type_of_diagnosis' => '',
            'category_of_services' => '',
            'services_id' => '',
            'added_by'=> '',
            'patient_mrn_id' => '',
            'complexity_of_service' => '',
            'code_id' => '',
            'sub_code_id' => '',
            'complexity_of_service' => '',
            'outcome' => '',
            'medication' => '',
            'zone' => '',
            'case_manager' => '',
            'specialist' => '',
            'date' => '',
            'id' => '',
            'appointment_details_id' => '',
            'additional_diagnosis' => '',
            'additional_subcode' => '',
            'additional_code_id' => '',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if($request->status=='0') {
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

                $patientindexform = [
                    'services_id' =>  $request->services_id,
                    'code_id' => $request->code_id,
                    'sub_code_id' => $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'diagnosis' =>  $request->diagnosis,
                    'date_onset' =>  $request->date_onset,
                    'date_of_diagnosis' =>  $request->date_of_diagnosis,
                    'date_of_referral' =>  $request->date_of_referral,
                    'date_of_first_assessment' =>  $request->date_of_first_assessment,
                    'hospital' =>  $request->hospital,
                    'latest_admission_date' =>  $request->latest_admission_date,
                    'date_of_discharge' =>  $request->date_of_discharge,
                    'reason' =>  $request->reason,
                    'adherence_to_medication' =>  $request->adherence_to_medication,
                    'aggresion' =>  $request->aggresion,
                    'suicidality' =>  $request->suicidality,
                    'criminality' =>  $request->criminality,
                    'age_first_started' =>  $request->age_first_started,
                    'heroin' =>  $request->heroin,
                    'cannabis' =>  $request->cannabis,
                    'ats' =>  $request->ats,
                    'inhalant' =>  $request->inhalant,
                    'alcohol' =>  $request->alcohol,
                    'tobacco' =>  $request->tobacco,
                    'others' =>  $request->others,
                    'past_Medical' =>  $request->past_Medical,
                    'background_history' =>  $request->background_history,
                    'who_das_assessment' =>  $request->who_das_assessment,
                    'mental_state_examination' =>  $request->mental_state_examination,
                    'summary_of_issues' =>  $request->summary_of_issues,
                    'management_plan' =>  $request->management_plan,
                    'location_of_services' =>  $request->location_of_services,
                    'type_of_diagnosis' =>  $request->type_of_diagnosis,
                    'category_of_services' =>  $request->category_of_services,
                    'complexity_of_service' =>  $request->complexity_of_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'zone' =>  $request->zone,
                    'case_manager' =>  $request->case_manager,
                    'specialist' =>  $request->specialist,
                    'date' =>  $request->date,
                    'status' => "0",
                    'appointment_details_id' => $request->appId,
                    'additional_code_id' => $request->additional_code_id,
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_subcode' => $additional_subcode,
                ];

            //     $validatePatientIndex = [];

            // if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
            //     $validatePatientIndex['services_id'] = '';
            //     $patientindexform['services_id'] =  $request->services_id;
            // } else if ($request->category_services == 'clinical-work') {
            //     $validatePatientIndex['code_id'] = '';
            //     $patientindexform['code_id'] =  $request->code_id;
            //     $validatePatientIndex['sub_code_id'] = '';
            //     $patientindexform['sub_code_id'] =  $sub_code_id;
            // }

                if($request->id) {
                    PatientIndexForm::where(
                                ['id' => $request->id]
                            )->update($patientindexform);
                            return response()->json(["message" => "Patient Index Form updated", "code" => 200]);
                } else {
                    PatientIndexForm::create($patientindexform);
                    return response()->json(["message" => "Patient Index Form created", "code" => 200]);
                }

        } if($request->status=='1') {
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

                $patientindexform = [
                    'services_id' =>  $request->services_id,
                    'code_id' => $request->code_id,
                    'sub_code_id' => $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'diagnosis' =>  $request->diagnosis,
                    'date_onset' =>  $request->date_onset,
                    'date_of_diagnosis' =>  $request->date_of_diagnosis,
                    'date_of_referral' =>  $request->date_of_referral,
                    'date_of_first_assessment' =>  $request->date_of_first_assessment,
                    'hospital' =>  $request->hospital,
                    'latest_admission_date' =>  $request->latest_admission_date,
                    'date_of_discharge' =>  $request->date_of_discharge,
                    'reason' =>  $request->reason,
                    'adherence_to_medication' =>  $request->adherence_to_medication,
                    'aggresion' =>  $request->aggresion,
                    'suicidality' =>  $request->suicidality,
                    'criminality' =>  $request->criminality,
                    'age_first_started' =>  $request->age_first_started,
                    'heroin' =>  $request->heroin,
                    'cannabis' =>  $request->cannabis,
                    'ats' =>  $request->ats,
                    'inhalant' =>  $request->inhalant,
                    'alcohol' =>  $request->alcohol,
                    'tobacco' =>  $request->tobacco,
                    'others' =>  $request->others,
                    'past_Medical' =>  $request->past_Medical,
                    'background_history' =>  $request->background_history,
                    'who_das_assessment' =>  $request->who_das_assessment,
                    'mental_state_examination' =>  $request->mental_state_examination,
                    'summary_of_issues' =>  $request->summary_of_issues,
                    'management_plan' =>  $request->management_plan,
                    'location_of_services' =>  $request->location_of_services,
                    'type_of_diagnosis' =>  $request->type_of_diagnosis,
                    'category_of_services' =>  $request->category_of_services,
                    'complexity_of_service' =>  $request->complexity_of_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'zone' =>  $request->zone,
                    'case_manager' =>  $request->case_manager,
                    'specialist' =>  $request->specialist,
                    'date' =>  $request->date,
                    'status' => "1",
                    'appointment_details_id' => $request->appId,
                    'additional_code_id' => $request->additional_code_id,
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_subcode' => $additional_subcode,
                ];

                $validatePatientIndex = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validatePatientIndex['services_id'] = 'required';
                $patientindexform['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validatePatientIndex['code_id'] = 'required';
                $patientindexform['code_id'] =  $request->code_id;
                $validatePatientIndex['sub_code_id'] = 'required';
                $patientindexform['sub_code_id'] =  $sub_code_id;
            }

                if($request->id) {
                    PatientIndexForm::where(
                                ['id' => $request->id]
                            )->update($patientindexform);
                            return response()->json(["message" => "Patient Index Form updated", "code" => 200]);
                } else {
                    PatientIndexForm::create($patientindexform);
                    return response()->json(["message" => "Patient Index Form created", "code" => 200]);
                }

        }
    }
}
