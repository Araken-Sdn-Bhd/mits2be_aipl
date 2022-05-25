<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalReferralForm extends Model
{
    use HasFactory;
    protected $table = 'internal_referral_form';
    protected $fillable = ['added_by', 'patient_mrn_id','diagnosis','reason_for_referral','summary',
    'management','medication','name','designation','hospital','location_services','type_diagnosis_id',
    'category_services','services_id','code_id','sub_code_id','complexity_services','outcome',
    'medication_des','status','created_at'];
}
