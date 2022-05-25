<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentari_Staff_Transfer extends Model
{
    use HasFactory;
    protected $table = 'mentari_staff_transfer';
    protected $fillable = ['added_by', 'old_branch_id', 'new_branch_id','staff_id', 'start_date', 'end_date', 'document'];
}
