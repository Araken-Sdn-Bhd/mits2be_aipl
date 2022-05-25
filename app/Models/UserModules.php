<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModules extends Model
{
    protected $table = 'allowed_modules_to_user';

    public function modules(){
        return $this->hasOne('APP\Models\Modules','module_id','id');
    }
}
