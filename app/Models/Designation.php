<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HospitalHODManagement;

class Designation extends Model
{
    use HasFactory;
    protected $table = 'designation';
    protected $fillable = ['designation_name','designation_order'];

    public function hods()
    {
        return $this->belongsTo(HospitalHODManagement::class, 'designation', 'id');
    }
}
