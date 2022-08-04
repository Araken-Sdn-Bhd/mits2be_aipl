<?php

namespace App\Models;
use App\Models\PatientRegistration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharpRegistrationFinalStep extends Model
{
    use HasFactory;
    protected $table = 'sharp_registraion_final_step';
    protected $fillable = [
        'added_by',
        'patient_id',
        'risk',
        'protective',
        'self_harm',
        'harm_time',
        'harm_date',
        'suicide_risk',
        'hospital_mgmt',
        'status',
        'created_at',
        'updated_at',
        'risk_factor_yes_value'
    ];
    public function patient()
    {
        return $this->hasMany(PatientRegistration::class, "id", "patient_id");
    }
}
