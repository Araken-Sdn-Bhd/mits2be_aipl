<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAlert extends Model
{
    use HasFactory;
    protected $table = 'patient_alert';
    protected $fillable = ['added_by','added_by','patient_id','message','created_at'];
}
