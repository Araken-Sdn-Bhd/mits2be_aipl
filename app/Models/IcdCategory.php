<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IcdCategory extends Model
{
    use HasFactory;
    protected $table = 'icd_category';
    protected $fillable = ['added_by','icd_type_id','icd_category_code','icd_category_name','icd_category_description','icd_category_order'];
}
