<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreasOfInvolvement extends Model
{
    use HasFactory;
    protected $table = 'areas_of_involvement';
    protected $fillable = ['id', 'added_by','name','status'];
}
