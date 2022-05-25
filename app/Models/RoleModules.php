<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModules extends Model
{
    use HasFactory;
    protected $table = 'module_role';

    public function modules()
    {
        return $this->hasOne('APP\Models\Modules', 'parent_module_id', 'id');
    }
}
