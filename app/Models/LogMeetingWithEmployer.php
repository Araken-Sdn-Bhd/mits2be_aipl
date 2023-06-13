<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMeetingWithEmployer extends Model
{
    use HasFactory;

    protected $table = 'log_meeting_with_employer';
    protected $fillable = [
        'added_by',
        'patient_id',
        'date',
        'appointment_details_id',
        'is_deleted',
        'employee_name',
        'company_name',
        'purpose_of_meeting',
        'discussion_start_time',
        'discussion_end_time',
        'staff_name',
        'location_services',
        'type_diagnosis_id',
        'category_services','services_id',
        'code_id',
        'sub_code_id',
        'complexity_services',
        'outcome',
        'additional_code_id',
        'additional_diagnosis',
        'additional_subcode',
        'medication_des',
        'status',
        'created_at'
    ];

}
