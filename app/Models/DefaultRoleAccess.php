<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScreenPageModule;


class DefaultRoleAccess extends Model
{
    use HasFactory;
    protected $table = 'default_role_access';
    protected $fillable = ['role_id','module_id','sub_module_id','screen_id'];

    public function screens()
    {
        return $this->hasMany(ScreenPageModule::class, 'screen_id', 'id');
    }

}
