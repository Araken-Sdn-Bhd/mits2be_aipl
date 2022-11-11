<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\PatientRegistration;
use App\Models\PatientAppointmentDetails;
use App\Models\Postcode;
use App\Models\State;
use App\Models\PatientShharpRegistrationRiskProtective;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShharpReportExport;
use App\Exports\WorkloadTotalPatienTypeRefferalReportExport;
use App\Exports\PatientActivityReportExport;
use App\Exports\VONActivityReportExport;
use App\Exports\GeneralReportExport;
use App\Exports\KPIReportExport;
use App\Models\ShharpReportGenerateHistory;
use App\Models\PatientAppointmentCategory;
use App\Models\PatientAppointmentVisit;
use App\Models\PatientCounsellorClerkingNotes;
use App\Models\PsychiatryClerkingNote;
use App\Models\PatientAppointmentType;
use App\Models\GeneralSetting;
use App\Models\IcdCode;
use App\Models\StaffManagement;
use App\Models\NetworkingContribution;
use App\Models\VonOrgRepresentativeBackground;
use App\Models\OutReachProjects;
use App\Models\Volunteerism;
use App\Models\HospitalBranchManagement;
use App\Models\SeProgressNote;

class ReportController extends Controller
{
    public function getSharpReport(Request $request)
    {
        // if($request->fromDate && $request->toDate){
            $response = SharpRegistrationFinalStep::select('*', DB::raw('null as METHOD_OF_SELF_HARM'), DB::raw('null as SUCIDAL_INTENT'), DB::raw('null as IDEA_OF_METHOD'))->whereBetween('harm_date', [$request->fromDate, $request->toDate])->where('status', '1')->get()->toArray();
        // }else{
        //     $response = SharpRegistrationFinalStep::select('*', DB::raw('null as METHOD_OF_SELF_HARM'), DB::raw('null as SUCIDAL_INTENT'), DB::raw('null as IDEA_OF_METHOD'))->where('status', '0')->get()->toArray();
        // }
        // dd($response);
        $rftxt = '';
        $ptxt = '';
        $patient = [];
        $result = [];
        if ($response) {
            foreach ($response as $key => $val) {
                if ($val['self_harm']) {
                    $rf = explode('^', $val['self_harm']);
                    $ssh = SharpRegistrationSelfHarmResult::select('section', 'section_value')->whereIn('id', $rf)->get()->toArray();

                    if (count($ssh) > 0) {
                        foreach ($ssh as $k => $v) {
                            $mth = !empty($v['section_value']) ? json_decode($v['section_value'], true) : [];
                            if ($v['section'] == 'Method of Self-Harm') {
                                $response[$key]['METHOD_OF_SELF_HARM'] = ($mth['Method of Self-Harm']) ? implode(',', array_values($mth['Method of Self-Harm'])) : '';
                            }
                            if ($v['section'] == 'Suicidal Intent') {
                                $response[$key]['SUCIDAL_INTENT'] = ($mth['Suicidal Intent']) ? implode(',', array_values($mth['Suicidal Intent'])) : '';
                            }
                            if ($v['section'] == 'How did Patient Get Idea about Method') {
                                $response[$key]['IDEA_OF_METHOD'] = ($mth['How did Patient Get Idea about Method']) ? implode(',', array_values($mth['How did Patient Get Idea about Method'])) : '';
                            }
                        }
                    }
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
            if ($response && $request->risk_factor) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if ($val['risk']) {
                        $rf = explode('^', $val['risk']);
                        $rsk = PatientRiskProtectiveAnswer::where(['QuestionId' => $request->risk_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
                        //  print_r($rf);
                        if ($rsk) {
                            $patient[] = $val['patient_id'];
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
                $rftext = PatientShharpRegistrationRiskProtective::where('id', $request->risk_factor)->get()->pluck('Question')->toArray();
                $rftxt = $rftext[0];
            }

            if ($response && $request->protective_factor) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if ($val['protective']) {
                        $rf = explode('^', $val['protective']);
                        $rsk = PatientRiskProtectiveAnswer::where(['QuestionId' => $request->protective_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
                        if ($rsk) {
                            $patient[] = $val['patient_id'];
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
                $ptxtt = PatientShharpRegistrationRiskProtective::where('id', $request->protective_factor)->get()->pluck('Question')->toArray();
                $ptxt = $ptxtt[0];
            }

            if ($response && $request->self_harm) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if (stripos($val['METHOD_OF_SELF_HARM'], $request->self_harm) !== false) {
                        $patient[] = $val['patient_id'];
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

            if ($response && $request->suicidal_intent) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if (stripos($val['SUCIDAL_INTENT'], $request->suicidal_intent) !== false) {
                        $patient[] = $val['patient_id'];
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

            if ($response && $request->idea_about_method) {
                $unset = [];
                foreach ($response as $key => $val) {
                    if (stripos($val['IDEA_OF_METHOD'], $request->idea_about_method) !== false) {
                        $patient[] = $val['patient_id'];
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
                }

                $index = 0;
                foreach ($response as $k => $v) {
                    $result[$index]['DATE'] = date('d/m/y', strtotime($v['harm_date']));
                    $result[$index]['Time'] = date('h:i A', strtotime($v['harm_time']));
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $patientInfo[$v['patient_id']]['NRIC_NO_PASSPORT_NO'];
                    $result[$index]['Name'] = $patientInfo[$v['patient_id']]['Name'];
                    $result[$index]['ADDRESS'] = $patientInfo[$v['patient_id']]['ADDRESS'];
                    $result[$index]['CITY'] = $patientInfo[$v['patient_id']]['CITY'];
                    $result[$index]['STATE'] = $patientInfo[$v['patient_id']]['STATE'];
                    $result[$index]['POSTCODE'] = $patientInfo[$v['patient_id']]['POSTCODE'];
                    $result[$index]['PHONE_NUMBER'] = $patientInfo[$v['patient_id']]['PHONE_NUMBER'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo[$v['patient_id']]['DATE_OF_BIRTH'];
                    $result[$index]['RISK_FACTOR'] = $rftxt;
                    $result[$index]['PROTECTIVE_FACTOR'] = $ptxt;
                    $result[$index]['METHOD_OF_SELF_HARM'] = $v['METHOD_OF_SELF_HARM'];
                    $result[$index]['SUCIDAL_INTENT'] = $v['SUCIDAL_INTENT'];
                    $result[$index]['IDEA_OF_METHOD'] = $v['IDEA_OF_METHOD'][0];

                    $index++;
                }
            }
            // dd($result);
            if ($result) {
                $totalReports =
                    ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'),
                     DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' 
                     WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 
                     THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  
                     WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  
                     WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' 
                     ELSE 'December' END as month"), 'report_month', 'report_year')
                     ->where('report_type', 'shharp_mgmt')->groupBy('report_month', 'report_year')->get()->toArray();
                $filePath = '';
                if (isset($request->report_type) && $request->report_type == 'excel') {
                    $filePath = 'downloads/report/report-' . time() . '.xlsx';
                    Excel::store(new ShharpReportExport($result, $totalReports), $filePath, 'public');
                    ShharpReportGenerateHistory::create([
                        'generated_by' => ($request->added_by) ? $request->added_by : 1,
                        'report_month' => date('m'),
                        'report_year' => date('Y'),
                        'file_path' => env('APP_URL') . '/storage/app/public/' . $filePath,
                        'report_type' => 'shharp_mgmt',
                        'status' => '1',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    return response()->json(["message" => "Shharp Report", 'result' => $result, 'filepath' =>  env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
                } else {
                    return response()->json(["message" => "Shharp Report", 'result' => $result, 'filepath' =>  '', "code" => 200]);
                }
            } else {
                return response()->json(["message" => "Shharp Report", 'result' => [], 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Shharp Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getTotalPatientTypeRefferalReport(Request $request)
    {
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);

        $ssh = $appointments->get()->toArray();
        // dd($ssh);
        $result = [];
        $cpa = [];
        $vta = [];
        $rfa = ['Walk-In' => 0, 'Refferal' => 0];
        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);

                $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = Postcode::where(['postcode' => $patientInfo['postcode']])->get()->toArray();
                    $st = State::where(['id' => $patientInfo['state_id']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    $city_name = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $state_name = ($st) ? $st[0]['state_name'] : 'NA';
                    $postcode = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';
                    if (array_key_exists($cp[0]['appointment_category_name'], $cpa)) {
                        $cpa[$cp[0]['appointment_category_name']] = $cpa[$cp[0]['appointment_category_name']] + 1;
                    } else {
                        $cpa[$cp[0]['appointment_category_name']] = 1;
                    }
                    if (array_key_exists($vt[0]['appointment_visit_name'], $vta)) {
                        $vta[$vt[0]['appointment_visit_name']] = $vta[$vt[0]['appointment_visit_name']] + 1;
                    } else {
                        $vta[$vt[0]['appointment_visit_name']] = 1;
                    }

                    if (in_array($request->referral_type, [7, 253])) {
                        $rfa['Walk-In'] = $rfa['Walk-In'] + 1;
                    } else {
                        $rfa['Refferal'] = $rfa['Refferal'] + 1;
                    }

                    $result[$index]['DATE'] = date('d/m/Y', strtotime($v['booking_date']));
                    $result[$index]['TIME'] = date('h:i:s A', strtotime($v['booking_time']));
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = ($patientInfo['nric_no']) ? $patientInfo['nric_no'] : $patientInfo['passport_no'];;
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['ADDRESS'] = $patientInfo['address1'] . ' ' . $patientInfo['address2'] . ' ' . $patientInfo['address3'];
                    $result[$index]['CITY'] = $city_name;
                    $result[$index]['STATE'] = $state_name;
                    $result[$index]['POSTCODE'] = $postcode;
                    $result[$index]['PHONE_NUMBER'] = $patientInfo['mobile_no'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo['birth_date'];
                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $index++;
                }
            }
        }
        // dd($result);
        if ($result) {
            $totalPatients = count($result);
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = $diff->format("%a");
            $patientCategories = $cpa;

            $visitTypes = $vta;

            foreach ($visitTypes as $k => $v) {
                $visitTypes[str_replace(' ', '_', $k)] = $v;
            }

            // dd($visitTypes);
            $refferals = $rfa;

            foreach ($refferals as $k => $v) {
                $refferals[str_replace('-', '_', $k)] = $v;
            }

            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filePath = 'downloads/report/report-' . time() . '.xlsx';
                Excel::store(new WorkloadTotalPatienTypeRefferalReportExport($result, $totalPatients, $totalDays, $patientCategories, $visitTypes, $refferals), $filePath, 'public');

                return response()->json([
                    "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, 'Total_Patient' => $totalPatients, 'Total_Days' => $totalDays,
                    'Referal_walk' => $rfa, 'Visit_Type' => $visitTypes, 'refferals' =>  $refferals, 'Category_Patient' => $patientCategories, "code" => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => '', 'Total_Patient' => $totalPatients, 'Total_Days' => $totalDays,
                    'Referal_walk' => $rfa, 'Visit_Type' => $visitTypes, 'refferals' =>  $refferals, 'Category_Patient' => $patientCategories, "code" => 200
                ]);
            }
        } else {
            return response()->json(["message" => "Toal Patient & Type of Refferal Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getPatientActivityReport(Request $request)
    {
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $ssh = $appointments->where('appointment_type', $request->appointment_type);
        if ($request->appointment_status != 0)
            $ssh = $appointments->where('appointment_status', $request->appointment_status);

        $ssh = $appointments->get()->toArray();
        $apcount = [];
        $result = [];
        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                if (array_key_exists($v['patient_mrn_id'], $apcount)) {
                    $apcount[$v['patient_mrn_id']] = $apcount[$v['patient_mrn_id']] + 1;
                } else {
                    $apcount[$v['patient_mrn_id']] = 1;
                }
                $notes = [];
                $icd = [];
                $notes = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('type_diagnosis_id', $request->diagnosis_id)
                    ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                    ->get()->toArray();
                if (count($notes) == 0) {
                    $notes = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('type_diagnosis_id', $request->diagnosis_id)
                        ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                        ->get()->toArray();
                }

                $staff = StaffManagement::select('name')->where('id', $v['assign_team'])->get()->toArray();
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);
                if ($request->gender != 0)
                    $query->where('sex', $request->gender);
                    $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];                   
                    $pc = GeneralSetting::where(['id' => $patientInfo['sex']])->get()->toArray();
                    $st = PatientAppointmentType::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    if ($notes)
                        $icd = IcdCode::where('id', $notes[0]['code_id'])->get()->toArray();
                    $nap = PatientAppointmentDetails::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('booking_date', '>', $v['booking_date'])->where('appointment_status', 0)->first();
                    $nxtAppointments = ($nap) ? $nap->toArray() : [];
                    $gender = ($pc) ? $pc[0]['section_value'] : 'NA';
                    $appointment_type = ($st) ? $st[0]['appointment_type_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';
                    $result[$index]['No']=$index+1;
                    $result[$index]['Next_visit'] = ($nxtAppointments) ? date('d/m/Y', strtotime($nxtAppointments['booking_date'])) : '-';
                    $result[$index]['time_registered'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['time_seen'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['Procedure'] = ($icd) ? $icd['icd_name'] : 'NA';
                    // $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                                        $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['Attending_staff'] = ($staff) ? $staff[0]['name'] : 'NA';
                    $result[$index]['IC_NO'] = '-';
                    $result[$index]['GENDER'] = $gender;
                    $result[$index]['APPOINTMENT_TYPE'] = $appointment_type;
                    $result[$index]['AGE'] = $patientInfo['age'];
                    $result[$index]['DIAGNOSIS'] = ($icd) ? $icd['icd_name'] : 'NA';
                    $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication_des'] : "NA";
                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $result[$index]['app_no'] = 'C' . $apcount[$v['patient_mrn_id']];
                    $index++;
                   
                }
                
            }
            // dd($index);
        }
        //dd($result);
        if ($result) {
            $totalPatients ='Total Patient:   '.count($result).'<br>';
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = 'Total Days:  '.$diff->format("%a").'<br>';
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $filePath = '';
            $filename='';
            $periodofservice='Period of Services :'.$fromDate. ' To '. $toDate .'<br>';
            $AttendNo=34;
            $NoShowNo=21;
            $Attend='Attend:   '.$AttendNo.'<br>';
            $NoShow='No Show:   '.$NoShowNo.'<br>';
            $summary= $periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                // $filePath = 'downloads/report/activity-patient-report-' . time() . '.xlsx';
                $filename = 'patient-report-'.time().'.xls';
                // // Excel::store(new PatientActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate), $filePath, 'public');
                // return Excel::download(new PatientActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate),'activity-patient-report-' . time() . '.xlsx');
                // return response()->json(["message"=>"Patient Activity Report", "excel"=>$excel]);
                //dd($result);
                return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'totalPatients' => $totalPatients,
                    'totalDays' =>  $totalDays,
                    'fromDate' => $fromDate,
                    'toDate' =>  $toDate,
                    'header' => $summary,
                    'filename' => $filename,
                    'code' => 200
                ]);
                // return response()->json([
                //     "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => 'storage/app/public/' . $filePath,
                //     "Total_Days" => $totalDays, "Total_Patient" => $totalPatients, "Attend" => $totalPatients, "No_Show" => $totalPatients, "code" => 200
                // ]);
            } else {
                return response()->json([
                    "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => '',
                    "Total_Days" => $totalDays, "Total_Patient" => $totalPatients, "Attend" => $totalPatients, "No_Show" => $totalPatients, "code" => 200
                ]);
            }
        } else {
            return response()->json(["message" => "Toal Patient & Type of Refferal Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getVONActivityReport(Request $request)
    {
        $from = $request->fromDate;
        $to = $request->toDate;
        $result = [];
        $index = 0;
        $toc = ['individual' => 'INDIVIDUAL', 'org' => 'ORGANIZATION', 'group' => 'GROUP'];
        $toi = ['volunteerism' => 'VOLUNTEERISM', 'networking-contribution' => 'NETWORKING', 'out-reach-project' => 'OUTREACH'];
        $toiArr = ['VOLUNTEER' => 0, 'OUTREACH' => 0, 'NETWORKING' => 0];
        $ssh = VonOrgRepresentativeBackground::whereBetween('created_at', [$from, $to]);
        if ($request->toc != 0)
            $ssh->where('section', $request->toc);
        if ($request->aoi != 0)
            $ssh->where('area_of_involvement', $request->aoi);
        if ($request->screening != 2)
            $ssh->where('screening_mode', $request->screening);
        if ($request->event != 'no-event')
            $ssh->where('name', $request->event);
        if ($request->location == 'mentari') {
            $ssh->where('branch_id', $request->location_value);
        }

        $vorb = $ssh->get()->toArray();
        if ($vorb) {
            foreach ($vorb as $k => $v) {
                $result[$index]['Name'] = $v['name'];
                $result[$index]['Type_of_Collaboration'] = $toc[$v['section']];
                $result[$index]['Type_of_Involvement'] = $toi[$v['area_of_involvement']];
                $result[$index]['Screening_Done'] = ($v['screening_mode'] === 1) ? 'YES' : 'NO';
                $result[$index]['Contact_Number'] = $v['phone_number'];
                $orp = [];
                $vol = [];
                $result[$index]['Cost'] = '-';
                $result[$index]['Others'] = '-';
                $result[$index]['No_of_Participants'] = '-';
                $result[$index]['Mentari'] = '-';
                $result[$index]['Location'] = '-';
                if ($v['area_of_involvement'] == 'out-reach-project') {
                    $toiArr['OUTREACH'] = $toiArr['OUTREACH'] + 1;
                    $orp = OutReachProjects::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($v['area_of_involvement'] == 'networking-contribution') {
                    $toiArr['NETWORKING'] = $toiArr['NETWORKING'] + 1;
                    $orp = NetworkingContribution::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($v['area_of_involvement'] == 'volunteerism') {
                    $toiArr['VOLUNTEER'] = $toiArr['VOLUNTEER'] + 1;
                    $vol = Volunteerism::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($orp) {
                    if (array_key_exists('estimated_budget', $orp[0])) {
                        $budget = $orp[0]['estimated_budget'];
                    } else {
                        $budget = $orp[0]['budget'];
                    }
                    $result[$index]['Cost'] = 'RM' . $budget;
                    $result[$index]['Location'] = strtoupper($orp[0]['project_loaction']);
                    $result[$index]['Mentari'] = ($orp[0]['project_loaction'] == 'mentari') ? $orp[0]['project_loaction_value'] : '-';
                    $result[$index]['Others'] = ($orp[0]['project_loaction'] != 'mentari') ? $orp[0]['project_loaction_value'] : '-';
                    $result[$index]['No_of_Participants'] = $orp[0]['no_of_paricipants'];
                }
                if ($vol) {
                    $result[$index]['Cost'] = '-';
                    $result[$index]['Others'] = '-';
                    $result[$index]['Location'] = 'MENTARI';
                    $brnchName = HospitalBranchManagement::where('id', $v['branch_id'])->get()->toArray();
                    $result[$index]['No_of_Participants'] = '-';
                    $result[$index]['Mentari'] = $brnchName[0]['hospital_branch_name'];
                }

                if ($request->location == 'other') {
                    if ($result[$index]['Others'] != $request->location_value) {
                        unset($result[$index]);
                        if ($v['area_of_involvement'] == 'out-reach-project') {
                            $toiArr['OUTREACH'] = $toiArr['OUTREACH'] - 1;
                        }
                        if ($v['area_of_involvement'] == 'networking-contribution') {
                            $toiArr['NETWORKING'] = $toiArr['NETWORKING'] - 1;
                        }
                        if ($v['area_of_involvement'] == 'volunteerism') {
                            $toiArr['VOLUNTEER'] = $toiArr['VOLUNTEER'] - 1;
                        }
                    }
                }

                $index++;
            }
        }

        if ($result) {
            $totalPatients = count($result);
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = $diff->format("%a");
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filePath = 'downloads/report/activity-von-report-' . time() . '.xlsx';
                Excel::store(new VONActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate, $toiArr), $filePath, 'public');
                return response()->json(["message" => "Activity VON Report", 'result' => $result, 'toiArr' => $toiArr, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "Activity VON Report", 'result' => $result, 'toiArr' => $toiArr, 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Activity VON Report", 'result' => [], 'toiArr' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getGeneralReport(Request $request)
    {
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $ssh = $appointments->where('appointment_type', $request->appointment_type);

        $ssh = $appointments->get()->toArray();

        $demo = [];
        $result = [];
        if ($request->name) {
            $demo['name_asin_nric'] = $request->name;
        }
        if ($request->citizenship) {
            $demo['citizenship'] = $request->citizenship;
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

        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                $notes = [];
                $icd = [];
                $notes = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('type_diagnosis_id', $request->diagnosis_id)
                    ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                    ->get()->toArray();
                if (count($notes) == 0) {
                    $notes = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('type_diagnosis_id', $request->diagnosis_id)
                        ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                        ->get()->toArray();
                }

                $staff = StaffManagement::select('name')->where('id', $v['assign_team'])->get()->toArray();
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($demo)
                    $query->where($demo);
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);

                $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = Postcode::where(['postcode' => $patientInfo['postcode']])->get()->toArray();
                    $st = State::where(['id' => $patientInfo['state_id']])->get()->toArray();
                    $sex = GeneralSetting::where(['id' => $patientInfo['sex']])->get()->toArray();
                    $citizenship = GeneralSetting::where(['id' => $patientInfo['citizenship']])->get()->toArray();
                    $race = GeneralSetting::where(['id' => $patientInfo['race_id']])->get()->toArray();
                    $religion = GeneralSetting::where(['id' => $patientInfo['religion_id']])->get()->toArray();
                    $marital = GeneralSetting::where(['id' => $patientInfo['marital_id']])->get()->toArray();
                    $accomodation = GeneralSetting::where(['id' => $patientInfo['accomodation_id']])->get()->toArray();
                    $education_level = GeneralSetting::where(['id' => $patientInfo['education_level']])->get()->toArray();
                    $occupation_status = GeneralSetting::where(['id' => $patientInfo['occupation_status']])->get()->toArray();
                    $fee_exemption_status = GeneralSetting::where(['id' => $patientInfo['fee_exemption_status']])->get()->toArray();
                    $occupation_sector = GeneralSetting::where(['id' => $patientInfo['occupation_sector']])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    $city_name = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $state_name = ($st) ? $st[0]['state_name'] : 'NA';
                    $postcode = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $gender = ($sex) ? $sex[0]['section_value'] : 'NA';
                    $citizenshipValue = ($citizenship) ? $citizenship[0]['section_value'] : 'NA';
                    $raceValue = ($race) ? $race[0]['section_value'] : 'NA';
                    $religionValue = ($religion) ? $religion[0]['section_value'] : 'NA';
                    $maritalValue = ($marital) ? $marital[0]['section_value'] : 'NA';
                    $accomodationValue = ($accomodation) ? $accomodation[0]['section_value'] : 'NA';
                    $education_levelValue = ($education_level) ? $education_level[0]['section_value'] : 'NA';
                    $occupation_statusValue = ($occupation_status) ? $occupation_status[0]['section_value'] : 'NA';
                    $fee_exemption_statusValue = ($fee_exemption_status) ? $fee_exemption_status[0]['section_value'] : 'NA';
                    $occupation_sectorValue = ($occupation_sector) ? $occupation_sector[0]['section_value'] : 'NA';
                    $apt = PatientAppointmentType::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    if ($notes)
                        $icd = IcdCode::where('id', $notes[0]['code_id'])->get()->toArray();
                    $appointment_type = ($apt) ? $apt[0]['appointment_type_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';

                    $result[$index]['Registration_date'] = date('d/m/Y', strtotime($patientInfo['created_at']));
                    $result[$index]['Registration_Time'] = date('h:i:s A', strtotime($patientInfo['created_at']));
                    $result[$index]['nric_no'] = $patientInfo['nric_no'];
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['Attendance_status'] = $v['appointment_status'];
                    // $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['ADDRESS'] = $patientInfo['address1'] . ' ' . $patientInfo['address2'] . ' ' . $patientInfo['address3'];
                    $result[$index]['CITY'] = $city_name;
                    $result[$index]['STATE'] = $state_name;
                    $result[$index]['POSTCODE'] = $postcode;
                    $result[$index]['PHONE_NUMBER'] = $patientInfo['mobile_no'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo['birth_date'];
                    $result[$index]['AGE'] = $patientInfo['age'];
                    $result[$index]['citizenship'] = $citizenshipValue;
                    $result[$index]['race'] = $raceValue;
                    $result[$index]['religion'] = $religionValue;
                    $result[$index]['marital'] = $maritalValue;
                    $result[$index]['accomodation'] = $accomodationValue;
                    $result[$index]['education_level'] = $education_levelValue;
                    $result[$index]['occupation_status'] = $occupation_statusValue;
                    $result[$index]['fee_exemption_status'] = $fee_exemption_statusValue;
                    $result[$index]['occupation_sector'] = $occupation_sectorValue;
                    $result[$index]['GENDER'] = $gender;
                    $result[$index]['APPOINTMENT_TYPE'] = $appointment_type;
                    $result[$index]['DIAGNOSIS'] = ($icd) ? $icd['icd_name'] : 'NA';
                    $result[$index]['DIAGNOSIS_CODE'] = ($icd) ? $icd['icd_code'] : 'NA';
                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $result[$index]['Attending_staff'] = ($staff) ? $staff[0]['name'] : 'NA';
                    $result[$index]['outcome'] = '-';
                    $result[$index]['category_of_services'] = '-';
                    $index++;
                }
            }
        }
        // dd($result);
        if ($result) {
            $totalReports = count($result);
            
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filePath = 'downloads/report/general-report-' . time() . '.xlsx';
                Excel::store(new GeneralReportExport($result, $totalReports, $request->fromDate, $request->toDate), $filePath, 'public');
                return response()->json(["message" => "General Report", 'result' => $result, 'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "General Report", 'result' => $result, 'filepath' => '', "code" => 200]);
            }
        } else {
            return response()->json(["message" => "General Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getKPIReport(Request $request)
    {
        $months = [
            'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'
        ];
        $startYear = date('Y', strtotime($request->fromDate));
        $endYear = date('Y', strtotime($request->toDate));

        $result = [];
        $resultSet = [];
        $finalResult = [];
        $notes = SeProgressNote::whereBetween('date', [$request->fromDate, $request->toDate]);
        if ($request->job_status != 0)
            $ssh = $notes->where('employment_status', $request->job_status);
        $ssh = $notes->get()->toArray();
        // dd($ssh);
        if ($ssh) {
            foreach ($ssh as $key => $val) {
                $query = DB::table('staff_management')
                    ->select('hospital_branch__details.hospital_code')
                    ->join('hospital_branch__details', 'staff_management.branch_id', '=', 'hospital_branch__details.id')
                    ->where('staff_management.id', $val['added_by']);
                if ($request->hospital_id != 0) {
                    $query->where('staff_management.branch_id', $request->hospital_id);
                }
                if ($request->state_id != 0) {
                    $query->where('hospital_branch__details.branch_state', $request->state_id);
                }

                $rs = $query->get()->toArray();
                if ($rs) {
                    $resultSet[] = ['hospital_code' => $rs[0]->hospital_code, 'date' => $val['date'], 'employment_status' => $val['employment_status'], 'status' => $val['status']];
                    //  $result[$resultSet['hospital_code']][$year][$month]['new_job'] = 0;
                    //  $result[$resultSet['hospital_code']][$year][$month]['ongoing_job'] = ($val['employment_status'] == 1)?;
                }
            }
        }
        $yearArray = [];
        if ($resultSet) {
            foreach ($resultSet as $key => $val) {
                // dd($val);
                $year = date('Y', strtotime($val['date']));
                $month = date('m', strtotime($val['date']));
                $result[$val['hospital_code']][$year][$month]['month_name'] = '';
                $result[$val['hospital_code']][$year][$month]['new_job'] = 0;
                $result[$val['hospital_code']][$year][$month]['ongoing_job'] = 0;
                $result[$val['hospital_code']][$year][$month]['total_caseload'] = 0;
                $result[$val['hospital_code']][$year][$month]['total_dismissed'] = 0;
                $result[$val['hospital_code']][$year][$month]['kpi'] = 0;
            }
            foreach ($resultSet as $key => $val) {
                $year = date('Y', strtotime($val['date']));
                $month = date('m', strtotime($val['date']));
                if (array_key_exists($year, $yearArray) && !in_array($month, $yearArray[$year])) {
                    $yearArray[$year][] =  (int)$month;
                } else if (!array_key_exists($year, $yearArray)) {
                    $yearArray[$year][] =  (int)$month;
                }
                $result[$val['hospital_code']][$year][$month]['month_name'] = $months[date('m', strtotime($val['date'])) - 1];
                $result[$val['hospital_code']][$year][$month]['new_job'] =  ($val['status'] == 1 && $val['employment_status'] == 2) ? ($result[$val['hospital_code']][$year][$month]['new_job'] + 1) : $result[$val['hospital_code']][$year][$month]['new_job'];
                $result[$val['hospital_code']][$year][$month]['ongoing_job'] =  ($val['employment_status'] == 1) ? ($result[$val['hospital_code']][$year][$month]['ongoing_job'] + 1) : $result[$val['hospital_code']][$year][$month]['ongoing_job'];
                $result[$val['hospital_code']][$year][$month]['total_caseload'] = ($val['status'] == 2 || $val['status'] == 1 || $val['status'] == 0) ? ($result[$val['hospital_code']][$year][$month]['total_caseload'] + 1) : $result[$val['hospital_code']][$year][$month]['total_caseload'];
                $result[$val['hospital_code']][$year][$month]['total_dismissed'] = ($val['status'] == 2) ? ($result[$val['hospital_code']][$year][$month]['total_dismissed'] + 1) : $result[$val['hospital_code']][$year][$month]['total_dismissed'];
                $result[$val['hospital_code']][$year][$month]['kpi'] = ($result[$val['hospital_code']][$year][$month]['total_caseload'] != 0) ? (($result[$val['hospital_code']][$year][$month]['new_job'] + $result[$val['hospital_code']][$year][$month]['ongoing_job']) / $result[$val['hospital_code']][$year][$month]['total_caseload']) * 100 : 0;
            }
        }
        foreach ($yearArray as $k => $v) {
            sort($v);
            $yearArray[$k] = $v;
        }

        // $filePath = 'downloads/report/kpi-report' . '.xlsx';
        // Excel::store(new KPIReportExport($result, $yearArray), $filePath, 'public');
        // dd($yearArray);
        // dd($result);
        if ($result) {
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filePath = 'downloads/report/kpi-report-rg-'.time() . '.xlsx';
                Excel::store(new KPIReportExport($result, $yearArray, $months), $filePath, 'public');
                return response()->json(["message" => "KPI Report", 'result' => $result,  'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
            } else {
                return response()->json(["message" => "KPI Report", 'result' => $result,  'filepath' => null, "code" => 200]);
            }

        }return response()->json(["message" => "KPI Report", 'result' => [], 'filepath' => null, "code" => 200]);
    }
}
