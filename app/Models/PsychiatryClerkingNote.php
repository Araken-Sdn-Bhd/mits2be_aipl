<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsychiatryClerkingNote extends Model
{
    use HasFactory;
    protected $table = 'psychiatry_clerking_note';
    protected $fillable = ['id','appointment_details_id', 'added_by','patient_mrn_id','chief_complain', 'presenting_illness',
    'background_history', 'general_examination', 'mental_state_examination',
    'diagnosis_id','management', 'discuss_psychiatrist_name', 'date','time',
    'location_services_id','type_diagnosis_id','category_services',
    'services_id','code_id','sub_code_id','complexity_services_id','outcome_id',
    'medication_des','status','created_at','additional_diagnosis', 'additional_subcode','additional_code_id', 'is_deleted'];
}