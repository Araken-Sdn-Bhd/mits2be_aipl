<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptTest extends Model
{
    use HasFactory;
    protected $table = 'attempt_test';
    public $timestamps = true;
    protected $fillable = ['id', 'shharp_reg_id','added_by', 'patient_mrn_id', 'test_name', 'question_id', 'answer_id', 'status', 'created_at', 'updated_at', 'test_section_name', 'user_ip_address'];
}
