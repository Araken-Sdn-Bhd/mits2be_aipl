<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientShharpRegistrationRiskProtective extends Model
{
    use HasFactory;
    protected $table = 'patient_shharp_registration_risk_protective_factors';
    protected $fillable = ['added_by','Question', 'Options1','Options2','Type', 'status'];
    
}
