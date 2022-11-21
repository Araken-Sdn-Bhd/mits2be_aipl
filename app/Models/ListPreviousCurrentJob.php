<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPreviousCurrentJob extends Model
{
    use HasFactory;

    protected $table = 'list_previous_current_job';
    protected $fillable = [
        'added_by',
        'patient_id',
        'appointment_details_ids',
        'is_deleted',
        'job_list_current_previous',
        'location_services',
        'type_diagnosis_id',
        'category_services','services_id',
        'code_id',
        'sub_code_id',
        'complexity_services',
        'outcome',
        'medication_des',
        'status',
        'created_at'
    ];
}
