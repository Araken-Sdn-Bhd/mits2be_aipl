<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerIndividualApplicationForm extends Model
{
    use HasFactory;
    protected $table = 'volunteer_individual_application_form';
    protected $fillable = [
        'added_by',
        'volunteer_individual_id', 
        'name', 
        'date', 
        'email', 
        'phone_number', 
        'address', 
        'postcode_id',
        'city_id', 
        'state_id', 
        'highest_education', 
        'current_occupation', 
        'hospital_id', 
        'areas_involvement', 
        'status',
        'created_at',
        'updated_at'
    ];
}
