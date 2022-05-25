<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerismApplicationForm extends Model
{
    use HasFactory;
    protected $table = 'volunteerism_application_form';
    protected $fillable = ['added_by','volunteer_individual_id','volunteering_experience_yes', 'volunteering_experience_yes_des', 'volunteering_experience_no', 'health_professional_yes', 'health_professional_doc', 'health_professional_no','relevant_mentari_service', 'relevant_mentari_service_other', 'day', 'time', 'status'];
}
