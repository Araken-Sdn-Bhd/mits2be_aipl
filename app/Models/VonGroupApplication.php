<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VonGroupApplication extends Model
{
    use HasFactory;
    protected $table = 'von_group_application';
    protected $fillable = [
        'added_by',
        'is_represent_org',
        'members_count',
        'member_background',
        'is_you_represenative',
        'is_agree',
        'created_at'
    ];
}
