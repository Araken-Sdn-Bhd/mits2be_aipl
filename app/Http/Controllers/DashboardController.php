<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientRegistration;
use App\Models\ShharpReportGenerateHistory;
use App\Models\StaffManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function getsystemadmin(Request $request)
    {   
        $users = DB::table('patient_appointment_details')
        ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
        ->select(DB::raw('count(appointment_status) as TeamTask'))
        ->where('patient_appointment_details.appointment_status', '=', '0')
        ->groupBy('patient_appointment_details.appointment_status')
        ->get();

        $list2 =StaffManagement::select(DB::raw('count(*) as TotalMITS2User'))
        ->get();

       $list =StaffManagement::select(DB::raw('count(*) as TotalMentari'))
        ->get();

        $users2 = DB::table('state')->where('state_name','=', $request->state_name)
        ->join('hospital_management', 'hospital_management.id', '=', 'state.country_id')
        ->select('state_name', DB::raw('count(state_name) as TotalState'))
        ->where('state.state_status', '=', '1')
        ->groupBy('state.state_name')
        ->get();

          $task=[];
        foreach ($users as $key => $value) {
            $task[]=$value;
        }
        foreach ($list as $key => $value) {
            $task[]=$value;

        }

        foreach ($list2 as $key => $value) {
            $task[]=$value;
        }

        foreach ($users2 as $key => $value) {
            $task[]=$value;
        }

        return response()->json(["message" => "System Admin", 'list' => $task, "code" => 200]);
    }


    public function getallmentaristaff(Request $request)
    {
       
        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
                       ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%".$search."%")
                       ->where('patient_mrn','=', $request->patient_mrn)
                       ->orwhere('name_asin_nric','=', $request->name_asin_nric)
                       ->where('passport_no','=', $request->passport_no)
                       ->orwhere('nric_no','=', $request->nric_no)
                       ->get();

                       $list =PatientAppointmentDetails::select(DB::raw('count(*) as todays_appointment'))
                       ->whereDate('created_at', today())
                       ->groupBy('booking_date')
                       ->get();

        $users = DB::table('patient_appointment_details')
                       ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
                       ->select(DB::raw('count(appointment_status) as TeamTask'))
                       ->where('patient_appointment_details.appointment_status', '=', '0')
                       ->groupBy('patient_appointment_details.appointment_status')
                       ->get();

                       $AMS=[];
                       foreach ($result as $key => $value) {
                           $AMS[]=$value;
                       }
                       foreach ($list as $key => $value) {
                           $AMS[]=$value;
               
                       }
               
                       foreach ($users as $key => $value) {
                           $AMS[]=$value;
                       }
    
       return response()->json(["message" => "All Mentari Staff", 'list' => $AMS, "code" => 200]);
       
    }

    public function getuseradminclerk(Request $request)
    {
       
        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
                       ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%".$search."%")
                       ->where('patient_mrn','=', $request->patient_mrn)
                       ->orwhere('name_asin_nric','=', $request->name_asin_nric)
                       ->where('passport_no','=', $request->passport_no)
                       ->orwhere('nric_no','=', $request->nric_no)
                       ->get();

        $list =PatientAppointmentDetails::select(DB::raw('count(*) as todays_appointment'))
                       ->whereDate('created_at', today())
                       ->groupBy('booking_date')
                       ->get();

        $users = DB::table('patient_appointment_details')
                       ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
                       ->select(DB::raw('count(appointment_status) as TeamTask'))
                       ->where('patient_appointment_details.appointment_status', '=', '0')
                       ->groupBy('patient_appointment_details.appointment_status')
                       ->get();

        $list2 =AppointmentRequest::select(DB::raw('count(*) as RequestAppointment'))
                       ->get();

        $list3 =PatientAppointmentDetails::select(DB::raw('count(*) as TOTAL_CASE'))
                       ->get();
    
                       $UAC=[];
                       foreach ($result as $key => $value) {
                           $UAC[]=$value;
                       }
                       foreach ($list as $key => $value) {
                           $UAC[]=$value;
               
                       }
               
                       foreach ($users as $key => $value) {
                           $UAC[]=$value;
                       }

                       foreach ($list2 as $key => $value) {
                        $UAC[]=$value;
            
                    }

                    foreach ($list3 as $key => $value) {
                        $UAC[]=$value;
            
                    }
    
       return response()->json(["message" => "All Mentari Staff", 'list' => $UAC, "code" => 200]);
    }


    public function getshharp(Request $request)
    {
       
        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
                       ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%".$search."%")
                       ->where('patient_mrn','=', $request->patient_mrn)
                       ->orwhere('name_asin_nric','=', $request->name_asin_nric)
                       ->where('passport_no','=', $request->passport_no)
                       ->orwhere('nric_no','=', $request->nric_no)
                       ->get();

        $list =PatientAppointmentDetails::select(DB::raw('count(*) as TODAY_CASE'))
                       ->whereDate('created_at', today())
                       ->groupBy('booking_date')
                       ->get();

        $list2 =PatientAppointmentDetails::select(DB::raw('count(*) as TOTAL_CASE'))
                       ->get();

        $shharpcase =ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year')
                       ->where('report_month','=', $request->report_month)
                       ->where('report_year','=', $request->report_year)
                       ->groupBy('report_month', 'report_year')
                       ->get()->toArray();
    
                       $Shharp=[];
                       foreach ($result as $key => $value) {
                           $Shharp[]=$value;
                       }
                       
                       foreach ($list as $key => $value) {
                           $Shharp[]=$value;
               
                       }
               
                       foreach ($list2 as $key => $value) {
                           $Shharp[]=$value;
                       }

                       foreach ($shharpcase as $key => $value) {
                        $Shharp[]=$value;
            
                    }
    
       return response()->json(["message" => "Shharp Record", 'list' => $Shharp, "code" => 200]);
    }

    public function gethighlevelMgt(Request $request)
    {
       
        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
                       ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%".$search."%")
                       ->where('patient_mrn','=', $request->patient_mrn)
                       ->orwhere('name_asin_nric','=', $request->name_asin_nric)
                       ->where('passport_no','=', $request->passport_no)
                       ->orwhere('nric_no','=', $request->nric_no)
                       ->get();

        $users =AppointmentRequest::select(DB::raw('count( * ) as TotalAppointmentRequest, YEAR(appointment_request.created_at) AS Year, MONTH(appointment_request.created_at) AS Month, hospital_name '),)
        ->join('hospital_management', 'hospital_management.hospital_status', '=', 'appointment_request.id')
        ->Where('appointment_request.created_at', 'like', '%'.$request->Year.'-'.$request->Month.'%')
        ->where('hospital_name','=', $request->hospital_name)
       ->groupBy('appointment_request.created_at', 'Month', 'hospital_name')
       ->get();

       $list =PatientAppointmentDetails::select(DB::raw('count(*) as total_appointments_request'))
       ->get();

       $list2 =StaffManagement::select(DB::raw('count(*) as TotalMentari'))
       ->get();

       $users2 = DB::table('state')->where('state_name','=', $request->state_name)
       ->join('hospital_management', 'hospital_management.id', '=', 'state.country_id')
       ->select('state_name', DB::raw('count(state_name) as TotalState'))
       ->where('state.state_status', '=', '1')
       ->groupBy('state.state_name')
       ->get();

       $shharpcase =ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year', 'state_name')
       ->join('state', 'state.id', '=', 'shharp_report_generate_history.id')
       ->where('report_month','=', $request->report_month)
       ->where('report_year','=', $request->report_year)
       ->where('state_name','=', $request->state_name)
       ->groupBy('report_month', 'report_year', 'state_name')
       ->get()->toArray();

       $clinicreport =ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year')
       ->where('report_month','=', $request->report_month)
       ->where('report_year','=', $request->report_year)
       ->groupBy('report_month', 'report_year')
       ->get();

       $kpi =ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year')
       ->where('report_month','=', $request->report_month)
       ->where('report_year','=', $request->report_year)
       ->groupBy('report_month', 'report_year')
       ->get();
    
                       $HLM=[];
                       foreach ($result as $key => $value) {
                           $HLM[]=$value;
                       }

                       foreach ($users as $key => $value) {
                        $HLM[]=$value;
            
                    }
                       foreach ($list as $key => $value) {
                           $HLM[]=$value;
               
                       }

                       foreach ($users2 as $key => $value) {
                        $HLM[]=$value;
            
                    }
               
                       foreach ($list2 as $key => $value) {
                           $HLM[]=$value;
                       }

                       foreach ($shharpcase as $key => $value) {
                        $HLM[]=$value;
            
                    }

                    foreach ($clinicreport as $key => $value) {
                        $HLM[]=$value;
            
                    }

                    foreach ($kpi as $key => $value) {
                        $HLM[]=$value;
            
                    }
    
       return response()->json(["message" => "High Level Mgt", 'list' => $HLM, "code" => 200]);
    }

}

