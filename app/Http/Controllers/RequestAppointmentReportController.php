<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\PatientRegistration;
use App\Models\Postcode;
use App\Models\State;
use App\Models\PatientShharpRegistrationRiskProtective;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RequestAppointmentReportExport;
use App\Models\PatientAppointmentDetails;
use App\Models\AppointmentRequest; 
use App\Models\StaffManagement; 


class RequestAppointmentReportController extends Controller
{
    //        $list = StaffManagement::select('id', 'team_id', 'branch_id')->where('email','=', $request->email)->get();
    // $list2 = StaffManagement::select('id', 'team_id', 'name')->where('branch_id','=', $list[0]['branch_id'])->where('team_id','=', $request->appointment_type)->get();
    // return response()->json(["message" => "Staff Name", 'list' => $list2, "code" => 200]);

    public function getRequestAppointmentReport(Request $request)
    {
        
        $list = StaffManagement::select('id', 'team_id', 'branch_id')->where('email','=', $request->email)->get();
        $response = AppointmentRequest::select('name','nric_or_passportno','address','contact_no','email', 'created_at')
        ->whereBetween('created_at', [$request->fromDate, $request->toDate])
        ->where('branch_id','=', $list[0]['branch_id'])
        ->where('status', '1')->get()->toArray();
        
        $patient = [];
        $result = [];
    
        if ($response) {


            if ($response) {

                  

                $totalReports = count($result);
                 
                $filePath = 'downloads/report/report-' . time() . '.xlsx';
                Excel::store(new RequestAppointmentReportExport($result, $totalReports, $request->fromDate, $request->toDate), $filePath, 'public');
               

                return response()->json(["message" => "Request Report",'result' => $response, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "Request Report",'result' => [], 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Request Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    
}
