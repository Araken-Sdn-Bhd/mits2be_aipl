<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Postcode;

class EmployeeRegistration extends Model
{
    use HasFactory;
    protected $table = 'employee_registration';
    protected $fillable = ['company_name','company_registration_number','company_address_1','company_address_2','company_address_3,
    state_id','city_id','postcode','employment_sector','is_existing_training_program','corporate_body_sector','contact_name',
    'contact_email','contact_position','status','updated_at','contact_number','user_id'];


    public function city()
    {
        return $this->belongsto(Postcode::class);
    }
}
