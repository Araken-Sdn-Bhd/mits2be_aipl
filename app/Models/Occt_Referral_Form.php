<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occt_Referral_Form extends Model
{
    use HasFactory;
    protected $table = 'occt_referral_form';
    protected $fillable = ['id', 'appointment_details_id','added_by', 'patient_mrn_id','appointment_details_id',
    'is_deleted', 'referral_location','date','diagnosis_id',
    'referral_clinical_assessment','referral_clinical_assessment_other','referral_clinical_intervention',
    'referral_clinical_intervention_other', 'referral_clinical_promotive_program', 'referral_name', 'referral_designation',
    'location_services','type_diagnosis_id','add_type_diagnosis_id','category_services','services_id','code_id','sub_code_id','add_code_id','add_sub_code_id','complexity_services','outcome',
    'medication_des','status','created_at'];
}
