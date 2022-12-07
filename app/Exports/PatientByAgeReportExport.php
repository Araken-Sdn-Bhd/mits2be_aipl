<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PatientByAgeReportExport implements FromView
{
    private $reportSet;
    private $fromDate;
    private $toDate;

    public function __construct($record,$totalReports)
    {
        $this->reportSet = $record;
         $this->totalReports = $totalReports;

    }

    public function view(): View
    {
        return view('patient_by_age',  [
            'shharpRecords' => $this->reportSet,
             'totalReports' => $this->totalReports,

        ]);
    }
}
