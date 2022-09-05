<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientRegistration;
use App\Models\ServiceRegister;
use App\Models\HospitalBranchTeamManagement;

class PatientAppointmentDetails extends Model
{
    use HasFactory;
    protected $table = 'patient_appointment_details';
    protected $fillable = ['id', 'added_by', 'patient_mrn_id', 'nric_or_passportno', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'status', 'appointment_status','end_appoitment_date','staff_id', 'created_at'];

    public function patient()
    {
        return $this->belongsTo(PatientRegistration::class, "patient_mrn_id", "id")->select('id', 'name_asin_nric', 'salutation_id', 'nric_no', 'patient_mrn');
    }

    public function service()
    {
        return $this->belongsTo(ServiceRegister::class, "appointment_type", "id");
    }

    public function team()
    {
        return $this->hasOne(HospitalBranchTeamManagement::class, "assign_team", "id");
    }
}
