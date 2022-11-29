<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPSReferralForm extends Model
{
    use HasFactory;

    protected $table = 'cps_referral_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'treatment_needs_individual',
        'treatment_needs_medication',
        'treatment_needs_support',
        'location_of_service',
        'type_of_diagnosis',
        'category_of_services',
        'services',
        'complexity_of_services',
        'outcome',
        'icd_9_code',
        'icd_9_subcode',
        'medication_des',
        'medication_referrer_name',
        'medication_referrer_designation',
        'created_at',
        'appointment_details_id',
        'is_deleted',
        'status',
        'id',
        'appointment_details_id',
    ];
}
