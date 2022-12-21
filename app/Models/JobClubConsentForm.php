<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobClubConsentForm extends Model
{
    use HasFactory;

    protected $table = 'job_club_consent_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'consent_for_participation',
        'created_at',
        'appointment_details_id'
    ];
}
