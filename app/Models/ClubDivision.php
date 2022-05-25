<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HospitalManagement;
use App\Models\HospitalBranchManagement;
use App\Models\ClubRegister;

class ClubDivision extends Model
{
    use HasFactory;
    protected $table = 'club_division';
    protected $fillable = [
        'added_by',
        'club_id',
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
    public function club()
    {
        return $this->belongsTo(ClubRegister::class, 'club_id', 'id');
    }
}
