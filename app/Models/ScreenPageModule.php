<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DefaultRoleAccess;

class ScreenPageModule extends Model
{
    use HasFactory;
    protected $table = 'screens';
    protected $fillable = ['added_by', 'module_id', 'module_name', 'sub_module_id', 'sub_module_name', 'screen_name', 'screen_route', 'screen_route_alt', 'screen_description','icon','index_val'];

    public function roles()
    {
        return $this->belongsTo(DefaultRoleAccess::class, 'screen_id', 'id');
    }

}
