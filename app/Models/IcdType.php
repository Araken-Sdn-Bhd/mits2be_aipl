<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IcdType extends Model
{
    use HasFactory;
    protected $table = 'icd_type';
    protected $fillable = ['id','added_by','icd_type_code','icd_type_name','icd_type_description','icd_type_order'];
}
