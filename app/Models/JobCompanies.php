<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCompanies extends Model
{
    use HasFactory;
    protected $table = 'job_companies';
    protected $fillable = [
        'added_by',
        'company_name',
        'company_registration_number',
        'company_address_1',
        'company_address_2',
        'company_address_3',
        'state_id',
        'city_id',
        'postcode',
        'employment_sector',
        'is_existing_training_program',
        'corporate_body_sector',
        'contact_name',
        'contact_number',
        'contact_email',
        'contact_position',
        'status',
        'created_at'
    ];

    public function city()
    {
        return $this->belongsTo(Postcode::class, "city_id", "id");
    }
}
