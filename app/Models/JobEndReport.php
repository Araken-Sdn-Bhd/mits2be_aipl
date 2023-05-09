<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobEndReport extends Model
{
    use HasFactory;

    protected $table = 'job_end_report';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'name',
        'job_title',
        'employer_name',
        'job_start_date',
        'job_end_date',
        'changes_in_job_duties',
        'reason_for_job_end',
        'clients_perspective',
        'staff_comments_regarding_job',
        'employer_comments',
        'type_of_support',
        'person_wish_for_another_job',
        'clients_preferences',
        'staff_name',
        'date',
        'location_of_service',
        'type_of_diagnosis',
        'add_type_of_diagnosis',
        'category_of_services',
        'services',
        'complexity_of_services',
        'outcome',
        'icd_9_code',
        'icd_9_subcode',
        'medication_prescription',
        'status',
        'created_at',
        'is_deleted',
        'add_code_id',
        'add_sub_code_id'
    ];
}
