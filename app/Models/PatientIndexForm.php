<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientIndexForm extends Model
{
    use HasFactory;
    protected $table = 'patient_index_form';
    protected $fillable = ['id', 'diagnosis','date_onset','date_of_diagnosis','date_of_referral',
    'date_of_first_assessment','hospital','latest_admission_date','date_of_discharge','reason',
    'adherence_to_medication','aggresion','suicidality','
    criminality','age_first_started','heroin','cannabis','ats','inhalant','alcohol',
    'tobacco','others','past_medical','background_history','who_das_assessment','mental_state_examination',
    'summary_of_issues','management_plan','location_of_services','type_of_diagnosis','category_of_services','services_id',
    'added_by','patient_mrn_id','code_id','sub_code_id',
    'complexity_of_service','outcome','medication','zone','case_manager','specialist','date','status','created_at'];
}
