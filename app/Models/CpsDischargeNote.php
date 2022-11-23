<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsDischargeNote extends Model
{
    use HasFactory;
    protected $table = 'cps_discharge_note';
    protected $fillable =  ['id', 'patient_mrn_id', 'appointment_details_id', 'added_by', 'name', 'mrn', 'cps_discharge_date', 'time', 'staff_name', 'diagnosis',
    'post_intervention', 'psychopathology', 'psychosocial', 'potential_risk', 'category_of_discharge',
    'discharge_diagnosis','outcome_medication','location_service','diagnosis_type','service_category',
    'services_id', 'code_id', 'sub_code_id', 'complexity_services', 'outcome',
    'medication', 'specialist_name', 'verification_date', 'case_manager','date', 'status','created_at'];
}
