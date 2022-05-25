<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRiskProtectiveAnswer extends Model
{
    use HasFactory;
    protected $table = 'patient_risk_protective_answers';
    protected $fillable = [
        'added_by',
        'patient_mrn_id',
        'factor_type',
        'QuestionId',
        'Answer',
        'Answer_text',
        'status',
        'created_at',
        'updated_at'
    ];
}
