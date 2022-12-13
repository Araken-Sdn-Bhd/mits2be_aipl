<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HospitalManagement;
use App\Models\HospitalBranchManagement;
use App\Models\ServiceRegister;

class ServiceDivision extends Model
{
    use HasFactory;
    protected $table = 'service_division';
    protected $fillable = [
        'added_by',
        'service_id',
        'hospital_id',
        'branch_id',
        'division_order',
        'status'
    ];

    public function hospitals()
    {
        return $this->hasOne(HospitalManagement::class, 'id', 'hospital_id');
    }
    public function branchs()
    {
        return $this->hasOne(HospitalBranchManagement::class, 'id', 'branch_id');
    }
    public function services()
    {
        return $this->belongsTo(ServiceRegister::class, 'service_id', 'id');
    }
}
