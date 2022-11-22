<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInterestChecklist extends Model
{
    use HasFactory;
    protected $table = 'job_interest_checklist';
    protected $fillable = [
        'added_by',
        'patient_id',
        'interest_to_work',
        'agree_if_mentari_find_job_for_you',
        'client_job_preferences',
        'clerk_job_interester',
        'clerk_job_notes',
        'factory_worker_job_interested',
        'factory_worker_notes',
        'cleaner_job_interested',
        'cleaner_job_notes',
        'security_guard_job_interested',
        'security_guard_notes',
        'laundry_worker_job_interested',
        'laundry_worker_notes',
        'car_wash_worker_job',
        'car_wash_worker_notes',
        'kitchen_helper_job',
        'kitchen_helper_notes',
        'waiter_job_interested',
        'waiter_job_notes',
        'chef_job_interested',
        'chef_job_notes',
        'others_job_specify',
        'others_job_notes',
        'type_of_job',
        'duration',
        'termination_reason',
        'note',
        'planning',
        'patient_consent_interested',
        'location_services',
        'type_diagnosis_id',
        'category_services','services_id',
        'code_id',
        'sub_code_id',
        'complexity_services',
        'outcome',
        'medication_des',
        'status',
        'created_at',
        'appointment_details_id',
        'is_deleted',
    ];
}
