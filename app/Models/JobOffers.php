<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffers extends Model
{
    use HasFactory;
    protected $table = 'job_offers';
    protected $fillable = [
        'job_id','branch_id','location_address_1','location_address_2','location_address_3','state_id','city_id','postcode','salary_offered',
        'duration_of_employment','work_schedule','is_transport','is_accommodation','jov_availability','approval_status','approve_by','user_id',
        'is_repeated'
       ];

       public function city()
       {
           return $this->belongsto(Postcode::class);
       }

       public function job()
       {
           return $this->belongsto(Job::class);
       }
}
