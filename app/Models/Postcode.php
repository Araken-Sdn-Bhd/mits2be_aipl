<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    use HasFactory;
    protected $table = 'postcode';
    protected $fillable = ['country_id', 'state_id', 'country_name', 'state_name', 'city_name', 'postcode', 'postcode_order', 'postcode_status', 'added_by'];
}
