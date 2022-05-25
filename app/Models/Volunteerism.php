<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteerism extends Model
{
    use HasFactory;
    protected $table = 'volunteerism';
    protected $fillable = [
        'added_by',
        'parent_section_id',
        'parent_section',
        'is_voluneering_exp',
        'exp_details',
        'is_mental_health_professional',
        'resume',
        'mentari_services',
        'available_date',
        'available_time',
        'created_at'
    ];
}
