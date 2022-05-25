<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleSettings extends Model
{
    use HasFactory;
    protected $table = 'module_settings';
    protected $fillable = ['module_id', 'sub_module_id', 'sub_module_1_id', 'sub_module_2_id', 'setting', 'added_by', 'status'];
}
