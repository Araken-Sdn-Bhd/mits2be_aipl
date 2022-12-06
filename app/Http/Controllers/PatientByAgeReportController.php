<?php

namespace App\Http\Controllers;

use App\Exports\PatientByAgeReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientRegistration;
use App\Models\Postcode;
use App\Models\State;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PatientAppointmentDetails;
use App\Models\GeneralSetting;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;
use Illuminate\Support\Facades\Storage;

class PatientByAgeReportController extends Controller
{

    public function getPatientByAgeReport(Request $request)
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
            if ($request->age) {
                $demo['age'] = $request->age;
            }
            if ($request->gender) {
                $demo['sex'] = $request->gender;
            }
            if ($request->race) {
                $demo['race_id'] = $request->race;
            }
            if ($request->religion) {
                $demo['religion_id'] = $request->religion;
            }
            if ($request->marital_status) {
                $demo['marital_id'] = $request->marital_status;
            }
            if ($request->accomodation) {
                $demo['accomodation_id'] = $request->accomodation;
            }
            if ($request->education_level) {
                $demo['education_level'] = $request->education_level;
            }
            if ($request->occupation_status) {
                $demo['occupation_status'] = $request->occupation_status;
            }
            if ($request->fee_exemption_status) {
                $demo['fee_exemption_status'] = $request->fee_exemption_status;
            }
            if ($request->occupation_sector) {
                $demo['occupation_sector'] = $request->occupation_sector;
            }

            $patientDetails =  PatientRegistration::whereIn('id', $patientArray)->where($demo)->get()->toArray();

             
            if ($patientDetails) {
                $patientInfo = [];
                foreach ($patientDetails as $key => $val) {
                
                    $gn = GeneralSetting::where(['id' => $val['sex']])->get()->toArray();
                    $patientInfo[$val['id']]['Gender'] = ($gn) ? $gn[0]['section_value'] : 'NA';
                    $patientInfo[$val['id']]['gender_id'] = ($gn) ? $gn[0]['id'] : 'NA';
                    $gn2 = GeneralSetting::where(['id' => $val['race_id']])->get()->toArray();
                    $patientInfo[$val['id']]['race'] = ($gn2) ? $gn2[0]['section_value'] : 'NA';
                    $patientInfo[$val['id']]['race_id'] = ($gn2) ? $gn2[0]['id'] : 'NA';
                   
                    $patientInfo[$val['id']]['Age'] = $val['age'];
                }

               
                $index = 0;
                foreach ($response as $k => $v) {
                    $result[$index]['DATE'] = date('d/m/y', strtotime($v['booking_date']));
                    $result[$index]['Gender'] = $patientInfo[$v['patient_mrn_id']]['Gender'] ?? 'NA';
                    $result[$index]['race'] = $patientInfo[$v['patient_mrn_id']]['race'] ?? 'NA';
                    $result[$index]['Age'] = $patientInfo[$v['patient_mrn_id']]['Age'] ?? 'NA';

                    $result[$index]['race_id'] =$patientInfo[$v['patient_mrn_id']]['race_id'] ?? 'NA';
                    $result[$index]['gender_id'] =$patientInfo[$v['patient_mrn_id']]['gender_id'] ?? 'NA';
                    $index++;
                }

                
            }

           

            if ($result) {
                $totalReports = count($result);
        $mainResult = [];
foreach($result as $key => $val){
    $raceName = GeneralSetting::where('id',$val['race_id'])->get()->toArray();
    $gender = GeneralSetting::where('id',$val['gender_id'])->get()->toArray();
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['male'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['female'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['10-19']['male'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['10-19']['female'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['male'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['female'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['male'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['female'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] = 0;
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['jumlah_besar'] = 0;
    if($val['Age']<10){
        if(strtolower($gender[0]['section_value']) == 'male'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] = ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male']+ $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['male']);
        }
        if(strtolower($gender[0]['section_value']) == 'female'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] = ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] + $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['female']);
        }
    }
    if($val['Age']>=10 && $val['Age']<=19){
        if(strtolower($gender[0]['group_name']['section_value']) == 'male'){
            $mainResult[0][$raceName[0]['group_name']['section_value']]['10-19']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male']+$mainResult[0]['group_name'][$raceName[0]['section_value']]['10-19']['male']);
        }
        if(strtolower($gender[0]['section_value']) == 'female'){
            $mainResult[0][$raceName[0]['group_name']['section_value']]['10-19']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female']+$mainResult[0]['group_name'][$raceName[0]['section_value']]['10-19']['female']);
        }
    }
    if($val['Age']>=20 && $val['Age']<=59){
        if(strtolower($gender[0]['section_value']) == 'male'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male']+$mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['male']);
        }
        if(strtolower($gender[0]['section_value']) == 'female'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female']+$mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['female']);
        }
    }
    if($val['Age']>=60){
        if(strtolower($gender[0]['section_value']) == 'male'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female']+$mainResult[0][$raceName[0]['group_name']['section_value']]['greater_60']['male']);
        }
        if(strtolower($gender[0]['section_value']) == 'female'){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] =  ($mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female']+$mainResult[0][$raceName[0]['group_name']['section_value']]['greater_60']['female']);
        }
    }
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['jumlah_besar'] =  $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] + $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'];
}
$listKey=[];
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
        ];
        $filePath = '';
        if (isset($request->report_type) && $request->report_type == 'excel') {
            $filename = 'PatientByAgeGenderEthnicityReport'.time() . '.xlsx';
            $filePath = 'downloads/report/'.$filename;
            $KPIExcel = Excel::store(new PatientByAgeReportExport($mainResult), $filePath, 'public');
            $pathToFile = Storage::url($filePath);
            return response()->json(["message" => "Patient By Age Report", 'result' => $mainResult,  'filepath' => env('APP_URL') . $pathToFile, "code" => 200]);
            } else {
                return response()->json(["message" => "Patient By Age Report", 'result' => $mainResult, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Patient By Age Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }
}

}
