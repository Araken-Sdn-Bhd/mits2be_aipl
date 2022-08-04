<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousOrCurrentJobRecord extends Model
{
    use HasFactory;

    protected $table = 'previous_or_current_job_record';
    protected $fillable = [
        'list_previous_current_job_id',
        'patient_id',
        'job',
        'salary',
        'duration',
        'reason_for_quit',
        'created_at'
    ];
}
