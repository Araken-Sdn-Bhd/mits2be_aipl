<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PatientActivityReportExport implements FromView
{
    private $reportSet;
    private $totalPatients;
    private $totalDays;
    private $fromDate;
    private $toDate;

    public function __construct($record, $totalRecords, $totalDays, $fromDate, $toDate)
    {
        $this->reportSet = $record;
        $this->totalPatients = $totalRecords;
        $this->totalDays = $totalDays;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        return view('patient_activity_report',  [
            'shharpRecords' => $this->reportSet,
            'totalPatients' => $this->totalPatients,
            'totalDays' => $this->totalDays,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate
        ]);
    }
}
