<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientSuicidalRiskAssesment extends Model
{
    use HasFactory;
    protected $table = 'patient_suicide_risk_assessment';
    protected $fillable = ['id','Type','risk_level','risk','suicidal_intent','status','added_by'];
}
