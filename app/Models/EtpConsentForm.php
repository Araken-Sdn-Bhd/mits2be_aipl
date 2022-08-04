<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EtpConsentForm extends Model
{
    use HasFactory;
    protected $table = 'etp_consent_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'consent_for_participation',
        'consent_for_disclosure',
        'created_at'
    ];
}
