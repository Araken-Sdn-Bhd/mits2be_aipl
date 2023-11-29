<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentRequest extends Model
{
    use HasFactory;
    protected $table = 'appointment_request';
    protected $fillable = ['id', 'added_by','branch_id','name','nric_or_passportno', 'contact_no', 'address', 'address1', 'email','ip_address','status','created_at','remark'];

}
