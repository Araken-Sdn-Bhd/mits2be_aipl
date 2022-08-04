<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    use HasFactory;
    protected $table = 'user_block';
    protected $fillable = ['id', 'user_id', 'no_of_attempts','created_at','block_untill'];
}
