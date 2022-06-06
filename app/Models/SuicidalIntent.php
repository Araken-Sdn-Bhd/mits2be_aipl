<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuicidalIntent extends Model
{
    use HasFactory;
    protected $table = 'suicidal_intent';
    protected $fillable = ['id', 'added_by','name','status'];
}
