<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VonOrgRepresentativeBackground extends Model
{
    use HasFactory;
    protected $table = 'von_org_representative_background';
    protected $fillable = [
        'id',
        'added_by',
        'org_background_id',
        'section',
        'dob',
        'name',
        'position_in_org',
        'email',
        'phone_number',
        'address',
        'postcode_id',
        'city_id',
        'state_id',
        'education_id',
        'occupation_sector_id',
        'branch_id',
        'area_of_involvement',
        'is_agree',
        'status',
        'created_at'
    ];
}
