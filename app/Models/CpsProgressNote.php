<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsProgressNote extends Model
{
    use HasFactory;
    protected $table = 'cps_progress_note';
    protected $fillable =  ['id', 'appointment_details_id','is_deleted','patient_mrn_id','added_by', 'cps_date', 'cps_time', 'cps_seen_by', 'cps_date_discussed',
    'cps_time_discussed', 'cps_discussed_with', 'visit_date', 'visit_time', 'informants_name', 'informants_relationship',
    'informants_contact','case_manager','visited_by','visit_outcome','current_intervention','compliance_treatment',
    'medication_supervised_by','medication_supervised_by_specify','delusions','hallucination','behavior','blunted_affect','depression',
    'anxiety','disorientation','uncooperativeness','poor_impulse_control','others','other_specify_details','ipsychopathology_remarks',
    'risk_of_violence','risk_of_suicide','risk_of_other_deliberate','risk_of_severe','risk_of_harm',
    'changes_in_teratment','akathisia','acute_dystonia','parkinsonism','tardive_dyskinesia','tardive_dystonia',
    'others_specify','side_effects_remarks','social_performance','psychoeducation','coping_skills',
    'adl_training','supported_employment','family_intervention','intervention_others','remarks',
    'employment_past_months','if_employment_yes','psychiatric_clinic','im_depot_clinic','next_community_visit',
    'comments','location_service','diagnosis_type','service_category',
    'services_id', 'code_id', 'sub_code_id', 'complexity_services', 'outcome',
    'medication', 'staff_name', 'designation', 'status','created_at'];
}
