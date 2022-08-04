<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobStartFormList extends Model
{
    use HasFactory;
    protected $table = 'job_start_form_list';
    protected $fillable = [
        'job_title',
        'created_at'
    ];
}
