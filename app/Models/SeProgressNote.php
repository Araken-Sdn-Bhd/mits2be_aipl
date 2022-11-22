<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeProgressNote extends Model
{
    use HasFactory;

    protected $table = 'se_progress_note';
    protected $fillable =  ['id','appointment_details_id', 'name', 'mrn','patient_mrn_id', 'patient_id', 'added_by', 'date', 'time', 'staff_name', 'activity_type',
    'employment_status', 'progress_note', 'management_plan', 'location_service', 'diagnosis_type', 'service_category',
    'services_id', 'code_id', 'sub_code_id', 'complexity_service', 'outcome',
    'medication', 'status','created_at','is_deleted'];
}
