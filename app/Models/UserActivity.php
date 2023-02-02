<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;
    protected $table = 'user_activity';
    protected $fillable = ['id','user_email','user_name', 'branch_name', 'login_count', 'first_login', 'last_login'];
}
