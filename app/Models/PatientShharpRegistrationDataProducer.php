<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientShharpRegistrationDataProducer extends Model
{
    use HasFactory;
    protected $table = 'patient_shharp_registration_data_producer';
    protected $fillable = ['id','added_by','patient_mrn_id', 'name_registering_officer', 'hospital_name', 'designation', 
    'psychiatrist_name', 'reporting_date','status'];
    
}
