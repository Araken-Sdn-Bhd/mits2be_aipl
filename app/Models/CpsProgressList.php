<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsProgressList extends Model
{
    use HasFactory;

    protected $table = 'cps_progress_list';
    protected $fillable =  ['id', 'name',
      'type'];
}
