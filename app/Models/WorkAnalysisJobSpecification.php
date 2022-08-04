<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAnalysisJobSpecification extends Model
{
    use HasFactory;
    protected $table = 'work_analysis_job_specification';
    protected $fillable = ['id', 'patient_id', 'work_analysis_form_id', 'question_name', 'answer', 'comment','created_at'];
}
