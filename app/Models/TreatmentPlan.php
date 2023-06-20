<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentPlan extends Model
{
    use HasFactory;
    protected $table = 'treatment_plan';
    protected $fillable = [
        'treatment_plan_id',
        'patient_care_plan_id',
        'issues',
        'goals',
        'management',
        'who',
        'update_at',
        'created_at'
    ];
}
