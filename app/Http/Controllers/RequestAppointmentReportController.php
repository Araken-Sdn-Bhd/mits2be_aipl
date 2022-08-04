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


class RequestAppointmentReportController extends Controller
{
    //

    public function getRequestAppointmentReport(Request $request)
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

            $patientDetails =  PatientRegistration::whereIn('id', $patientArray)->get()->toArray();


            if ($patientDetails) {
                $patientInfo = [];
                foreach ($patientDetails as $key => $val) {
                    $patientInfo[$val['id']]['Name'] = $val['name_asin_nric'];
                    $patientInfo[$val['id']]['NRIC_NO_PASSPORT_NO'] = ($val['nric_no']) ? $val['nric_no'] : $val['passport_no'];
                    $patientInfo[$val['id']]['ADDRESS'] = $val['address1'] . ' ' . $val['address2'] . ' ' . $val['address3'];
                    $patientInfo[$val['id']]['PHONE_NUMBER'] = $val['mobile_no'];
                    $patientInfo[$val['id']]['DATE_OF_BIRTH'] = date('d/m/Y', strtotime($val['birth_date']));
                    $pc = Postcode::where(['postcode' => $val['postcode']])->get()->toArray();
                    $st = State::where(['id' => $val['state_id']])->get()->toArray();
                    $patientInfo[$val['id']]['CITY'] = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $patientInfo[$val['id']]['STATE'] = ($st) ? $st[0]['state_name'] : 'NA';
                    $patientInfo[$val['id']]['POSTCODE'] = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $patientInfo[$val['id']]['EMAIL'] = 'NA';
                }

                $index = 0;
                foreach ($response as $k => $v) {
        
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $patientInfo[$v['patient_mrn_id']]['NRIC_NO_PASSPORT_NO'] ?? 'NA';
                    $result[$index]['Name'] = $patientInfo[$v['patient_mrn_id']]['Name'] ?? 'NA';
                    $result[$index]['ADDRESS'] = $patientInfo[$v['patient_mrn_id']]['ADDRESS'] ?? 'NA';
                    $result[$index]['CITY'] = $patientInfo[$v['patient_mrn_id']]['CITY'] ?? 'NA';
                    $result[$index]['STATE'] = $patientInfo[$v['patient_mrn_id']]['STATE'] ?? 'NA';
                    $result[$index]['POSTCODE'] = $patientInfo[$v['patient_mrn_id']]['POSTCODE'] ?? 'NA';
                    $result[$index]['PHONE_NUMBER'] = $patientInfo[$v['patient_mrn_id']]['PHONE_NUMBER'] ?? 'NA';
                    $result[$index]['EMAIL'] = $patientInfo[$v['patient_mrn_id']]['EMAIL'] ?? 'NA';

                    $index++;
                }
            }

            if ($result) {
                $totalReports = count($result);
                 
                $filePath = 'downloads/report/report-' . time() . '.xlsx';
                Excel::store(new RequestAppointmentReportExport($result, $totalReports, $request->fromDate, $request->toDate), $filePath, 'public');
               

                return response()->json(["message" => "Request Report", 'result' => $result, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "Request Report", 'result' => [], 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Request Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    
}
