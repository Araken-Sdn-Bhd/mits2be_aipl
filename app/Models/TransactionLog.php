<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;
    protected $table = 'transaction_log';
    protected $fillable = ['added_by', 'patient_id', 'date', 'time', 'activity'];

}
