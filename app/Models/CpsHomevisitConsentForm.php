<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsHomevisitConsentForm extends Model
{
    use HasFactory;

    protected $table = 'cps_homevisit_consent_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'consent_for_homevisit',
        'consent_for_hereby_already_give_explanation',
        'created_at'
    ];
}
