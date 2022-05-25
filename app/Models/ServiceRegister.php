<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceDivision;

class ServiceRegister extends Model
{
    use HasFactory;
    protected $table = 'service_register';
    protected $fillable = ['added_by', 'service_code', 'service_name', 'service_description', 'service_order'];

    public function divisions()
    {
        return $this->hasMany(ServiceDivision::class, 'service_id', 'id');
    }
}
