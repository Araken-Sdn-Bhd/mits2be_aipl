<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultRoleAccess extends Model
{
    use HasFactory;
    protected $table = 'default_role_access';
    protected $fillable = ['role_id','module_id','sub_module_id','screen_id'];
}
