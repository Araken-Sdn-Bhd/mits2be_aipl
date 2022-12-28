<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KPIReportExport implements FromView
{
    private $mainResult;
    private $averageResult;
    public function __construct($mainResult,$averageResult,$year)
    {
        $this->reportSet = $mainResult;
        $this->averageSet = $averageResult;
        $this->yearSet = $year;

    }

    public function view(): View
    {
        return view('kpi_report',  [
            'kpirecord' => $this->reportSet,
            'kpiaverage' => $this->averageSet,
            'kpiyear' => $this->yearSet,

        ]);
    }
}
