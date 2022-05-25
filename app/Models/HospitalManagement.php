<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceDivision;
use App\Models\State;
use App\Models\Postcode;

class HospitalManagement extends Model
{
    use HasFactory;
    protected $table = 'hospital_management';
    protected $fillable = [
        'added_by',
        'hod_psychiatrist_name',
        'hod_psychiatrist_id',
        'hospital_code',
        'hospital_prefix',
        'hospital_name',
        'hospital_adrress_1',
        'hospital_adrress_2',
        'hospital_adrress_3',
        'hospital_state',
        'hospital_city',
        'hospital_postcode',
        'hospital_contact_number',
        'hospital_email',
        'hospital_fax_no',
        'hospital_status'
    ];

    public function divisions()
    {
        return $this->belongsTo(ServiceDivision::class, 'hospital_id', 'id');
    }

    public function states()
    {
        return $this->hasOne(State::class, 'id', 'hospital_state');
    }

    public function cities()
    {
        return $this->hasOne(Postcode::class, 'id', 'hospital_city');
    }
}
