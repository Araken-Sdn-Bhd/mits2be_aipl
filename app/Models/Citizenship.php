<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizenship extends Model
{
    use HasFactory;
    protected $table = 'citizenship';
    protected $fillable = ['citizenship_name','citizenship_order'];
}
