<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VonAppointment extends Model
{
    use HasFactory;
    protected $table = 'von_appointment';
    protected $fillable = ['id', 'added_by', 'parent_section_id', 'name', 'booking_date', 'booking_time', 'duration', 'interviewer_id', 'area_of_involvement', 'services_type', 'status', 'created_at'];


    public function team()
    {
        return $this->hasOne(HospitalBranchTeamManagement::class, "assign_team", "id");
    }
}
