<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListJobClub extends Model
{
    use HasFactory;

    protected $table = 'list_job_club';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'job_club',
        'location_services',
        'type_diagnosis_id',
        'add_type_of_diagnosis',
        'category_services',
        'services_id',
        'code_id',
        'sub_code_id',
        'complexity_services',
        'outcome',
        'add_code_id',
        'add_sub_code_id',
        'medication_des',
        'status',
        'created_at',
        'is_deleted'
    ];
}
