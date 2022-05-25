<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffers extends Model
{
    use HasFactory;
    protected $table = 'job_offers';
    protected $fillable = [
        'id',
        'added_by',
        'company_id',
        'position_offered',
        'position_location_1',
        'position_location_2',
        'position_location_3',
        'education_id',
        'duration_of_employment',
        'salary_offered',
        'work_schedule',
        'is_transport',
        'is_accommodation',
        'work_requirement',
        'branch_id',
        'job_availability',
        'status',
        'created_at'
    ];
}
