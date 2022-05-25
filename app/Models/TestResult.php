<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;
    public $table = 'attempt_test_result';
    public $fillable = ['added_by', 'patient_id', 'test_name', 'test_section_name', 'result', 'created_at', 'updated_at'];
}
