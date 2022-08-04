<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehabReferralAndClinicalForm extends Model
{
    use HasFactory;

    protected $table = 'rehab_referral_and_clinical_form';
    protected $fillable = ['added_by', 'patient_mrn_id','patient_referred_for','diagnosis','date_onset','date_of_referral','no_of_admission',
    'latest_admission_date','current_medication','alerts','education_level','aggresion',
    'suicidality','criminality','age_first_started','heroin','cannabis','ats','inhalant',
    'alcohol','tobacco','others','other_information','location_services','type_diagnosis_id',
    'category_services','services_id','code_id','sub_code_id','complexity_services','outcome',
    'medication_des','referral_name','designation','status','created_at']; 
}
