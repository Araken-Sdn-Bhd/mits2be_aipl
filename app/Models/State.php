<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HospitalManagement;

class State extends Model
{
    use HasFactory;
    protected $table = 'state';
    protected $fillable = ['country_id', 'state_name', 'state_order', 'state_status', 'added_by'];

    public function hospitals()
    {
        return $this->belongsTo(HospitalManagement::class,  'id', 'hospital_state');
    }
}
