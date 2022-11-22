<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychologyReferral extends Model
{
    use HasFactory;
    protected $table = 'psychology_referral';
    protected $fillable = ['added_by', 'patient_id', 'appointment_details_id',
    'is_deleted', 'patient_acknowledged','diagnosis_id','reason_referral_assessment',
    'reason_referral_assessment_other','reason_referral_intervention','reason_referral_intervention_other','location_services','type_diagnosis_id',
    'category_services','services_id','code_id','sub_code_id','complexity_services','outcome',
    'medication_des','status','created_at'];
}
