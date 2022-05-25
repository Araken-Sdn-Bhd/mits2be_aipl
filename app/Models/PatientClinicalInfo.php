<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientClinicalInfo extends Model
{
    use HasFactory;
    protected $table = 'patient_clinical_information';
    protected $fillable = ['id', 'added_by', 'patient_mrn_id','patient_id', 'temperature', 'blood_pressure', 'pulse_rate', 'weight','height', 'bmi', 'waist_circumference','status','created_at'];

}
