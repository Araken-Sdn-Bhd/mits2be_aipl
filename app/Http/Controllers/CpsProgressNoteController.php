<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CpsProgressNote;
use App\Models\CpsProgressList;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientRegistration;
use App\Models\StaffManagement;
use App\Models\Notifications;
use App\Models\AppointmentRequest;
use App\Models\HospitalBranchManagement;
use DateTime;
use App\Models\TransactionLog;
use DateTimeZone;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentRequestMail as AppointmentRequestMail;

class CpsProgressNoteController extends Controller
{

    public function GetCPSProgressList(Request $request)
    {
        $list = CpsProgressList::select('id', 'name', 'type')->where('type', '=', $request->type)
            ->get();
        return response()->json(["message" => "CPS Progress List", 'list' => $list, "code" => 200]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'staff_name' => 'required|string',
            'designation' => 'required|string',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis = str_replace('"', "", $request->additional_diagnosis);
        $additional_subcode = str_replace('"', "", $request->additional_subcode);
        $sub_code_id = str_replace('"', "", $request->sub_code_id);

        if ($request->status == 1) {
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $cpsprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "1",
                        'additional_diagnosis' => $additional_diagnosis,
                    ];

                    try {
                        CpsProgressNote::where(
                            ['id' => $request->id]
                        )->update($cpsprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "1",
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        CpsProgressNote::where(
                            ['id' => $request->id]
                        )->update($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully1", "code" => 200]);
                }
            } else {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $cpsprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($cpsprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully2", "code" => 200]);
                } else if ($request->service_category == 'clinical') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully3", "code" => 200]);
                }
            }
        } else if ($request->status == 0) {
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

                    $cpsprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        CpsProgressNote::where(
                            ['id' => $request->id]
                        )->update($cpsprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {

                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        CpsProgressNote::where(
                            ['id' => $request->id]
                        )->update($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully4", "code" => 200]);
                } else {
                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        CpsProgressNote::where(
                            ['id' => $request->id]
                        )->update($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully5", "code" => 200]);
                }
            } else {
                if ($request->appId == null || $request->appId == '') {
                    $checkTodayAppointment = PatientAppointmentDetails::where('patient_mrn_id', $request->patient_mrn_id)->whereDate("created_at", '=', date('Y-m-d'))->first();
                    if ($checkTodayAppointment) {
                        $request->appId = $checkTodayAppointment->id;
                    } else {
                        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                        $duration_set = 30;
                        $booking_date_set = $date->format('Y-m-d H:i:s');
                        $booking_time_set = $date->format('Y-m-d H:i:s');
                        $assign_team_set = 3;
                        $appointment_type = 3;
                        $patient_category = 150;
                        $type_visit = 153;

                        $PatientDetails = PatientRegistration::where('id', $request->patient_id)->orWhere('patient_mrn', $request->mrn_id)->first();
                        if ($PatientDetails->nric_no != null || $PatientDetails->nric_no != '') {
                            $nric_or_passportno = $PatientDetails->nric_no;
                        } else if ($PatientDetails->passport_no != null && $PatientDetails->nric_no == null && $PatientDetails->nric_no != '') {
                            $nric_or_passportno = $PatientDetails->passport_no;
                        }
                        $userDetails = StaffManagement::where('id', $request->added_by)->first();
                        $nric_or_passportno = $PatientDetails->nric_no;
                        $getmnr_id = PatientRegistration::select('id')
                            ->where('nric_no', $nric_or_passportno)
                            ->orWhere('passport_no', $nric_or_passportno)
                            ->pluck('id');

                        if (count($getmnr_id) == 0) {
                            return response()->json(["message" => "This user is not registered", "code" => 401]);
                        } else {
                            $booking_date = $booking_date_set;
                            $booking_time = $booking_time_set;
                            $assign_team = $assign_team_set;
                            $branch_id = $userDetails->branch_id;
                            $duration = "+" . $duration_set . " minutes";
                            $endTime = date("H:i", strtotime($duration, strtotime($booking_time)));

                            $chkPoint =  PatientRegistration::join('patient_appointment_details', 'patient_appointment_details.patient_mrn_id', '=', 'patient_registration.id')
                                ->where('patient_registration.branch_id', '=', $branch_id)
                                ->where('patient_appointment_details.booking_date', '=', $booking_date)
                                ->whereBetween('patient_appointment_details.booking_time', [$booking_time, $endTime])
                                ->where('patient_appointment_details.status', '=', '1')
                                ->where('patient_appointment_details.assign_team', '=', $assign_team)
                                ->get();

                            if ($chkPoint->count() == 0) {
                                $service = [
                                    'added_by' => $request->added_by,
                                    'nric_or_passportno' => $nric_or_passportno,
                                    'booking_date' => $booking_date_set,
                                    'booking_time' => $booking_time_set,
                                    'patient_mrn_id' => $getmnr_id[0],
                                    'duration' => $duration_set,
                                    'appointment_type' => $appointment_type,
                                    'type_visit' => $type_visit,
                                    'patient_category' => $patient_category,
                                    'assign_team' => $assign_team_set
                                ];
                                $patient = PatientAppointmentDetails::create($service);
                                $request->appId = $patient->id;

                                // EMAIL
                                $app_request = AppointmentRequest::where('nric_or_passportno', $nric_or_passportno)
                                    ->select('name', 'email')->get();

                                $hospital_branch = HospitalBranchManagement::where('id', $userDetails->branch_id)
                                    ->select('hospital_branch_name')->get();
                                if ($app_request->count() != 0) {
                                    $bookingDate = date('d M Y', strtotime($booking_date_set));
                                    $bookingTime = date("h:i A", strtotime($booking_time_set));
                                    $data = array(
                                        'name' => $app_request[0]['name'],
                                        'branch' => ucwords(strtolower($hospital_branch[0]['hospital_branch_name'])),
                                        'email' => $app_request[0]['email'],
                                        'date' => $bookingDate,
                                        'time' => $bookingTime,
                                    );

                                    try {
                                        Mail::to($data['email'])->send(new AppointmentRequestMail($data));
                                    } catch (\Exception $err) {
                                        var_dump($err);

                                        return response([
                                            'message' => 'Error In Email Configuration: ' . $err,
                                            'code' => 500
                                        ]);
                                    }
                                };
                            } else {
                                return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
                            }
                        }
                    }
                }
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

                    $cpsprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($cpsprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully6", "code" => 200]);
                } else if ($request->service_category == 'clinical') {
                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully7", "code" => 200]);
                } else {
                    $CpsProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'cps_date' =>  $request->cps_date,
                        'cps_time' =>  $request->cps_time,
                        'cps_seen_by' =>  $request->cps_seen_by,
                        'cps_date_discussed' =>  $request->cps_date_discussed,
                        'cps_time_discussed' =>  $request->cps_time_discussed,
                        'cps_discussed_with' =>  $request->cps_discussed_with,
                        'visit_date' =>  $request->visit_date,
                        'visit_time' =>  $request->visit_time,
                        'informants_name' =>  $request->informants_name,
                        'informants_relationship' =>  $request->informants_relationship,
                        'informants_contact' =>  $request->informants_contact,
                        'case_manager' =>  $request->case_manager,
                        'visited_by' =>  $request->visited_by,
                        'visit_outcome' =>  $request->visit_outcome,
                        'current_intervention' =>  $request->current_intervention,
                        'compliance_treatment' =>  $request->compliance_treatment,
                        'medication_supervised_by' =>  $request->medication_supervised_by,
                        'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                        'delusions' =>  $request->delusions,
                        'hallucination' =>  $request->hallucination,
                        'behavior' =>  $request->behavior,
                        'blunted_affect' =>  $request->blunted_affect,
                        'depression' =>  $request->depression,
                        'anxiety' =>  $request->anxiety,
                        'disorientation' =>  $request->disorientation,
                        'uncooperativeness' =>  $request->uncooperativeness,
                        'poor_impulse_control' =>  $request->poor_impulse_control,
                        'others' =>  $request->others,
                        'other_specify_details' =>  $request->other_specify_details,
                        'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                        'risk_of_violence' =>  $request->risk_of_violence,
                        'risk_of_suicide' =>  $request->risk_of_suicide,
                        'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                        'risk_of_severe' =>  $request->risk_of_severe,
                        'risk_of_harm' =>  $request->risk_of_harm,
                        'changes_in_teratment' =>  $request->changes_in_teratment,
                        'akathisia' =>  $request->akathisia,
                        'acute_dystonia' =>  $request->acute_dystonia,
                        'parkinsonism' =>  $request->parkinsonism,
                        'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                        'tardive_dystonia' =>  $request->tardive_dystonia,
                        'others_specify' =>  $request->others_specify,
                        'side_effects_remarks' =>  $request->side_effects_remarks,
                        'social_performance' =>  $request->social_performance,
                        'psychoeducation' =>  $request->psychoeducation,
                        'coping_skills' =>  $request->coping_skills,
                        'adl_training' =>  $request->adl_training,
                        'supported_employment' =>  $request->supported_employment,
                        'family_intervention' =>  $request->family_intervention,
                        'intervention_others' =>  $request->intervention_others,
                        'remarks' =>  $request->remarks,
                        'employment_past_months' =>  $request->employment_past_months,
                        'if_employment_yes' =>  $request->if_employment_yes,
                        'psychiatric_clinic' =>  $request->psychiatric_clinic,
                        'im_depot_clinic' =>  $request->im_depot_clinic,
                        'next_community_visit' =>  $request->next_community_visit,
                        'comments' =>  $request->comments,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'complexity_services' =>  $request->complexity_services,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'staff_name' =>  $request->staff_name,
                        'designation' =>  $request->designation,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = CpsProgressNote::create($CpsProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "CPS Progress Note Successfully8", "code" => 200]);
                }
            }
        }
    }
    public function storeMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis = str_replace('"', "", $request->additional_diagnosis);
        $additional_subcode = str_replace('"', "", $request->additional_subcode);
        $sub_code_id = str_replace('"', "", $request->sub_code_id);
        if ( $request->add_diagnosis_type1 != null && $request->add_diagnosis_type1 != ''){
            $additional_diagnosis = $request->add_diagnosis_type1;
            if ($request->add_diagnosis_type2 != null && $request->add_diagnosis_type2 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type2;
                if ($request->add_diagnosis_type3 != null && $request->add_diagnosis_type3 != ''){
                    $additional_diagnosis .= ','.$request->add_diagnosis_type3;
                    if ($request->add_diagnosis_type4 != null && $request->add_diagnosis_type4 != ''){
                        $additional_diagnosis .= ','.$request->add_diagnosis_type4;
                        if ($request->add_diagnosis_type5 != null && $request->add_diagnosis_type5 != ''){
                            $additional_diagnosis .= ','.$request->add_diagnosis_type5;
                        }
                    }
                }
            }
        }
        // if ($request->appId == null || $request->appId == '') {
        //     $checkTodayAppointment = PatientAppointmentDetails::where('patient_mrn_id', $request->patient_mrn_id)->whereDate("created_at", '=', date('Y-m-d'))->first();
        //     if ($checkTodayAppointment) {
        //         $request->appId = $checkTodayAppointment->id;
        //     } else {
        //         $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        //         $duration_set = 30;
        //         $booking_date_set = $date->format('Y-m-d H:i:s');
        //         $booking_time_set = $date->format('Y-m-d H:i:s');
        //         $assign_team_set = 3;
        //         $appointment_type = 3;
        //         $patient_category = 150;
        //         $type_visit = 153;

        //         $PatientDetails = PatientRegistration::where('id', $request->patient_id)->orWhere('patient_mrn', $request->mrn_id)->first();
        //         if ($PatientDetails->nric_no != null || $PatientDetails->nric_no != '') {
        //             $nric_or_passportno = $PatientDetails->nric_no;
        //         } else if ($PatientDetails->passport_no != null && $PatientDetails->nric_no == null && $PatientDetails->nric_no != '') {
        //             $nric_or_passportno = $PatientDetails->passport_no;
        //         }
        //         $userDetails = StaffManagement::where('id', $request->added_by)->first();
        //         $nric_or_passportno = $PatientDetails->nric_no;
        //         $getmnr_id = PatientRegistration::select('id')
        //             ->where('nric_no', $nric_or_passportno)
        //             ->orWhere('passport_no', $nric_or_passportno)
        //             ->pluck('id');

        //         if (count($getmnr_id) == 0) {
        //             return response()->json(["message" => "This user is not registered", "code" => 401]);
        //         } else {
        //             $booking_date = $booking_date_set;
        //             $booking_time = $booking_time_set;
        //             $assign_team = $assign_team_set;
        //             $branch_id = $userDetails->branch_id;
        //             $duration = "+" . $duration_set . " minutes";
        //             $endTime = date("H:i", strtotime($duration, strtotime($booking_time)));

        //             $chkPoint =  PatientRegistration::join('patient_appointment_details', 'patient_appointment_details.patient_mrn_id', '=', 'patient_registration.id')
        //                 ->where('patient_registration.branch_id', '=', $branch_id)
        //                 ->where('patient_appointment_details.booking_date', '=', $booking_date)
        //                 ->whereBetween('patient_appointment_details.booking_time', [$booking_time, $endTime])
        //                 ->where('patient_appointment_details.status', '=', '1')
        //                 ->where('patient_appointment_details.assign_team', '=', $assign_team)
        //                 ->get();

        //             if ($chkPoint->count() == 0) {
        //                 $service = [
        //                     'added_by' => $request->added_by,
        //                     'nric_or_passportno' => $nric_or_passportno,
        //                     'booking_date' => $booking_date_set,
        //                     'booking_time' => $booking_time_set,
        //                     'patient_mrn_id' => $getmnr_id[0],
        //                     'duration' => $duration_set,
        //                     'appointment_type' => $appointment_type,
        //                     'type_visit' => $type_visit,
        //                     'patient_category' => $patient_category,
        //                     'assign_team' => $assign_team_set
        //                 ];
        //                 $patient = PatientAppointmentDetails::create($service);
        //                 $request->appId = $patient->id;

        //                 // EMAIL
        //                 $app_request = AppointmentRequest::where('nric_or_passportno', $nric_or_passportno)
        //                     ->select('name', 'email')->get();

        //                 $hospital_branch = HospitalBranchManagement::where('id', $userDetails->branch_id)
        //                     ->select('hospital_branch_name')->get();
        //                 if ($app_request->count() != 0) {
        //                     $bookingDate = date('d M Y', strtotime($booking_date_set));
        //                     $bookingTime = date("h:i A", strtotime($booking_time_set));
        //                     $data = array(
        //                         'name' => $app_request[0]['name'],
        //                         'branch' => ucwords(strtolower($hospital_branch[0]['hospital_branch_name'])),
        //                         'email' => $app_request[0]['email'],
        //                         'date' => $bookingDate,
        //                         'time' => $bookingTime,
        //                     );

        //                     try {
        //                         Mail::to($data['email'])->send(new AppointmentRequestMail($data));
        //                     } catch (\Exception $err) {
        //                         var_dump($err);

        //                         return response([
        //                             'message' => 'Error In Email Configuration: ' . $err,
        //                             'code' => 500
        //                         ]);
        //                     }
        //                 };
        //             } else {
        //                 return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
        //             }
        //         }
        //     }
        // }
        if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

            $cpsprogressnote = [
                'services_id' =>  $request->services_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->mrn_id,
                'cps_date' =>  $request->cps_date,
                'cps_time' =>  $request->cps_time,
                'cps_seen_by' =>  $request->cps_seen_by,
                'cps_date_discussed' =>  $request->cps_date_discussed,
                'cps_time_discussed' =>  $request->cps_time_discussed,
                'cps_discussed_with' =>  $request->cps_discussed_with,
                'visit_date' =>  $request->visit_date,
                'visit_time' =>  $request->visit_time,
                'informants_name' =>  $request->informants_name,
                'informants_relationship' =>  $request->informants_relationship,
                'informants_contact' =>  $request->informants_contact,
                'case_manager' =>  $request->case_manager,
                'visited_by' =>  $request->visited_by,
                'visit_outcome' =>  $request->visit_outcome,
                'current_intervention' =>  $request->current_intervention,
                'compliance_treatment' =>  $request->compliance_treatment,
                'medication_supervised_by' =>  $request->medication_supervised_by,
                'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                'delusions' =>  $request->delusions,
                'hallucination' =>  $request->hallucination,
                'behavior' =>  $request->behavior,
                'blunted_affect' =>  $request->blunted_affect,
                'depression' =>  $request->depression,
                'anxiety' =>  $request->anxiety,
                'disorientation' =>  $request->disorientation,
                'uncooperativeness' =>  $request->uncooperativeness,
                'poor_impulse_control' =>  $request->poor_impulse_control,
                'others' =>  $request->others,
                'other_specify_details' =>  $request->other_specify_details,
                'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                'risk_of_violence' =>  $request->risk_of_violence,
                'risk_of_suicide' =>  $request->risk_of_suicide,
                'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                'risk_of_severe' =>  $request->risk_of_severe,
                'risk_of_harm' =>  $request->risk_of_harm,
                'changes_in_teratment' =>  $request->changes_in_teratment,
                'akathisia' =>  $request->akathisia,
                'acute_dystonia' =>  $request->acute_dystonia,
                'parkinsonism' =>  $request->parkinsonism,
                'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                'tardive_dystonia' =>  $request->tardive_dystonia,
                'others_specify' =>  $request->others_specify,
                'side_effects_remarks' =>  $request->side_effects_remarks,
                'social_performance' =>  $request->social_performance,
                'psychoeducation' =>  $request->psychoeducation,
                'coping_skills' =>  $request->coping_skills,
                'adl_training' =>  $request->adl_training,
                'supported_employment' =>  $request->supported_employment,
                'family_intervention' =>  $request->family_intervention,
                'intervention_others' =>  $request->intervention_others,
                'remarks' =>  $request->remarks,
                'employment_past_months' =>  $request->employment_past_months,
                'if_employment_yes' =>  $request->if_employment_yes,
                'psychiatric_clinic' =>  $request->psychiatric_clinic,
                'im_depot_clinic' =>  $request->im_depot_clinic,
                'next_community_visit' =>  $request->next_community_visit,
                'comments' =>  $request->comments,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'staff_name' =>  $request->staff_name,
                'designation' =>  $request->designation,
                'status' => "0",
                'appointment_details_id' => $request->appId,
            ];

            try {
                $HOD = CpsProgressNote::create($cpsprogressnote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
        } else if ($request->service_category == 'clinical-work') {
            $CpsProgress = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->mrn_id,
                'cps_date' =>  $request->cps_date,
                'cps_time' =>  $request->cps_time,
                'cps_seen_by' =>  $request->cps_seen_by,
                'cps_date_discussed' =>  $request->cps_date_discussed,
                'cps_time_discussed' =>  $request->cps_time_discussed,
                'cps_discussed_with' =>  $request->cps_discussed_with,
                'visit_date' =>  $request->visit_date,
                'visit_time' =>  $request->visit_time,
                'informants_name' =>  $request->informants_name,
                'informants_relationship' =>  $request->informants_relationship,
                'informants_contact' =>  $request->informants_contact,
                'case_manager' =>  $request->case_manager,
                'visited_by' =>  $request->visited_by,
                'visit_outcome' =>  $request->visit_outcome,
                'current_intervention' =>  $request->current_intervention,
                'compliance_treatment' =>  $request->compliance_treatment,
                'medication_supervised_by' =>  $request->medication_supervised_by,
                'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                'delusions' =>  $request->delusions,
                'hallucination' =>  $request->hallucination,
                'behavior' =>  $request->behavior,
                'blunted_affect' =>  $request->blunted_affect,
                'depression' =>  $request->depression,
                'anxiety' =>  $request->anxiety,
                'disorientation' =>  $request->disorientation,
                'uncooperativeness' =>  $request->uncooperativeness,
                'poor_impulse_control' =>  $request->poor_impulse_control,
                'others' =>  $request->others,
                'other_specify_details' =>  $request->other_specify_details,
                'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                'risk_of_violence' =>  $request->risk_of_violence,
                'risk_of_suicide' =>  $request->risk_of_suicide,
                'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                'risk_of_severe' =>  $request->risk_of_severe,
                'risk_of_harm' =>  $request->risk_of_harm,
                'changes_in_teratment' =>  $request->changes_in_teratment,
                'akathisia' =>  $request->akathisia,
                'acute_dystonia' =>  $request->acute_dystonia,
                'parkinsonism' =>  $request->parkinsonism,
                'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                'tardive_dystonia' =>  $request->tardive_dystonia,
                'others_specify' =>  $request->others_specify,
                'side_effects_remarks' =>  $request->side_effects_remarks,
                'social_performance' =>  $request->social_performance,
                'psychoeducation' =>  $request->psychoeducation,
                'coping_skills' =>  $request->coping_skills,
                'adl_training' =>  $request->adl_training,
                'supported_employment' =>  $request->supported_employment,
                'family_intervention' =>  $request->family_intervention,
                'intervention_others' =>  $request->intervention_others,
                'remarks' =>  $request->remarks,
                'employment_past_months' =>  $request->employment_past_months,
                'if_employment_yes' =>  $request->if_employment_yes,
                'psychiatric_clinic' =>  $request->psychiatric_clinic,
                'im_depot_clinic' =>  $request->im_depot_clinic,
                'next_community_visit' =>  $request->next_community_visit,
                'comments' =>  $request->comments,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'staff_name' =>  $request->staff_name,
                'designation' =>  $request->designation,
                'status' => "0",
                'appointment_details_id' => $request->appId,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
            ];

            try {
                $HOD = CpsProgressNote::create($CpsProgress);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Note Successfully6", "code" => 200]);
        } else if ($request->service_category == 'clinical') {
            $CpsProgress = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $request->sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->mrn_id,
                'cps_date' =>  $request->cps_date,
                'cps_time' =>  $request->cps_time,
                'cps_seen_by' =>  $request->cps_seen_by,
                'cps_date_discussed' =>  $request->cps_date_discussed,
                'cps_time_discussed' =>  $request->cps_time_discussed,
                'cps_discussed_with' =>  $request->cps_discussed_with,
                'visit_date' =>  $request->visit_date,
                'visit_time' =>  $request->visit_time,
                'informants_name' =>  $request->informants_name,
                'informants_relationship' =>  $request->informants_relationship,
                'informants_contact' =>  $request->informants_contact,
                'case_manager' =>  $request->case_manager,
                'visited_by' =>  $request->visited_by,
                'visit_outcome' =>  $request->visit_outcome,
                'current_intervention' =>  $request->current_intervention,
                'compliance_treatment' =>  $request->compliance_treatment,
                'medication_supervised_by' =>  $request->medication_supervised_by,
                'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                'delusions' =>  $request->delusions,
                'hallucination' =>  $request->hallucination,
                'behavior' =>  $request->behavior,
                'blunted_affect' =>  $request->blunted_affect,
                'depression' =>  $request->depression,
                'anxiety' =>  $request->anxiety,
                'disorientation' =>  $request->disorientation,
                'uncooperativeness' =>  $request->uncooperativeness,
                'poor_impulse_control' =>  $request->poor_impulse_control,
                'others' =>  $request->others,
                'other_specify_details' =>  $request->other_specify_details,
                'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                'risk_of_violence' =>  $request->risk_of_violence,
                'risk_of_suicide' =>  $request->risk_of_suicide,
                'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                'risk_of_severe' =>  $request->risk_of_severe,
                'risk_of_harm' =>  $request->risk_of_harm,
                'changes_in_teratment' =>  $request->changes_in_teratment,
                'akathisia' =>  $request->akathisia,
                'acute_dystonia' =>  $request->acute_dystonia,
                'parkinsonism' =>  $request->parkinsonism,
                'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                'tardive_dystonia' =>  $request->tardive_dystonia,
                'others_specify' =>  $request->others_specify,
                'side_effects_remarks' =>  $request->side_effects_remarks,
                'social_performance' =>  $request->social_performance,
                'psychoeducation' =>  $request->psychoeducation,
                'coping_skills' =>  $request->coping_skills,
                'adl_training' =>  $request->adl_training,
                'supported_employment' =>  $request->supported_employment,
                'family_intervention' =>  $request->family_intervention,
                'intervention_others' =>  $request->intervention_others,
                'remarks' =>  $request->remarks,
                'employment_past_months' =>  $request->employment_past_months,
                'if_employment_yes' =>  $request->if_employment_yes,
                'psychiatric_clinic' =>  $request->psychiatric_clinic,
                'im_depot_clinic' =>  $request->im_depot_clinic,
                'next_community_visit' =>  $request->next_community_visit,
                'comments' =>  $request->comments,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'staff_name' =>  $request->staff_name,
                'designation' =>  $request->designation,
                'status' => "0",
                'appointment_details_id' => $request->appId,
            ];

            try {
                $HOD = CpsProgressNote::create($CpsProgress);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Note Successfully7", "code" => 200]);
        } else {
            $CpsProgress = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->mrn_id,
                'cps_date' =>  $request->cps_date,
                'cps_time' =>  $request->cps_time,
                'cps_seen_by' =>  $request->cps_seen_by,
                'cps_date_discussed' =>  $request->cps_date_discussed,
                'cps_time_discussed' =>  $request->cps_time_discussed,
                'cps_discussed_with' =>  $request->cps_discussed_with,
                'visit_date' =>  $request->visit_date,
                'visit_time' =>  $request->visit_time,
                'informants_name' =>  $request->informants_name,
                'informants_relationship' =>  $request->informants_relationship,
                'informants_contact' =>  $request->informants_contact,
                'case_manager' =>  $request->case_manager,
                'visited_by' =>  $request->visited_by,
                'visit_outcome' =>  $request->visit_outcome,
                'current_intervention' =>  $request->current_intervention,
                'compliance_treatment' =>  $request->compliance_treatment,
                'medication_supervised_by' =>  $request->medication_supervised_by,
                'medication_supervised_by_specify' =>  $request->medication_supervised_by_specify,
                'delusions' =>  $request->delusions,
                'hallucination' =>  $request->hallucination,
                'behavior' =>  $request->behavior,
                'blunted_affect' =>  $request->blunted_affect,
                'depression' =>  $request->depression,
                'anxiety' =>  $request->anxiety,
                'disorientation' =>  $request->disorientation,
                'uncooperativeness' =>  $request->uncooperativeness,
                'poor_impulse_control' =>  $request->poor_impulse_control,
                'others' =>  $request->others,
                'other_specify_details' =>  $request->other_specify_details,
                'ipsychopathology_remarks' =>  $request->ipsychopathology_remarks,
                'risk_of_violence' =>  $request->risk_of_violence,
                'risk_of_suicide' =>  $request->risk_of_suicide,
                'risk_of_other_deliberate' =>  $request->risk_of_other_deliberate,
                'risk_of_severe' =>  $request->risk_of_severe,
                'risk_of_harm' =>  $request->risk_of_harm,
                'changes_in_teratment' =>  $request->changes_in_teratment,
                'akathisia' =>  $request->akathisia,
                'acute_dystonia' =>  $request->acute_dystonia,
                'parkinsonism' =>  $request->parkinsonism,
                'tardive_dyskinesia' =>  $request->tardive_dyskinesia,
                'tardive_dystonia' =>  $request->tardive_dystonia,
                'others_specify' =>  $request->others_specify,
                'side_effects_remarks' =>  $request->side_effects_remarks,
                'social_performance' =>  $request->social_performance,
                'psychoeducation' =>  $request->psychoeducation,
                'coping_skills' =>  $request->coping_skills,
                'adl_training' =>  $request->adl_training,
                'supported_employment' =>  $request->supported_employment,
                'family_intervention' =>  $request->family_intervention,
                'intervention_others' =>  $request->intervention_others,
                'remarks' =>  $request->remarks,
                'employment_past_months' =>  $request->employment_past_months,
                'if_employment_yes' =>  $request->if_employment_yes,
                'psychiatric_clinic' =>  $request->psychiatric_clinic,
                'im_depot_clinic' =>  $request->im_depot_clinic,
                'next_community_visit' =>  $request->next_community_visit,
                'comments' =>  $request->comments,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'staff_name' =>  $request->staff_name,
                'designation' =>  $request->designation,
                'status' => "0",
                'appointment_details_id' => $request->appId,
                'additional_diagnosis' => $additional_diagnosis,
                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
            ];

            try {
                $HOD = CpsProgressNote::create($CpsProgress);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Note Successfully8", "code" => 200]);
        }
    }
}
