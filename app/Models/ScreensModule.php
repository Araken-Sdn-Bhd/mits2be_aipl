<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreensModule extends Model
{
    use HasFactory;
    protected $table = 'screen_modules';
    protected $fillable = ['added_by', 'module_code', 'module_name', 'module_short_name', 'module_order'];
}
