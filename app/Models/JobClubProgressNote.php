<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobClubProgressNote extends Model
{
    use HasFactory;
    protected $table = 'job_club_progress_note';
    protected $fillable = ['id', 'name', 'mrn','patient_mrn_id', 'appointment_details_id', 'added_by', 'date', 'time', 'staff_name', 'work_readiness',
    'progress_note', 'management_plan', 'location_service', 'diagnosis_type', 'service_category',
    'services_id', 'code_id', 'sub_code_id', 'complexity_service', 'outcome',
    'medication', 'status','created_at','add_code_id', 'add_sub_code_id'];
}
