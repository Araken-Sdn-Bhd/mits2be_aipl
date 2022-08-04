<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RequestAppointmentReportExport implements FromView
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
        return view('request_appointment_report',  [
            'shharpRecords' => $this->reportSet,
            'totalRecord' => $this->totalRecord,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate
        ]);
    }
}
