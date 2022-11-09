<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $table = 'jobs';
    protected $fillable = ['company_id',
        'position','education_id','work_requirement','updated_at'
    ];

    public function jobOffers(){
        return $this->hasMany(JobOffers::class);
    }
}
