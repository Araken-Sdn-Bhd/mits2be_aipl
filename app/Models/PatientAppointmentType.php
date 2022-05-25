<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAppointmentType extends Model
{
    use HasFactory;
    protected $table = 'patient_appointment_type';
    protected $fillable = ['appointment_type_name','status'];
}
