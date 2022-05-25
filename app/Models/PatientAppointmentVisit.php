<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAppointmentVisit extends Model
{
    use HasFactory;
    protected $table = 'patient_appointment_visit';
    protected $fillable = ['appointment_visit_name','status'];
}
