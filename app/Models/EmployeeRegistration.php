<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRegistration extends Model
{
    use HasFactory;
    protected $table = 'employee_registration';
    protected $fillable = ['company_name','email','contact_number','user_id','password'];

}
