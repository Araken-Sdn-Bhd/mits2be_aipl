<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCauseInjury extends Model
{
    use HasFactory;
    protected $table = 'external_cause_injury';
    protected $fillable = ['id','added_by','name','created_at'];
}
