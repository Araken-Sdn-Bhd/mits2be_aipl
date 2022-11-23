<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationDischargeNote extends Model
{
    use HasFactory;
    protected $table = 'consultation_discharge_note';
    protected $fillable = ['appointment_details_id', 'added_by', 'patient_id','diagnosis_id','category_discharge','comment',
    'specialist_name_id','date','location_services','type_diagnosis_id',
    'category_services','services_id','code_id','sub_code_id','complexity_services','outcome',
    'medication_des','status','created_at'];
}
