<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EtpDivision;

class EtpRegister extends Model
{
    use HasFactory;
    protected $table = 'etp_register';
    protected $fillable = ['added_by', 'etp_code', 'etp_name', 'etp_description', 'etp_order','status'];

    public function divisions()
    {
        return $this->hasMany(EtpDivision::class, 'etp_id', 'id');
    }
}
