<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutreachProjectCollaborationForm extends Model
{
    use HasFactory;
    protected $table = 'outreach_project_collaboration_form';
    protected $fillable = ['added_by','volunteer_individual_id', 'project_name', 'project_background', 'project_objectives', 
    'target_audience', 'number_participants', 'estimated_budget','project_scopes', 'project_location_mentari', 
    'project_location_mentari_location', 'project_location_others', 'project_location_others_des', 'measure_target_outcome', 
    'planned_follow_projects','relevant_mentari_service','relevant_mentari_service_other','status'];
}
