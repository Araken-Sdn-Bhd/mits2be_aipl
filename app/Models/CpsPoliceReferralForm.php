<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsPoliceReferralForm extends Model
{
    use HasFactory;
    protected $table = 'cps_police_referral_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'to',
        'officer_in_charge',
        'the_above_patient_ongoing',
        'name',
        'designation',
        'created_at'
    ];
}
