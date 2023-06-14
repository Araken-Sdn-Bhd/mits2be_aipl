<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListOfJobSearch extends Model
{
    use HasFactory;

    protected $table = 'list_of_job_search';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'job_listed',
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
