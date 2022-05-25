<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharpRegistrationFinalStep extends Model
{
    use HasFactory;
    protected $table = 'sharp_registraion_final_step';
    protected $fillable = [
        'added_by',
        'patient_id',
        'risk',
        'protective',
        'self_harm',
        'suicide_risk',
        'hospital_mgmt',
        'status',
        'created_at',
        'updated_at'
    ];
}
