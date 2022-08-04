<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VONActivityReportExport implements FromView
{
    private $reportSet;
    private $totalPatients;
    private $totalDays;
    private $fromDate;
    private $toDate;
    private $toiArr;

    public function __construct($record, $totalRecords, $totalDays, $fromDate, $toDate, $toiArr)
    {
        $this->reportSet = $record;
        $this->totalPatients = $totalRecords;
        $this->totalDays = $totalDays;
        $this->toDate = $toDate;
        $this->fromDate = $fromDate;
        $this->toiArr = $toiArr;
    }

    public function view(): View
    {
        return view('von_activity_report',  [
            'shharpRecords' => $this->reportSet,
            'totalPatients' => $this->totalPatients,
            'totalDays' => $this->totalDays,
            'toDate' => $this->toDate,
            'fromDate' => $this->fromDate,
            'toiArr' => $this->toiArr
        ]);
    }
}
