<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GeneralReportExport implements FromView
{
    private $reportSet;
    private $totalRecord;
    private $fromDate;
    private $toDate;

    public function __construct($record, $totalRecords, $fromDate, $toDate)
    {
        $this->reportSet = $record;
        $this->totalRecord = $totalRecords;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function view(): View
    {
        return view('general_report',  [
            'shharpRecords' => $this->reportSet,
            'totalRecord' => $this->totalRecord,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate
        ]);
    }
}
