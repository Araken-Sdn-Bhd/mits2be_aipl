<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounsellingProgressNote extends Model
{
    use HasFactory;
    protected $table = 'counselling_progress_note';
    protected $fillable = ['id', 'added_by','patient_mrn_id','therapy_date', 'therapy_time', 
    'diagnosis_id','frequency_session','frequency_session_other','model_therapy','model_therapy_other',
    'mode_therapy','mode_therapy_other','comment_therapy_session','patent_condition','patent_condition_other',
    'comment_patent_condition','session_details','session_issues','conduct_session','outcome_session',
    'transference_session','duration_session','other_comment_session','name','designation',
    'date_session','location_services_id','type_diagnosis_id','category_services',
    'services_id','code_id','sub_code_id','complexity_services_id','outcome_id',
    'medication_des','status','created_at'];
}
