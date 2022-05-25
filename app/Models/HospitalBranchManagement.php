<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceDivision;

class HospitalBranchManagement extends Model
{
    use HasFactory;
    protected $table = 'hospital_branch__details';
    protected $fillable = [
        'added_by',
        'hospital_id',
        'hospital_code',
        'hospital_branch_name',
        'isHeadquator',
        'branch_adrress_1',
        'branch_adrress_2',
        'branch_adrress_3',
        'branch_state',
        'branch_city',
        'branch_postcode',
        'branch_contact_number_office',
        'branch_contact_number_mobile',
        'branch_email',
        'branch_fax_no',
        'branch_status'
    ];

    public function divisions()
    {
        return $this->belongsTo(ServiceDivision::class, 'branch_id', 'id');
    }
}
