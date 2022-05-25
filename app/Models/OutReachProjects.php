<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutReachProjects extends Model
{
    use HasFactory;
    protected $table = 'out_reach_projects';
    protected $fillable = [
        'added_by',
        'parent_section_id',
        'parent_section',
        'project_name',
        'project_background',
        'project_objectives',
        'target_audience',
        'no_of_paricipants',
        'time_frame',
        'estimated_budget',
        'project_scopes',
        'project_loaction',
        'project_loaction_value',
        'target_outcome',
        'followup_projects',
        'mentari_services',
        'created_at'
    ];
}
