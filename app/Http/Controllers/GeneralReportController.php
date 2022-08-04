<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\PatientRegistration;
use App\Models\Postcode;
use App\Models\State;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralReportExport;
use App\Models\PatientShharpRegistrationHospitalManagement;

use App\Models\PatientAppointmentDetails;

class GeneralReportController extends Controller
{
    //

    public function getGeneralReport(Request $request)
    {
        $response = PatientAppointmentDetails::select('*', DB::raw('patient_category'), DB::raw('type_visit'), DB::raw('appointment_type'))
        ->whereBetween('booking_date', [$request->fromDate, $request->toDate])
        ->where('appointment_status', '1')->get()->toArray();
        $ptxt = '';
        $patient = [];
        $result = [];
        if ($response) {
            foreach ($response as $key => $val) {
                if ($val['booking_date']) {
                    $patient[] = $val['patient_mrn_id'];
                }
            }

            if ($response && $request->diagnosis) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if ($val['hospital_mgmt']) {
                        $rf = $val['hospital_mgmt'];
                        $rsk = PatientShharpRegistrationHospitalManagement::where(['main_psychiatric_diagnosis' => $request->diagnosis])->where('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
                        if ($rsk) {
                            $patient[] = $rsk[0];
                        } else {
                            $unset[] = $key;
                        }
                    } else {
                        $unset[] = $key;
                    }
                }

                if ($unset) {
                    foreach ($unset as $u) {
                        unset($response[$u]);
                    }
                }
            }

            if ($response && $request->patient_category) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if ($val['patient_category']) {
                        $rf = explode('^', $val['patient_category']);
                        $rsk = PatientAppointmentDetails::where(['patient_mrn_id' => $request->patient_category])->whereIn('id', $rf)->get()->pluck('patient_mrn_id')->toArray();
                        if ($rsk) {
                            $patient[] = $val['patient_mrn_id'];
                        } else {
                            $unset[] = $key;
                        }
                    } else {
                        $unset[] = $key;
                    }
                }
                if ($unset) {
                    foreach ($unset as $u) {
                        unset($response[$u]);
                    }
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
            if ($request->patient_category) {
                $demo['patient_category'] = $request->patient_category;
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

            $patientDetails =  PatientRegistration::whereIn('id', $patientArray)->get()->toArray();
           
            if ($patientDetails) {
                $patientInfo = [];
                foreach ($patientDetails as $key => $val) {
                    $patientInfo[$val['id']]['Name'] = $val['name_asin_nric'];
                    $patientInfo[$val['id']]['NRIC_NO_PASSPORT_NO'] = ($val['nric_no']) ? $val['nric_no'] : $val['passport_no'];
                    $patientInfo[$val['id']]['ADDRESS'] = $val['address1'] . ' ' . $val['address2'] . ' ' . $val['address3'];
                    $patientInfo[$val['id']]['PHONE_NUMBER'] = $val['mobile_no'];
                    $patientInfo[$val['id']]['DATE_OF_BIRTH'] = date('d/m/Y', strtotime($val['birth_date']));
                    $patientInfo[$val['id']]['REFERRAL_TYPE'] = $val['referral_type'];
                    $pc = Postcode::where(['postcode' => $val['postcode']])->get()->toArray();
                    $st = State::where(['id' => $val['state_id']])->get()->toArray();;
                    $patientInfo[$val['id']]['CITY'] = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $patientInfo[$val['id']]['STATE'] = ($st) ? $st[0]['state_name'] : 'NA';
                    $patientInfo[$val['id']]['POSTCODE'] = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $gn = PatientAppointmentDetails::where(['patient_mrn_id' => $val['id']])->get()->toArray();
                    $patientInfo[$val['id']]['patient_category'] = ($gn) ? $gn[0]['patient_category'] : 'NA';
                }

                $index = 0;
                foreach ($response as $k => $v) {
                    $result[$index]['DATE'] = date('d/m/y', strtotime($v['booking_date']));
                    $result[$index]['Time'] = date('h:i A', strtotime($v['booking_time']));
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $patientInfo[$v['patient_mrn_id']]['NRIC_NO_PASSPORT_NO'] ?? 'NA';
                    $result[$index]['Name'] = $patientInfo[$v['patient_mrn_id']]['Name'] ?? 'NA';
                    $result[$index]['ADDRESS'] = $patientInfo[$v['patient_mrn_id']]['ADDRESS'] ?? 'NA';
                    $result[$index]['CITY'] = $patientInfo[$v['patient_mrn_id']]['CITY'] ?? 'NA';
                    $result[$index]['STATE'] = $patientInfo[$v['patient_mrn_id']]['STATE'] ?? 'NA';
                    $result[$index]['POSTCODE'] = $patientInfo[$v['patient_mrn_id']]['POSTCODE'] ?? 'NA';
                    $result[$index]['PHONE_NUMBER'] = $patientInfo[$v['patient_mrn_id']]['PHONE_NUMBER'] ?? 'NA';
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo[$v['patient_mrn_id']]['DATE_OF_BIRTH'] ?? 'NA';
                    $result[$index]['REFERRAL_TYPE'] = $patientInfo[$v['patient_mrn_id']]['REFERRAL_TYPE'] ?? 'NA';
                   $result[$index]['patient_category'] = $patientInfo[$v['patient_mrn_id']]['patient_category'] ?? 'NA';
                    $index++;
                }
            }

            if ($result) {
                $totalReports = count($result);
        //   dd($result);       
                $filePath = 'downloads/report/report-' . time() . '.xlsx';
                Excel::store(new GeneralReportExport($result, $totalReports, $request->fromDate, $request->toDate), $filePath, 'public');              

                return response()->json(["message" => "General Report", 'result' => $result, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "General Report", 'result' => [], 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "General Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

}
