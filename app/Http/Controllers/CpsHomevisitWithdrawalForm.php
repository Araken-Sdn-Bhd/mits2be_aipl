<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpsHomevisitWithdrawalForm extends Model
{
    use HasFactory;

    protected $table = 'cps_homevisit_withdrawal_form';
    protected $fillable = [
        'added_by',
        'patient_id',
        'community_psychiatry_services',
        'created_at',
    ];
}
