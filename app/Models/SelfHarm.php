<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfHarm extends Model
{
    use HasFactory;
    protected $table = 'self_harm';
    protected $fillable = ['id', 'added_by','name','status'];
}
