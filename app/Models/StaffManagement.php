<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffManagement extends Model
{
    use HasFactory;
    protected $table = 'staff_management';
    protected $fillable = ['added_by', 'name', 'nric_no', 'registration_no', 'role_id', 'email', 'team_id', 'branch_id', 'contact_no', 'designation_id', 'is_incharge', 'designation_period_start_date', 'designation_period_end_date', 'start_date', 'end_date', 'document', 'mentari_location'];
}
