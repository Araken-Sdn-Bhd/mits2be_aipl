<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;
    protected $table = 'announcement_mgmt';
    protected $fillable = [
        'added_by',
        'title',
        'content',
        'document',
        'start_date',
        'end_date',
        'branch_id',
        'audience_ids',
        'status',
        'created_at'
    ];
}
