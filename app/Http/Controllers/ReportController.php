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

class ReportController extends Controller
{
    public function getSharpReport(Request $request)
    {
        // $response = PatientShharpRegistrationHospitalManagement::where(['main_psychiatric_diagnosis' => $request->diagnosis])
        //     ->whereBetween('created_at', [$request->fromDate, $request->toDate]);

        $response = SharpRegistrationFinalStep::whereBetween('harm_date', [$request->fromDate, $request->toDate])->where('status', '1')->get()->toArray();
        //dd(($response));
        $rftxt = '';
        $ptxt = '';
        $patient = [];
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
        //dd($response);
        if ($response && $request->risk_factor) {
            $unset = [];
            foreach ($response as $key => $val) {
                if ($val['risk']) {
                    $rf = explode('^', $val['risk']);
                    $rsk = PatientRiskProtectiveAnswer::where(['factor_type' => 'risk', 'QuestionId' => $request->risk_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
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
            $rftext = PatientShharpRegistrationRiskProtective::where('id', $request->risk_factor)->get()->pluck('Question')->toArray();
            $rftxt = $rftext[0];
        }
        if ($response && $request->protective_factor) {
            $unset = [];
            foreach ($response as $key => $val) {
                if ($val['protective']) {
                    $rf = explode('^', $val['protective']);
                    $rsk = PatientRiskProtectiveAnswer::where(['factor_type' => 'protective', 'QuestionId' => $request->protective_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
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
            $ptxtt = PatientShharpRegistrationRiskProtective::where('id', $request->protective_factor)->get()->pluck('Question')->toArray();
            $ptxt = $ptxtt[0];
        }
        //dd($response);
        if ($response && $request->self_harm) {
            $unset = [];
            foreach ($response as $key => $val) {
                if ($val['protective']) {
                    $rf = explode('^', $val['self_harm']);
                    $sh = SharpRegistrationSelfHarmResult::whereIn('id', $rf);
                    if ($request->self_harm) {
                        $sh->where(['section' => 'Method of Self-Harm'])->Where('section_value', 'like', '%' . $request->self_harm . '%');
                    }

                    $ssh = $sh->get()->pluck('patient_id')->toArray();
                    if (count($ssh) > 0) {
                        $patient[] = $ssh[0];
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
        if ($response && $request->suicidal_intent) {
            $unset = [];
            foreach ($response as $key => $val) {
                if ($val['protective']) {
                    $rf = explode('^', $val['self_harm']);
                    $sh = SharpRegistrationSelfHarmResult::whereIn(
                        'id',
                        $rf
                    );

                    if ($request->suicidal_intent) {
                        $sh->where(['section' => 'Suicidal Intent'])->Where('section_value', 'like', '%' . $request->suicidal_intent . '"%');
                    }
                    $ssh = $sh->get()->pluck('patient_id')->toArray();
                    if (count($ssh) > 0) {
                        $patient[] = $ssh[0];
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
        if ($response && $request->idea_about_method) {
            $unset = [];
            foreach ($response as $key => $val) {
                if ($val['protective']) {
                    $rf = explode('^', $val['self_harm']);
                    $sh = SharpRegistrationSelfHarmResult::whereIn(
                        'id',
                        $rf
                    );

                    if ($request->idea_about_method) {
                        $sh->where(['section' => 'How did Patient Get Idea about Method'])->Where('section_value', 'like', '%' . $request->idea_about_method . '%');
                    }
                    $ssh = $sh->get()->pluck('patient_id')->toArray();
                    if (count($ssh) > 0) {
                        $patient[] = $ssh[0];
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

        // dd($response);

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
        $result = [];
        if ($patientDetails) {
            // dd($patientDetails);
            $patientInfo = [];
            foreach ($patientDetails as $key => $val) {
                $patientInfo[$val['id']]['Name'] = $val['name_asin_nric'];
                $patientInfo[$val['id']]['NRIC_NO_PASSPORT_NO'] = ($val['nric_no']) ? $val['nric_no'] : $val['passport_no'];
                $patientInfo[$val['id']]['ADDRESS'] = $val['address1'] . ' ' . $val['address2'] . ' ' . $val['address3'];
                $patientInfo[$val['id']]['PHONE_NUMBER'] = $val['mobile_no'];
                $patientInfo[$val['id']]['DATE_OF_BIRTH'] = date('d/m/Y', strtotime($val['birth_date']));
                $pc = Postcode::where(['postcode' => $val['postcode']])->get()->toArray();
                $st = State::where(['id' => $val['state_id']])->get()->toArray();;
                $patientInfo[$val['id']]['CITY'] = ($pc) ? $pc[0]['city_name'] : 'NA';
                $patientInfo[$val['id']]['STATE'] = ($st) ? $st[0]['state_name'] : 'NA';
                $patientInfo[$val['id']]['POSTCODE'] = ($pc) ? $pc[0]['postcode'] : 'NA';
            }
            // dd($response);

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
                $result[$index]['METHOD_OF_SELF_HARM'] = $request->self_harm;
                $result[$index]['SUCIDAL_INTENT'] = $request->suicidal_intent;
                $result[$index]['IDEA_OF_METHOD'] = $request->idea_about_method;

                $index++;
            }
        }

        // Excel::download('report', 'xx', function ($excel) use ($result) {

        //     $excel->sheet('SHHARP', function ($sheet) use ($result) {

        //         $sheet->fromArray(array(
        //             $result
        //         ));
        //     });
        // });

        return response()->json(["message" => "Patient Details", 'result' => $result, "code" => 200]);
    }
}
