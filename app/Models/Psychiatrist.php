<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psychiatrist extends Model
{
    use HasFactory;
    protected $table = 'psychiatrist';
    protected $fillable = ['id','added_by','name','created_at'];
}
