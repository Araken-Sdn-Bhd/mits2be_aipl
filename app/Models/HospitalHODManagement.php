<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralSetting;
use App\Models\Citizenship;
use App\Models\Designation;

class HospitalHODManagement extends Model
{
    use HasFactory;
    protected $table = 'hospital_hod_psychiatrist_details';
    protected $fillable = ['added_by', 'salutation', 'name', 'gender', 'citizenship', 'passport_nric_no', 'religion', 'designation', 'email', 'contact_mobile', 'contact_office', 'status'];

    public function salutations()
    {
        return $this->hasOne(GeneralSetting::class, 'id', 'salutation');
    }

    public function citizenships()
    {
        return $this->hasOne(Citizenship::class, 'id', 'citizenship');
    }

    public function designations()
    {
        return $this->hasOne(Designation::class, 'id', 'designation');
    }

    public function religions()
    {
        return $this->hasOne(GeneralSetting::class, 'id', 'religion');
    }

    public function genders()
    {
        return $this->hasOne(GeneralSetting::class, 'id', 'gender');
    }
}
