<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResultSuicidalRisk extends Model
{
    use HasFactory;
    public $table = 'test_result_suicidal_risk';
    public $fillable = ['added_by', 'patient_id','ip_address', 'result', 'created_at', 'updated_at'];
}
