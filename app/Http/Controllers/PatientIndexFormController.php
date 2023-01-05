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
            'complexity_of_service' => '',
            'outcome' => '',
            'medication' => '',
            'zone' => '',
            'case_manager' => '',
            'specialist' => '',
            'date' => '',
            'id' => '',
            'appointment_details_id' => '',


        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->id) {
            if ($request->category_of_services == 'assisstance' || $request->category_of_services == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                $patientindexform = [
                    'services_id' =>  $request->services_id,
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
                    'created_at' => date("Y-m-d h:i:s"),
                    'updated_at' => date("Y-m-d h:i:s"),
                    
                    
                ]; 

                try {
                    PatientIndexForm::where(
                        ['id' => $request->id]
                    )->update($patientindexform);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $patientindexform, "code" => 200]);
                }
                return response()->json(["message" => "Patient Index Form Successfully00", "code" => 200]);
            } else if ($request->category_of_services == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $PatientIndex = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                ];

                try {
                    PatientIndexForm::where(
                        ['id' => $request->id]
                    )->update($PatientIndex);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $PatientIndex, "code" => 200]);
                }
                return response()->json(["message" => "Patient Index Form Successfully11", "code" => 200]);
            } else if ($request->category_of_services == '0') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $PatientIndex = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                    'status' => "1"
                ];

                try {
                    PatientIndexForm::where(
                        ['id' => $request->id]
                    )->update($PatientIndex);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $PatientIndex, "code" => 200]);
                }
                return response()->json(["message" => "Patient Index Form Successfully11", "code" => 200]);
            }
        } else {
            if ($request->category_of_services == 'assisstance' || $request->category_of_services == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $patientindexform = [
                    'services_id' =>  $request->services_id,
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
                    'status' => $request->status,
                    'appointment_details_id' => $request->appId,
                ];

                try {
                    $HOD = PatientIndexForm::firstOrCreate($patientindexform);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $patientindexform, "code" => 200]);
                }
                return response()->json(["message" => "Patient Index Form Successfully00", "code" => 200]);
            } else if ($request->category_of_services == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $PatientIndex = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                    'status' => $request->status,
                    'appointment_details_id' => $request->appId,
                ];

                try {
                    $HOD = PatientIndexForm::firstOrCreate($PatientIndex);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $PatientIndex, "code" => 200]);
                }
                $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    // $notifi=[
                    //     'added_by' => $HOD['added_by'],
                    //     'patient_id' =>   $HOD['patient_mrn_id'],
                    //     'created_at' => $date->format('Y-m-d H:i:s'),
                    //     'message' =>  'pending submission of Clinical Documents',
                    // ];
                    // $HOD1 = Notifications::insert($notifi);

                return response()->json(["message" => "Patient Index Form Successfully11", "code" => 200]);
            } else if ($request->category_of_services == '0') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $PatientIndex = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
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
                    'status' => $request->status,
                    'appointment_details_id' => $request->appId,
                ];

                try {
                    $HOD2 = PatientIndexForm::firstOrCreate($PatientIndex);
                    // $notifi=[
                    //     'added_by' => $HOD2['added_by'],
                    //     'patient_id' =>   $HOD2['patient_mrn_id'],
                    //     'created_at' =>  date("Y-m-d h:i:s"),
                    //     'message' =>  'pending submission of Clinical Documents',
                    // ];
                    // $HOD2 = Notifications::insert($notifi);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'PatientIndex' => $PatientIndex, "code" => 200]);
                }
                return response()->json(["message" => "Patient Index Form Successfully11", "code" => 200]);
            }
        }
    }
}
