<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientRegistration;
use App\Models\TriageForm;
use Exception;
use Validator;
use App\Models\UserDiagnosis;
use Illuminate\Support\Facades\DB;

class TriageFormController extends Controller
{
    public function store(Request $request)
    {

        if ($request->status == '1') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'patient_mrn_id' => 'required|integer',
                'risk_history_assressive' => '',
                'risk_history_criminal' => '',
                'risk_history_detereotation' => '',
                'risk_history_neglect' => '',
                'risk_history_suicidal_idea' => '',
                'risk_history_suicidal_attempt' => '',
                'risk_history_homicidal_idea' => '',
                'risk_history_homicidal_attempt' => '',
                'risk_history_aggressive_idea' => '',
                'risk_history_aggressive_attempt' => '',
                'risk_social_has_no_family' => '',
                'risk_homeless' => '',
                'capacity_cannot_give_commitment' => '',
                'capacity_showed_no_interest' => '',
                'treatment_checked' => '',
                'treatment_given_appointment' => '',
                'treatment_given_regular' => '',
                'placement_referred' => '',
                'placement_discharge' => '',
                'screening_id' => '',
                'score' => '',

                'appointment_date' => '',
                'appointment_time' => '',
                'appointment_duration' => '',
                'appointment_type' => '',
                'appointment_type_visit' => '',
                'appointment_patient_category' => '',
                'appointment_assign_team' => '',

                'location_services_id' => '',
                'services_id' => '',
                'code_id' => '',
                'sub_code_id' => '',
                'type_diagnosis_id' => '',
                'category_services' => '',
                'complexity_services_id' => '',
                'outcome_id' => '',
                'medication_des' => '',
                'id' => '',
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            $patient_mrn_id = $request->patient_mrn_id;
            $chkPoint1 =  PatientRegistration::where(function ($query) use ($patient_mrn_id) {
                $query->where('id', '=', $patient_mrn_id);
            })->where('status', '1')->get();

            if ($chkPoint1->count() == 0) {
                return response()->json(["message" => "This user is not registered", "code" => 401]);
            } else {
                $appointment_date = $request->appointment_date;
                $appointment_time = $request->appointment_time;
                $appointment_assign_team = $request->appointment_assign_team;
                // $chkPoint =  PatientAppointmentDetails::where(function ($query) use ($appointment_date, $appointment_time, $appointment_assign_team) {
                //     $query->where('booking_date', '=', $appointment_date)->where('booking_time', '=', $appointment_time)->where('assign_team', '=', $appointment_assign_team);
                // })->where('status', '1')->get();

                $chkPointfortriage =  TriageForm::where(function ($query) use ($appointment_date, $appointment_time, $appointment_assign_team) {
                    $query->where('appointment_date', '=', $appointment_date)->where('appointment_time', '=', $appointment_time)->where('appointment_assign_team', '=', $appointment_assign_team);
                })->where('status', '1')->get();

                if ($chkPointfortriage->count() == 0) {
                    $triageform = [
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_mrn_id,

                        'risk_history_assressive' => $request->risk_history_assressive,
                        'risk_history_criminal' => $request->risk_history_criminal,
                        'risk_history_detereotation' => $request->risk_history_detereotation,
                        'risk_history_neglect' => $request->risk_history_neglect,
                        'risk_history_suicidal_idea' => $request->risk_history_suicidal_idea,
                        'risk_history_suicidal_attempt' => $request->risk_history_suicidal_attempt,
                        'risk_history_homicidal_idea' => $request->risk_history_homicidal_idea,
                        'risk_history_homicidal_attempt' => $request->risk_history_homicidal_attempt,
                        'risk_history_aggressive_idea' => $request->risk_history_aggressive_idea,
                        'risk_history_aggressive_attempt' => $request->risk_history_aggressive_attempt,
                        'risk_social_has_no_family' => $request->risk_social_has_no_family,
                        'risk_homeless' => $request->risk_homeless,
                        'capacity_cannot_give_commitment' => $request->capacity_cannot_give_commitment,
                        'capacity_showed_no_interest' => $request->capacity_showed_no_interest,
                        'treatment_checked' => $request->treatment_checked,
                        'treatment_given_appointment' => $request->treatment_given_appointment,
                        'treatment_given_regular' => $request->treatment_given_regular,
                        'placement_referred' => $request->placement_referred,
                        'placement_discharge' => $request->placement_discharge,
                        'screening_id' => $request->screening_id,
                        'score' => $request->score,
                        'appointment_date' => $request->appointment_date,
                        'appointment_time' => $request->appointment_time,
                        'appointment_duration' => $request->appointment_duration,
                        'appointment_type' => $request->appointment_type,
                        'appointment_type_visit' => $request->appointment_type_visit,
                        'appointment_patient_category' => $request->appointment_patient_category,
                        'appointment_assign_team' => $request->appointment_assign_team,

                        'location_services_id' => $request->location_services_id,
                        'services_id' => $request->services_id,
                        'code_id' => $request->code_id,
                        'sub_code_id' => $request->sub_code_id,
                        'type_diagnosis_id' => $request->type_diagnosis_id,
                        'category_services' => $request->category_services,
                        'complexity_services_id' => $request->complexity_services_id,
                        'outcome_id' => $request->outcome_id,
                        'medication_des' => $request->medication_des,
                        'status' => "1",
                    ];

                    $validateTriageForm = [];

                    if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                        $validateTriageForm['services_id'] = 'required';
                        $triageform['services_id'] =  $request->services_id;
                    } elseif ($request->category_services == 'clinical-work') {
                        $validateTriageForm['code_id'] = 'required';
                        $triageform['code_id'] =  $request->code_id;
                        $validateTriageForm['sub_code_id'] = 'required';
                        $triageform['sub_code_id'] =  $request->sub_code_id;
                    }
                    $validator = Validator::make($request->all(), $validateTriageForm);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }
                    if ($request->id) {
                        TriageForm::where(['id' => $request->id])->update($triageform);
                        if($request->patient_mrn_id){
                            PatientRegistration::where(['id' => $request->patient_mrn_id])->update(['patient_need_triage_screening' => '0']);
                        }
                        return response()->json(["message" => "Triage Updated Successfully!", "code" => 200]);
                    } else {
                        TriageForm::firstOrCreate($triageform);
                        if($request->patient_mrn_id){
                            PatientRegistration::where(['id' => $request->patient_mrn_id])->update(['patient_need_triage_screening' => '0']);
                        }
                        return response()->json(["message" => "Triage Created Successfully!", "code" => 200]);
                    }
                } else {
                    return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
                }
            }
        } elseif ($request->status == '0') {
            $triageform = [
                'added_by' => $request->added_by,
                'patient_mrn_id' => $request->patient_mrn_id,

                'risk_history_assressive' => $request->risk_history_assressive,
                'risk_history_criminal' => $request->risk_history_criminal,
                'risk_history_detereotation' => $request->risk_history_detereotation,
                'risk_history_neglect' => $request->risk_history_neglect,
                'risk_history_suicidal_idea' => $request->risk_history_suicidal_idea,
                'risk_history_suicidal_attempt' => $request->risk_history_suicidal_attempt,
                'risk_history_homicidal_idea' => $request->risk_history_homicidal_idea,
                'risk_history_homicidal_attempt' => $request->risk_history_homicidal_attempt,
                'risk_history_aggressive_idea' => $request->risk_history_aggressive_idea,
                'risk_history_aggressive_attempt' => $request->risk_history_aggressive_attempt,
                'risk_social_has_no_family' => $request->risk_social_has_no_family,
                'risk_homeless' => $request->risk_homeless,
                'capacity_cannot_give_commitment' => $request->capacity_cannot_give_commitment,
                'capacity_showed_no_interest' => $request->capacity_showed_no_interest,
                'treatment_checked' => $request->treatment_checked,
                'treatment_given_appointment' => $request->treatment_given_appointment,
                'treatment_given_regular' => $request->treatment_given_regular,
                'placement_referred' => $request->placement_referred,
                'placement_discharge' => $request->placement_discharge,
                'screening_id' => $request->screening_id,
                'score' => $request->score,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'appointment_duration' => $request->appointment_duration,
                'appointment_type' => $request->appointment_type,
                'appointment_type_visit' => $request->appointment_type_visit,
                'appointment_patient_category' => $request->appointment_patient_category,
                'appointment_assign_team' => $request->appointment_assign_team,

                'location_services_id' => $request->location_services_id,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $request->sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services_id' => $request->complexity_services_id,
                'outcome_id' => $request->outcome_id,
                'medication_des' => $request->medication_des,
                'status' => "0",
                'appointment_details_id' => $request->appId,
            ];
            if ($request->id) {
                TriageForm::where(['id' => $request->id])->update($triageform);
                return response()->json(["message" => "Triage Updated Successfully!", "code" => 200]);
            } else {
                TriageForm::create($triageform);
                return response()->json(["message" => "Triage Created Successfully!", "code" => 200]);
            }
        }
    }

    public function storeTriage(Request $request) {

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_mrn_id' => 'required|integer',
            'appointment_details_id' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($request->status == '1') {
            $triageform = [
                'added_by' => $request->added_by,
                'patient_mrn_id' => $request->patient_mrn_id,

                'risk_history_assressive' => $request->risk_history_assressive,
                'risk_history_criminal' => $request->risk_history_criminal,
                'risk_history_detereotation' => $request->risk_history_detereotation,
                'risk_history_neglect' => $request->risk_history_neglect,
                'risk_history_suicidal_idea' => $request->risk_history_suicidal_idea,
                'risk_history_suicidal_attempt' => $request->risk_history_suicidal_attempt,
                'risk_history_homicidal_idea' => $request->risk_history_homicidal_idea,
                'risk_history_homicidal_attempt' => $request->risk_history_homicidal_attempt,
                'risk_history_aggressive_idea' => $request->risk_history_aggressive_idea,
                'risk_history_aggressive_attempt' => $request->risk_history_aggressive_attempt,
                'risk_social_has_no_family' => $request->risk_social_has_no_family,
                'risk_homeless' => $request->risk_homeless,
                'capacity_cannot_give_commitment' => $request->capacity_cannot_give_commitment,
                'capacity_showed_no_interest' => $request->capacity_showed_no_interest,
                'treatment_checked' => $request->treatment_checked,
                'treatment_given_appointment' => $request->treatment_given_appointment,
                'treatment_given_regular' => $request->treatment_given_regular,
                'placement_referred' => $request->placement_referred,
                'placement_discharge' => $request->placement_discharge,
                'screening_id' => $request->screening_id,
                'score' => $request->score,
                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
                'additional_diagnosis' => $additional_diagnosis,
                'location_services_id' => $request->location_services_id,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services_id' => $request->complexity_services_id,
                'outcome_id' => $request->outcome_id,
                'medication_des' => $request->medication_des,
                'status' => "1",
                'appointment_details_id' => $request->appId,
            ];

                        $validateTriageForm = [];

                        if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                            $validateTriageForm['services_id'] = 'required';
                            $triageform['services_id'] =  $request->services_id;
                        } elseif ($request->category_services == 'clinical-work') {
                            $validateTriageForm['code_id'] = 'required';
                            $triageform['code_id'] =  $request->code_id;
                            $validateTriageForm['sub_code_id'] = 'required';
                            $triageform['sub_code_id'] =  $sub_code_id;
                        }
                        $validator = Validator::make($request->all(), $validateTriageForm);
                        if ($validator->fails()) {
                            return response()->json(["message" => $validator->errors(), "code" => 422]);
                        }

                        $user_diagnosis = [
                            'app_id' => $request->appId,
                            'patient_id' =>  $request->patient_mrn_id,
                            'diagnosis_id' =>  $request->type_diagnosis_id,
                            'add_diagnosis_id' => $additional_diagnosis,
                            'code_id' =>  $request->code_id,
                            'sub_code_id' =>  $sub_code_id,
                            'add_code_id'=> $request->additional_code_id,
                            'add_sub_code_id' => $additional_subcode,
                            'outcome_id' =>  $request->outcome_id,
                            'category_services' =>  $request->category_services,
                            'remarks' => 'triage_form',
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                        UserDiagnosis::create($user_diagnosis);
                        if ($request->id) {
                            TriageForm::where(['id' => $request->id])->update($triageform);
                            return response()->json(["message" => "Triage Updated Successfully!", "code" => 200]);
                        } else {
                            TriageForm::create($triageform);
                            return response()->json(["message" => "Triage Created Successfully!", "code" => 200]);
                        }
        }
        else if ($request->status == '0') {
            $triageform = [
                'added_by' => $request->added_by,
                'patient_mrn_id' => $request->patient_mrn_id,

                'risk_history_assressive' => $request->risk_history_assressive,
                'risk_history_criminal' => $request->risk_history_criminal,
                'risk_history_detereotation' => $request->risk_history_detereotation,
                'risk_history_neglect' => $request->risk_history_neglect,
                'risk_history_suicidal_idea' => $request->risk_history_suicidal_idea,
                'risk_history_suicidal_attempt' => $request->risk_history_suicidal_attempt,
                'risk_history_homicidal_idea' => $request->risk_history_homicidal_idea,
                'risk_history_homicidal_attempt' => $request->risk_history_homicidal_attempt,
                'risk_history_aggressive_idea' => $request->risk_history_aggressive_idea,
                'risk_history_aggressive_attempt' => $request->risk_history_aggressive_attempt,
                'risk_social_has_no_family' => $request->risk_social_has_no_family,
                'risk_homeless' => $request->risk_homeless,
                'capacity_cannot_give_commitment' => $request->capacity_cannot_give_commitment,
                'capacity_showed_no_interest' => $request->capacity_showed_no_interest,
                'treatment_checked' => $request->treatment_checked,
                'treatment_given_appointment' => $request->treatment_given_appointment,
                'treatment_given_regular' => $request->treatment_given_regular,
                'placement_referred' => $request->placement_referred,
                'placement_discharge' => $request->placement_discharge,
                'screening_id' => $request->screening_id,
                'score' => $request->score,

                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
                'additional_diagnosis' => $additional_diagnosis,
                'location_services_id' => $request->location_services_id,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services_id' => $request->complexity_services_id,
                'outcome_id' => $request->outcome_id,
                'medication_des' => $request->medication_des,
                'status' => "0",
                'appointment_details_id' => $request->appId,
            ];

            if ($request->id) {
                TriageForm::where(['id' => $request->id])->update($triageform);
                return response()->json(["message" => "Triage Updated Successfully!", "code" => 200]);
            } else {
                TriageForm::create($triageform);
                return response()->json(["message" => "Triage Created Successfully!", "code" => 200]);
            }
        }
    }
}
