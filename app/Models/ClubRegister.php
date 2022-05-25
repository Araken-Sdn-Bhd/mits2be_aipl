<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClubDivision;

class ClubRegister extends Model
{
    use HasFactory;
    protected $table = 'club_register';
    protected $fillable = ['added_by', 'club_code', 'club_name', 'club_description', 'club_order'];

    public function divisions()
    {
        return $this->hasMany(ClubDivision::class, 'club_id', 'id');
    }
}
