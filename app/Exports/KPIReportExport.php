<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KPIReportExport implements FromView
{
    private $reportSet;
    private $yearArray;
    private $months;

    public function __construct($record, $yearArray,  $months)
    {
        $this->reportSet = $record;
        $this->yearArray = $yearArray;
        $this->months =  $months;
    }

    public function view(): View
    {
        return view('kpi_report',  [
            'shharpRecords' => $this->reportSet,
            'yearArray' => $this->yearArray,
            'months' =>  $this->months
        ]);
    }
}
