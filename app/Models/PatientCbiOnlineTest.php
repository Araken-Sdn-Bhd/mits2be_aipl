<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientCbiOnlineTest extends Model
{
    use HasFactory;
    protected $table = 'patient_cbi_onlinetest';
    protected $fillable = ['id', 'added_by', 'Type', 'status', 'Question', 'question_ml', 'Answer0', 'Answer1', 'Answer2', 'Answer3', 'Answer4', 'Answer5', 'question_order', 'status'];
}
