<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAttachment extends Model
{
    use HasFactory;
    protected $table = 'patient_attachment';
    protected $fillable = ['added_by','added_by','patient_id','file_name','uploaded_path','status','created_at'];
}
