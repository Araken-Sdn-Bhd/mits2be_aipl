<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WorkloadTotalPatienTypeRefferalReportExport implements FromView
{
    private $reportSet;
    private $totalPatients;
    private $totalDays;
    private $patientCategories;
    private $visitTypes;
    private $refferals;

    public function __construct($record, $totalRecords, $totalDays, $patientCategories, $visitTypes, $refferals)
    {
        $this->reportSet = $record;
        $this->totalPatients = $totalRecords;
        $this->totalDays = $totalDays;
        $this->patientCategories = $patientCategories;
        $this->visitTypes = $visitTypes;
        $this->refferals = $refferals;
    }

    public function view(): View
    {
        return view('total_patient_type_refferal_report',  [
            'shharpRecords' => $this->reportSet,
            'totalPatients' => $this->totalPatients,
            'totalDays' => $this->totalDays,
            'patientCategories' => $this->patientCategories,
            'visitTypes' => $this->visitTypes,
            'refferals' => $this->refferals
        ]);
    }
}
