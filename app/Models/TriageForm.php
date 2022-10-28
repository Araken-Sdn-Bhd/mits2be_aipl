<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TriageForm extends Model
{
    use HasFactory;
    protected $table = 'triage_form';
    protected $fillable = ['added_by', 'patient_mrn_id', 'risk_history_assressive', 'risk_history_criminal', 'risk_history_detereotation',
    'risk_history_neglect', 'risk_history_suicidal_idea', 'risk_history_suicidal_attempt', 'risk_history_homicidal_idea', 'risk_history_homicidal_attempt', 'risk_history_aggressive_idea',
    'risk_history_aggressive_attempt', 'risk_social_has_no_family', 'risk_homeless', 'capacity_cannot_give_commitment', 'capacity_showed_no_interest',
    'treatment_checked','treatment_given_appointment','treatment_given_regular','placement_referred','placement_discharge','screening_id','score',
    'appointment_date', 'appointment_time', 'appointment_duration', 'appointment_type','appointment_type_visit', 'appointment_patient_category', 'appointment_assign_team',
    'location_services_id','type_diagnosis_id','category_services','services_id','code_id','sub_code_id','complexity_services_id','outcome_id',
    'medication_des','status','created_at'];

}
