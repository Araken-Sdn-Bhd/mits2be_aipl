<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientCounsellorClerkingNotes extends Model
{
    use HasFactory;
    protected $table = 'patient_counsellor_clerking_notes';
    protected $fillable = ['id', 'added_by','patient_mrn_id','clinical_summary', 
    'background_history', 'clinical_notes',  'diagnosis_id','management', 
    'location_services_id','type_diagnosis_id','category_services',
    'services_id','code_id','sub_code_id','complexity_services_id','outcome_id',
    'medication_des','status','created_at'];
}
