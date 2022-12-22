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
            $demo = [];
            $age=[];

            if ($request->age) {
                $age = GeneralSetting::where('id', $request->age)->first();
                if($age['min_age']!=NULL){
                    $age['agemin']=$age['min_age'];
                }
                if($age['max_age']!=NULL){
                    $age['agemax']=$age['max_age'];
                }
            }
            if ($request->gender) {
                $demo['sex'] = $request->gender;
            }
            if ($request->race) {
                $demo['race_id'] = $request->race;
            }

        $query = DB::table('patient_appointment_details as pad')
        ->select('*')
        ->join('patient_registration as p', function($join) {
            $join->on('pad.patient_mrn_id', '=', 'p.id');
        })
        ->whereBetween('pad.booking_date', [$request->fromDate, $request->toDate])
        ->where('appointment_status', '1');

        if ($demo){
        $query->where($demo);
        }
        
        if ($age){
            
            if($age['agemin'] && $age['agemax']!=NULL){
                
            $query->whereBetween('age',[$age['agemin'],$age['agemax']]);
            }
            if($age['agemin']==NULL) {

                $query->where('age','<=',$age['agemax']);
                
            }
            if($age['agemax']==NULL) {
                $query->where('age','>=',$age['agemin']);
            }
        }
        $query2=$query->get()->toArray();
        $response  = json_decode(json_encode($query2), true);
        

        $patient = [];
        $result = [];
        $index=0;
        if ($response) {

            foreach ($response as $key => $val) {
                    if($val['sex']!=NULL && $val['race_id']!=NULL && $val['age']!=NULL){
                        $gender = GeneralSetting::where(['id' => $val['sex']])->first();
                        $race = GeneralSetting::where(['id' => $val['race_id']])->first();
                        $result[$index]['DATE'] = date('d/m/y', strtotime($val['booking_date']));
                        $result[$index]['Gender'] = $gender['section_value']  ?? 'NA';
                        $result[$index]['race'] = $race['section_value'] ?? 'NA';
                        $result[$index]['Age'] = $val['age'] ?? 'NA';

                        $result[$index]['race_id'] =$val['race_id'] ?? 'NA';
                        $result[$index]['gender_id'] =$val['sex'] ?? 'NA';
                        $index++;
                    }else{
                        continue;
                    }
            }
            
            if ($result) {

                $totalReports = count($result);
        $mainResult = [];

        $race_array = array();

        $count_races=0;
        foreach ($result as $key => $val){

        $raceName = GeneralSetting::where('id',$val['race_id'])->get()->toArray();

        

        if(!in_array($raceName[0]['section_value'], $race_array, true)){
            array_push($race_array, $raceName[0]['section_value']);
               
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
            }


        $male = GeneralSetting::where('section_value','Male')->first();
        $female = GeneralSetting::where('section_value','Female')->first();


    if($val['Age']<10){
        if($val['gender_id'] == $male['id']){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['below_10']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] +=1;
    }
        if($val['gender_id'] == $female['id']){
            $mainResult[0][$raceName[0]['group_name']['section_value']]['10-19']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] +=1;
        }
    }
    if($val['Age']>=10 && $val['Age']<=19){
        if($val['gender_id'] == $male['id']){
            $mainResult[0][$raceName[0]['group_name']['section_value']]['10-19']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] +=1;
        }
        if($val['gender_id'] == $female['id']){
            $mainResult[0][$raceName[0]['group_name']['section_value']]['10-19']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] +=1;
        }
    }
    if($val['Age']>=20 && $val['Age']<=59){
        if($val['gender_id'] == $male['id']){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] +=1;
        }
        if($val['gender_id'] == $female['id']){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['20-59']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] +=1;
        }
        
    }
    
    if($val['Age']>=60){
        if($val['gender_id'] == $male['id']){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['male'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] +=1;
        }
        if($val['gender_id'] == $female['id']){
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['greater_60']['female'] +=1;
            $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'] +=1;
        }
    }
    $mainResult[0]['group_name'][$raceName[0]['section_value']]['jumlah_besar'] =  $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['male'] + $mainResult[0]['group_name'][$raceName[0]['section_value']]['total']['female'];
  
    
    $count_races++;
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
            $testing=2001;
            $KPIExcel = Excel::store(new PatientByAgeReportExport($mainResult,$totalReports), $filePath, 'public');
            $pathToFile = Storage::url($filePath);
            return response()->json(["message" => "Patient By Age Report", 'result' => $mainResult,  'filepath' => env('APP_URL') . $pathToFile, "code" => 200]);
            } else {
                return response()->json(["message" => "Patient By Age Report", 'result' => $mainResult, 'totalReport'=>$totalReports, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Patient By Age Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }else {
        return response()->json(["message" => "Patient By Age Report", 'result' => [], 'filepath' => null, "code" => 200]);
    }
}

}
