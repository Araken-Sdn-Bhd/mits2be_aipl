<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkingContribution extends Model
{
    use HasFactory;
    protected $table = 'networking_contribution';
    protected $fillable = [
        'added_by',
        'parent_section_id',
        'parent_section',
        'contribution',
        'budget',
        'project_loaction',
        'project_loaction_value',
        'no_of_paricipants',
        'mentari_services',
        'created_at'
    ];
}
