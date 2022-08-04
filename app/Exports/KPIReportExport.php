<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KPIReportExport implements FromView
{
    private $reportSet;
    private $yearArray;

    public function __construct($record, $yearArray)
    {
        $this->reportSet = $record;
        $this->yearArray = $yearArray;
    }

    public function view(): View
    {
        return view('kpi_report',  [
            'shharpRecords' => $this->reportSet,
            'yearArray' => $this->yearArray
        ]);
    }
}
