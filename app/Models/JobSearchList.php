<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSearchList extends Model
{
    use HasFactory;
    protected $table = 'job_search_list';
    protected $fillable = [
        'list_of_job_search_id',
        'appointment_details_id',
        'patient_id',
        'company_name',
        'job_applied',
        'application_date',
        'interview_date',
        'created_at'
    ];
}
