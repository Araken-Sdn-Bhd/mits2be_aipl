<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LASERAssesmenForm extends Model
{
    use HasFactory;
    protected $table = 'laser_assesmen_form';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'pre_contemplation',
        'contemplation',
        'action',
        'location_of_service',
        'type_of_diagnosis',
        'category_of_services',
        'services',
        'complexity_of_services',
        'outcome',
        'icd_9_code',
        'icd_9_subcode',
        'additional_code_id',
        'additional_diagnosis',
        'additional_subcode',
        'medication_prescription',
        'created_at',
        'is_deleted'
    ];
}
