<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HospitalManagement;
use App\Models\HospitalBranchManagement;
use App\Models\EtpRegister;

class EtpDivision extends Model
{
    use HasFactory;
    protected $table = 'etp_division';
    protected $fillable = [
        'added_by',
        'etp_id',
        'hospital_id',
        'branch_id',
        'division_order'
    ];

    public function hospitals()
    {
        return $this->hasOne(HospitalManagement::class, 'id', 'hospital_id');
    }
    public function branchs()
    {
        return $this->hasOne(HospitalBranchManagement::class, 'id', 'branch_id');
    }
    public function etp()
    {
        return $this->belongsTo(EtpRegister::class, 'etp_id', 'id');
    }
}
