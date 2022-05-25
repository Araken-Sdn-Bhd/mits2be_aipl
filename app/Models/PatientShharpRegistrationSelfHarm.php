<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientShharpRegistrationSelfHarm extends Model
{
    use HasFactory;
    protected $table = 'patient_shharp_registration_self_harm';
    protected $fillable = ['added_by', 'date', 'patient_mrn_no','time', 'place_occurence', 'method_of_self_harm',
    'overdose_poisoning','other', 'patient_get_idea_about_method', 'specify_patient_actual_word',
    'suicidal_intent', 'suicidal_intent_yes','suicidal_intent_other', 'status'];
}
