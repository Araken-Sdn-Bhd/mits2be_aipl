<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CpsProgressNote;
use App\Models\CpsProgressList;

class CpsProgressNoteController extends Controller
{
    //

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
            'cps_date' => 'required|date',
            'cps_time' => 'required',
            'cps_seen_by' => 'required',
            'cps_date_discussed' => 'required|date',
            'cps_time_discussed' => 'required',
            'cps_discussed_with' => 'required|integer',
            'visit_date' => 'required|date',
            'visit_time' => 'required',
            'informants_name' => 'required|string',
            'informants_relationship' => 'required|string',
            'informants_contact' => 'required|string',
            'case_manager' => 'required|integer',
            'visited_by' => 'required|string',
            'visit_outcome' => 'required|integer',
            'current_intervention' => 'required',
            'compliance_treatment' => 'required',
            'medication_supervised_by' => 'required',
            'delusions' => 'required|string',
            'hallucination' => 'required|string',
            'behavior' => 'required|string',
            'blunted_affect' => 'required|string',
            'depression' => 'required|string',
            'anxiety' => 'required|string',
            'disorientation' => 'required|string',
            'uncooperativeness' => 'required|string',
            'poor_impulse_control' => 'required|string',
            'others' => 'required|string',
            'ipsychopathology_remarks' => 'required|string',
            'risk_of_violence' => 'required|string',
            'risk_of_suicide' => 'required|string',
            'risk_of_other_deliberate' => 'required|string',
            'risk_of_severe' => 'required|string',
            'risk_of_harm' => 'required|string',
            'changes_in_teratment' => 'required|string',
            'akathisia' => 'required|string',
            'acute_dystonia' => 'required|string',
            'parkinsonism' => 'required|string',
            'tardive_dyskinesia' => 'required|string',
            'tardive_dystonia' => 'required|string',
            'others_specify' => 'required|string',
            'side_effects_remarks' => 'required|string',
            'social_performance' => 'required|string',
            'psychoeducation' => 'required|string',
            'coping_skills' => 'required|string',
            'adl_training' => 'required|string',
            'supported_employment' => 'required|string',
            'family_intervention' => 'required|string',
            'intervention_others' => 'required|string',
            'remarks' => 'required|string',
            'employment_past_months' => 'required|string',
            'psychiatric_clinic' => 'required|date',
            'im_depot_clinic' => 'required|date',
            'next_community_visit' => 'required|date',
            'comments' => 'string',
            'location_service' => 'required|integer',
            'diagnosis_type' => 'required|integer',
            'service_category' => 'required|string',
            'complexity_services' => 'required|integer',
            'outcome' => 'required|integer',
            'medication' => '',
            'staff_name' => 'required|string',
            'designation' => 'required|string',
            'id' => ''

        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        
        if($request->id){
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
                    'status' => "1"
                ];
    
                try {
                    CpsProgressNote::where(
                        ['id' => $request->id]
                    )->update($cpsprogressnote);
                    // $HOD = CpsProgressNote::firstOrCreate($cpsprogressnote);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
                }
                return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
            } else if ($request->service_category == 'clinical-work') {
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
                    'status' => "1"
                ];
    
                try {
                    CpsProgressNote::where(
                        ['id' => $request->id]
                    )->update($CpsProgress);
                    // $HOD = CpsProgressNote::firstOrCreate($CpsProgress);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
                }
                return response()->json(["message" => "CPS Progress Note Successfully11", "code" => 200]);
            }
        }else{
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
                'status' => "1"
            ];

            try {
                $HOD = CpsProgressNote::firstOrCreate($cpsprogressnote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $cpsprogressnote, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Form Successfully00", "code" => 200]);
        } else if ($request->service_category == 'clinical-work') {
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
                'status' => "1"
            ];

            try {
                $HOD = CpsProgressNote::firstOrCreate($CpsProgress);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsProgress' => $CpsProgress, "code" => 200]);
            }
            return response()->json(["message" => "CPS Progress Note Successfully11", "code" => 200]);
        }
    }
    }
}
