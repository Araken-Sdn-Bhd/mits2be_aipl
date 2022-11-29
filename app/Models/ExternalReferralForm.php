<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalReferralForm extends Model
{
    use HasFactory;
    protected $table = 'external_referral_form';
    protected $fillable = ['appointment_details_id', 'id', 'added_by', 'patient_mrn_id','history','examination','diagnosis','result_of_investigation','summary',
    'treatment','purpose_of_referral','location_services','type_diagnosis_id',
    'category_services','services_id','code_id','sub_code_id','complexity_services','outcome',
    'medication_des','name','designation','hospital','status','created_at','is_deleted'];

}
