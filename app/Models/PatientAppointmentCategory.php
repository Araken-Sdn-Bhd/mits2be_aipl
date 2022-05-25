<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAppointmentCategory extends Model
{
    use HasFactory;
    protected $table = 'patient_appointment_category';
    protected $fillable = ['appointment_category_name','status'];
}
