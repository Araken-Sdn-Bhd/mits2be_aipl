<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInterestList extends Model
{
    use HasFactory;
    protected $table = 'job_interest_list';
    protected $fillable = [
        'job_interest_checklist_id',
        'patient_id',
        'type_of_job',
        'duration',
        'termination_reason',
        'created_at'
    ];
}
