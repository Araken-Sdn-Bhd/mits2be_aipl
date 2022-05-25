<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PatientAppointmentDetails;

class HospitalBranchTeamManagement extends Model
{
    use HasFactory;
    protected $table = 'hospital_branch_team_details';
    protected $fillable = [
        'added_by',
        'hospital_id',
        'hospital_code',
        'hospital_branch_name',
        'hospital_branch_id',
        'team_name',
        'status'
    ];

    public function appointment()
    {
        return $this->belongsTo(PatientAppointmentDetails::class, 'id', 'assign_team');
    }
}
