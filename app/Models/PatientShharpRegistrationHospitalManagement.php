<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientShharpRegistrationHospitalManagement extends Model
{
    use HasFactory;
    protected $table = 'patient_shharp_registration_hospital_management';
    protected $fillable = ['added_by','patient_mrn_no', 'referral_or_contact','referral_or_contact_other','arrival_mode','arrival_mode_other',
    'date','time','physical_consequences','physical_consequences_des','patient_admitted',
    'patient_admitted_des','discharge_status','discharge_date', 'discharge_number_days_in_ward',
    'main_psychiatric_diagnosis','external_cause_inquiry','discharge_psy_mx','discharge_psy_mx_des','status', 'additional_diagnosis','additional_external_cause_injury'];
}
