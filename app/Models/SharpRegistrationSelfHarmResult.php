<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharpRegistrationSelfHarmResult extends Model
{
    use HasFactory;
    protected $table = 'sharp_registraion_self_harm_result';
    protected $fillable = [
        'added_by',
        'patient_id',
        'section',
        'section_value',
        'created_at',
        'updated_at'
    ];
}
