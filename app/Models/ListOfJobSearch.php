<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListOfJobSearch extends Model
{
    use HasFactory;

    protected $table = 'list_of_job_search';
    protected $fillable = [
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
        'medication_des',
        'status',
        'created_at'
    ];
}
