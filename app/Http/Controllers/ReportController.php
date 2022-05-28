<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\PatientRegistration;

class ReportController extends Controller
{
    public function getSharpReport(Request $request)
    {
        // $response = PatientShharpRegistrationHospitalManagement::where(['main_psychiatric_diagnosis' => $request->diagnosis])
        //     ->whereBetween('created_at', [$request->fromDate, $request->toDate]);
        $response = SharpRegistrationFinalStep::whereBetween('created_at', [$request->fromDate, $request->toDate]);
        $patient = [];

        if ($response->count() > 0 && $request->diagnosis) {
            foreach ($response->get() as $key => $val) {
                $rf = explode('^', $val['risk']);
                $rsk = PatientShharpRegistrationHospitalManagement::where(['main_psychiatric_diagnosis' => $request->diagnosis])->whereIn('id', $rf)->get()->pluck('patient_mrn_no')->toArray();
                if (count($rsk) > 0) {
                    $patient[] = $rsk[0];
                }
            }
        }

        if ($response->count() > 0 && $request->risk_factor) {
            foreach ($response->get() as $key => $val) {
                $rf = explode('^', $val['risk']);
                $rsk = PatientRiskProtectiveAnswer::where(['factor_type' => 'risk', 'QuestionId' => $request->risk_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_id')->toArray();
                if (count($rsk) > 0) {
                    $patient[] = $rsk[0];
                }
            }
        }

        if ($response->count() > 0 && $request->protective_factor) {
            foreach ($response->get() as $key => $val) {
                $rf = explode('^', $val['protective']);
                $rsk = PatientRiskProtectiveAnswer::where(['factor_type' => 'protective', 'QuestionId' => $request->protective_factor, 'Answer' => 'Yes'])->whereIn('id', $rf)->get()->pluck('patient_mrn_id')->toArray();
                if (count($rsk) > 0) {
                    $patient[] = $rsk[0];
                }
            }
        }

        if ($response->count() > 0 && ($request->self_harm || $request->suicidal_intent || $request->idea_about_method)) {
            foreach ($response->get() as $key => $val) {
                $rf = explode('^', $val['self_harm']);
                $sh = SharpRegistrationSelfHarmResult::whereIn('id', $rf);
                if ($request->self_harm) {
                    $sh->where(['section' => 'Method of Self-Harm'])->Where('section_value', 'like', '%' . $request->self_harm . '%');
                }
                if ($request->suicidal_intent) {
                    $sh->where(['section' => 'Suicidal Intent'])->Where('section_value', 'like', '%' . $request->suicidal_intent . '%');
                }
                if ($request->idea_about_method) {
                    $sh->where(['section' => 'How did Patient Get Idea about Method'])->Where('section_value', 'like', '%' . $request->idea_about_method . '%');
                }
                $ssh = $sh->get()->pluck('patient_id')->toArray();
                if (count($ssh) > 0) {
                    $patient[] = $ssh[0];
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


        $patientDetails =  PatientRegistration::whereIn('id', $patientArray)->where($demo)->get();
        $result = [];
        $result[0] = [
            'DATE' => '1/5/22',
            'TIME' => '9:00:00 AM',
            'NRIC_NO/PASSPORT_NO' => '911121-22-2232',
            'Name' => 'AMIR BIN FALAH',
            'ADDRESS' => 'No 42 ',
            'CITY' => 'kuala lumpur',
            'STATE' => 'kuala lumpur',
            'POSTCODE' => '40432',
            'PHONE NUMBER' => '0123456789',
            'DATE OF BIRTH' => '22/11/1978',
            'RISK FACTOR' => 'Psychiatric Disorder',
            'PROTECTIVE FACTOR' => 'Realistic life goals or future plans',
            'METHOD OF SELF HARM' => 'Drowning',
            'IDEA OF METHOD' => 'Printed media',
            'SUCIDAL INTENT' => 'Handwritten'
        ];
        $result[1] = [
            'DATE' => '5/5/22',
            'TIME' => '11:00:00 AM',
            'NRIC_NO/PASSPORT_NO' => '900021-22-2232',
            'Name' => 'Tan Jing Hui',
            'ADDRESS' => 'No 499 ',
            'CITY' => 'kuala lumpur',
            'STATE' => 'kuala lumpur',
            'POSTCODE' => '40432',
            'PHONE NUMBER' => '0866656789',
            'DATE OF BIRTH' => '22/11/1991',
            'RISK FACTOR' => 'Psychiatric Disorder',
            'PROTECTIVE FACTOR' => 'Realistic life goals or future plans',
            'METHOD OF SELF HARM' => 'Drowning',
            'IDEA OF METHOD' => 'Printed media',
            'SUCIDAL INTENT' => 'Handwritten'
        ];
        return response()->json(["message" => "Patient Details", 'result' => $result, "code" => 200]);
    }
}
