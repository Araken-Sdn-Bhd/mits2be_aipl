<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\PatientRegistration;
use App\Models\Postcode;
use App\Models\State;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivityReportExport;

use App\Models\PatientAppointmentDetails;
use App\Models\GeneralSetting;
use App\Models\StaffManagement; 
use App\Models\CpsProgressNote;
use App\Models\PsychiatricProgressNote;
use App\Models\JobDescription;

class ActivityReportController extends Controller
{
    public function getActivityReport(Request $request)
    {
        $response = PatientAppointmentDetails::select('*')
        ->whereBetween('booking_date', [$request->fromDate, $request->toDate])
        ->where('appointment_status', '1')->get()->toArray();
        $patient = [];
        $result = [];
        if ($response) {
            foreach ($response as $key => $val) {
                if ($val['booking_date']) {
                    $patient[] = $val['patient_mrn_id'];
                }
                
            }

            $patientArray = array_unique($patient);

            $demo = [];
            if ($request->name) {
                $demo['name_asin_nric'] = $request->name;
            }
            if ($request->citizenship) {
                $demo['citizenship'] = $request->citizenship;
            }
            if ($request->appointment_status) {
                $demo['APPOINTMENT_STATUS'] = $request->appointment_status;
            }
            if ($request->procedure) {
                $demo['PROCEDURE'] = $request->procedure;
            }
            if ($request->type_of_visit) {
                $demo['type_of_visit'] = $request->type_of_visit;
            }
            if ($request->name) {
                $demo['NAME'] = $request->name;
            }
            if ($request->cps_time) {
                $demo['CPS_TIME'] = $request->cps_time;
            }
            if ($request->diagnosis) {
                $demo['Diagnosis'] = $request->diagnosis;
            }
            if ($request->gender) {
                $demo['sex'] = $request->gender;
            }
            
            $patientDetails =  PatientRegistration::whereIn('id', $patientArray)->where($demo)->get()->toArray();

            if ($patientDetails) {
                $patientInfo = [];
                foreach ($patientDetails as $key => $val) {
                    $patientInfo[$val['id']]['Name'] = $val['name_asin_nric'];
                    $gn = GeneralSetting::where(['id' => $val['sex']])->get()->toArray();
                    $patientInfo[$val['id']]['Gender'] = ($gn) ? $gn[0]['section_value'] : 'NA';
                    $patientInfo[$val['id']]['Age'] = $val['age'];
                    $patientInfo[$val['id']]['NRIC_NO_PASSPORT_NO'] = ($val['nric_no']) ? $val['nric_no'] : $val['passport_no'];
                    $patientInfo[$val['id']]['ADDRESS'] = $val['address1'] . ' ' . $val['address2'] . ' ' . $val['address3'];
                    $patientInfo[$val['id']]['PHONE_NUMBER'] = $val['mobile_no'];
                    $patientInfo[$val['id']]['DATE_OF_BIRTH'] = date('d/m/Y', strtotime($val['birth_date']));
                    $patientInfo[$val['id']]['REFERRAL_TYPE'] = $val['referral_type'];
                    $pc = Postcode::where(['postcode' => $val['postcode']])->get()->toArray();
                    $st = State::where(['id' => $val['state_id']])->get()->toArray();
                    $patientInfo[$val['id']]['CITY'] = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $patientInfo[$val['id']]['STATE'] = ($st) ? $st[0]['state_name'] : 'NA';
                    $patientInfo[$val['id']]['POSTCODE'] = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $as = PatientAppointmentDetails::where(['patient_mrn_id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['ATTENDANCE_STATUS'] = ($as) ? $as[0]['appointment_status'] : 'NA';
                    $sn = CpsProgressNote::where(['id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['ATTENDING_STAFF'] = ($sn) ? $sn[0]['staff_name'] : 'NA';
                    $ts = CpsProgressNote::where(['patient_mrn_id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['TIME_SEEN'] = ($ts) ? $ts[0]['cps_time'] : 'NA';
                    $ts = PsychiatricProgressNote::where(['patient_mrn_id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['Diagnosis'] = ($ts) ? $ts[0]['diagnosis'] : 'NA';
                    $pro = JobDescription::where(['id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['PROCEDURE'] = ($pro) ? $pro[0]['procedure'] : 'NA';
    
                }

                $index = 0;
                foreach ($response as $k => $v) {
                    $result[$index]['DATE'] = date('d/m/y', strtotime($v['booking_date']));
                    $result[$index]['Time'] = date('h:i A', strtotime($v['booking_time']));
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $patientInfo[$v['patient_mrn_id']]['NRIC_NO_PASSPORT_NO'];
                    $result[$index]['Name'] = $patientInfo[$v['patient_mrn_id']]['Name'];
                    $result[$index]['Gender'] = $patientInfo[$v['patient_mrn_id']]['Gender'];
                    $result[$index]['Age'] = $patientInfo[$v['patient_mrn_id']]['Age'];
                    $result[$index]['ADDRESS'] = $patientInfo[$v['patient_mrn_id']]['ADDRESS'];
                    $result[$index]['CITY'] = $patientInfo[$v['patient_mrn_id']]['CITY'];
                    $result[$index]['STATE'] = $patientInfo[$v['patient_mrn_id']]['STATE'];
                    $result[$index]['POSTCODE'] = $patientInfo[$v['patient_mrn_id']]['POSTCODE'];
                    $result[$index]['PHONE_NUMBER'] = $patientInfo[$v['patient_mrn_id']]['PHONE_NUMBER'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo[$v['patient_mrn_id']]['DATE_OF_BIRTH'];
                    $result[$index]['REFERRAL_TYPE'] = $patientInfo[$v['patient_mrn_id']]['REFERRAL_TYPE'];
                    $result[$index]['NEXT_VISIT'] = date('d/m/y', strtotime($v['booking_date']));
                    $result[$index]['TIME_REGISTERED'] = date('h:i A', strtotime($v['booking_time']));
                    $result[$index]['APPOINTMENT_TYPE'] = 'Clinic';
                    $result[$index]['ATTENDANCE_STATUS'] = $patientInfo[$v['patient_mrn_id']]['ATTENDANCE_STATUS'];
                    $result[$index]['ATTENDING_STAFF'] = $patientInfo[$v['patient_mrn_id']]['ATTENDING_STAFF'];
                    $result[$index]['TIME_SEEN'] = $patientInfo[$v['patient_mrn_id']]['TIME_SEEN'];
                    $result[$index]['Diagnosis'] = $patientInfo[$v['patient_mrn_id']]['Diagnosis'];
                    $result[$index]['PROCEDURE'] = $patientInfo[$v['patient_mrn_id']]['PROCEDURE'];
                    $result[$index]['type_of_visit'] = $patientInfo[$v['patient_mrn_id']]['type_of_visit'];
                    $index++;
                }
            }

            if ($result) {
                $totalReports = count($result);
                $filePath = 'downloads/report/report-' . time() . '.xlsx';
                Excel::store(new ActivityReportExport($result, $totalReports, $request->fromDate, $request->toDate), $filePath, 'public');
               

                return response()->json(["message" => "Activity Report", 'result' => $result, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "Activity Report", 'result' => [], 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Activity Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }
}
