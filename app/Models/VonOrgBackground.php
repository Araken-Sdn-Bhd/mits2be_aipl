<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VonOrgBackground extends Model
{
    use HasFactory;
    protected $table = 'von_org_background';
    protected $fillable = [
        'added_by',
        'org_name',
        'org_reg_number',
        'org_desc',
        'org_email',
        'org_phone',
        'created_at'
    ];
}
