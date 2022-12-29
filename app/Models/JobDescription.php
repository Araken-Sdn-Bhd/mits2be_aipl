<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDescription extends Model
{
    use HasFactory;
    protected $table = 'job_description';
    protected $fillable = [
        'id',
        'work_analysis_form_id',
        'patient_id',
        'task_description',
        'objectives',
        'procedure',
        'rate_of_time',
        'created_at'
    ];
}
