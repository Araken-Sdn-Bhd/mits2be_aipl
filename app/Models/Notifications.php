<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'added_by',
        'patient_id',
        'team_id',
        'message',
        'isseen_staff',
        'isseen_admin',
        'created_at'
    ];
}
