<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenAccessRoles extends Model
{
    use HasFactory;
    protected $table = 'screen_access_roles';
    protected $fillable = [
        'added_by',
        'module_id',
        'sub_module_id',
        'screen_id',
        'hospital_id',
        'branch_id',
        'team_id',
        'staff_id'
    ];
}
