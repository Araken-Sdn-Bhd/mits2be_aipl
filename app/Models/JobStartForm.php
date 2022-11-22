<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStartForm extends Model
{
    use HasFactory;

    protected $table = 'job_start_form';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'client',
        'employment_specialist',
        'case_manager',
        'first_date_of_work',
        'job_title',
        'duties_field',
        'rate_of_pay',
        'benefits_field',
        'work_schedule',
        'disclosure',
        'name_of_employer',
        'name_of_superviser',
        'address',
        'location_of_service',
        'type_of_diagnosis',
        'category_of_services',
        'services',
        'complexity_of_services',
        'outcome',
        'icd_9_code',
        'icd_9_subcode',
        'medication_prescription',
        'created_at',
        'is_deleted'
    ];
}
