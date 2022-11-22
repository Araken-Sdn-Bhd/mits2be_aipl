<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\RoleModules;

class Roles extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'role_name',
        'status',
        'requested_by',
        'created_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function modules()
    {
        return $this->hasMany(RoleModules::class, 'role_id', 'id');
    }
}
