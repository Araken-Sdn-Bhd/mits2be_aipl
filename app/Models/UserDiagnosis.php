<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiagnosis extends Model
{
    use HasFactory;
    protected $table = 'user_diagnosis';
    protected $fillable = ['app_id','patient_id','diagnosis_id','add_diagnosis_id','code_id', 'sub_code_id', 'add_code_id', 'add_sub_code_id',
     'outcome_id','category_services','remarks','created_at','updated_at'];

}
