<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientGetIdeaAboutMethod extends Model
{
    use HasFactory;
    protected $table = 'patient_get_idea_about_method';
    protected $fillable = ['id', 'added_by','name','status'];
}
