<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ShharpReportExport implements FromView
{
    private $reportSet;
    private $totalRecord;

    public function __construct($record, $totalRecords)
    {
        $this->reportSet = $record;
        $this->totalRecord = $totalRecords;
    }

    public function view(): View
    {
        return view('shharp_report',  [
            'shharpRecords' => $this->reportSet,
            'totalRecord' => $this->totalRecord
        ]);
    }
}
