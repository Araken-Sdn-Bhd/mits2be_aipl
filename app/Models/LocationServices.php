<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationServices extends Model
{
    use HasFactory;
    protected $table = 'location_services';
    protected $fillable = ['id', 'added_by','name','status'];
}
