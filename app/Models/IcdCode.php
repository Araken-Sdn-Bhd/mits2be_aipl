<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IcdCode extends Model
{
    use HasFactory;
    protected $table = 'icd_code';
    protected $fillable = ['added_by','icd_type_id','icd_category_id','icd_code','icd_name','icd_description','icd_order'];
}
