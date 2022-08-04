<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShharpReportGenerateHistory extends Model
{
    use HasFactory;
    protected $table = 'shharp_report_generate_history';
    protected $fillable = ['generated_by', 'report_month', 'report_year', 'file_path', 'report_type', 'status', 'created_at'];
}
