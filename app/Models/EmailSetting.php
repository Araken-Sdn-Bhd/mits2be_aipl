<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;
    protected $table = 'email_setting';
    protected $fillable = ['send_email_from','outgoing_smtp_server','login_user_id','login_password',
    'verify_password','smtp_port_number','security'];

}
