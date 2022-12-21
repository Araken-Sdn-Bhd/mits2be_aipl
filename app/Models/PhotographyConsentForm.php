<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographyConsentForm extends Model
{
    use HasFactory;
    protected $table = 'photography_consent_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'photography_consent_form_agree',
        'created_at',
        'appointment_details_id'
    ];
}
