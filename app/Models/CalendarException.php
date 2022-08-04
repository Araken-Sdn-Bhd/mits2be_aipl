<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarException extends Model
{
    use HasFactory;
    protected $table = 'calendar_exception';
    protected $fillable = ['added_by', 'name', 'start_date', 'end_date', 'description', 'state','branch_id'];
}
