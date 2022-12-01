<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtpProgressNote extends Model
{
    use HasFactory;

    protected $table = 'etp_progress_note';
    protected $fillable =  ['id', 'name','appointment_details_id', 'mrn','patient_mrn_id','added_by', 'date', 'time', 'staff_name', 'work_readiness',
    'progress_note', 'management_plan', 'location_service', 'diagnosis_type', 'service_category',
    'services_id', 'code_id', 'sub_code_id', 'complexity_service', 'outcome',
    'medication', 'status','created_at'];
}
