<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenPageModule extends Model
{
    use HasFactory;
    protected $table = 'screens';
    protected $fillable = ['added_by', 'module_id', 'module_name', 'sub_module_id', 'sub_module_name', 'screen_name', 'screen_route', 'screen_description'];
}
