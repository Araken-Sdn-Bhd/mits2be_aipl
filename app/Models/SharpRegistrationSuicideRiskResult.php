<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharpRegistrationSuicideRiskResult extends Model
{
    use HasFactory;

    protected $table = 'sharp_register_suicide_risk';
    protected $fillable = [
        'added_by',
        'patient_id',
        'result',
        'created_at',
        'updated_at'
    ];
}
