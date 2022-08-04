<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenSubModule extends Model
{
    use HasFactory;
    protected $table = 'screen_sub_modules';
    protected $fillable = ['added_by', 'sub_module_code', 'sub_module_name', 'module_name', 'module_id','icon','index_val'];
}
