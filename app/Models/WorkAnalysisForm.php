<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAnalysisForm extends Model
{
    use HasFactory;
    protected $table = "work_analysis_forms";
    protected $fillable = ["added_by", "patient_id", 'appointment_details_id',
    'is_deleted', "company_name","company_address1","company_address2","company_address3", "state_id", "city_id","postcode_id",
    "supervisor_name", "email", "position","job_position","client_name","current_wage","wage_specify","wage_change_occur",
    "change_in_rate","from","to","on_date","works_hour_week","work_schedule","no_of_current_employee","no_of_other_employee","during_same_shift",
    "education_level", "grade", "job_experience_year","job_experience_months","others",
    "location_services","type_diagnosis_id","category_services","services_id","code_id",
    "sub_code_id","complexity_services","outcome","medication_des","status"];
}
