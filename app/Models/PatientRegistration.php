<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientAppointmentDetails;
use App\Models\GeneralSetting;
use App\Models\ServiceRegister;
use App\Models\Citizenship;
use App\Models\SharpRegistrationFinalStep;

class PatientRegistration extends Model
{
    use HasFactory;
    protected $table = 'patient_registration';
    protected $fillable = [
        'added_by', 'salutation_id', 'name_asin_nric', 'citizenship', 'nric_type', 'nric_no', 'passport_no', 'expiry_date', 'country_id', 'sex', 'birth_date', 'age', 'mobile_no', 'house_no', 'added_by', 'hospital_mrn_no', 'mintari_mrn_no', 'services_type', 'referral_type', 'referral_letter', 'address1', 'address2', 'address3', 'state_id', 'city_id', 'postcode', 'race_id', 'religion_id', 'marital_id',
        'accomodation_id', 'education_level', 'occupation_status', 'fee_exemption_status', 'occupation_sector',
        'kin_name_asin_nric', 'kin_relationship_id', 'kin_mobile_no', 'kin_house_no', 'kin_address1',
        'kin_address2', 'kin_address3', 'kin_state_id', 'kin_city_id', 'kin_postcode', 'drug_allergy',
        'drug_allergy_description', 'traditional_medication', 'kin_postcode', 'traditional_description',
        'other_allergy', 'other_description', 'status','patient_need_triage_screening','kin_nric_no'
    ];

    public function appointments()
    {
        return $this->hasMany(PatientAppointmentDetails::class, "patient_mrn_id", "id");
    }
    public function salutation()
    {
        return $this->hasMany(GeneralSetting::class, "id", "salutation_id");
    }
    public function service()
    {
        return $this->belongsTo(ServiceRegister::class, "services_type", "id");
    }
    public function gender()
    {
        return $this->hasMany(GeneralSetting::class, "id", "sex");
    }
    public function maritialstatus()
    {
        return $this->hasMany(GeneralSetting::class, "id", "marital_id");
    }
    public function citizenships()
    {
        return $this->hasMany(Citizenship::class, "id", "citizenship");
    }
    public function sharpharm()
    {
        return $this->hasMany(SharpRegistrationFinalStep::class, "id", "patient_id");
    }
}
