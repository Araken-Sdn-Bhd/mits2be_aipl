<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientRegistration;
use App\Models\GeneralSetting;
use App\Models\HospitalBranchTeamManagement;
use Validator;
use Exception;

class PatientDetailsController extends Controller
{
    public function demmographicDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $details = PatientRegistration::select('id', 'patient_mrn', 'sex', 'birth_date', 'mobile_no', 'citizenship', 'marital_id', 'drug_allergy', 'drug_allergy_description', 'traditional_medication', 'traditional_description', 'other_allergy', 'other_description')->where('id', $request->patient_id)->get();
        $result = [];
        $result['id'] = $details[0]->id;
        $result['patient_mrn'] = $details[0]->patient_mrn;
        $result['birth_date'] = date('d/m/Y', strtotime($details[0]->birth_date));
        $result['age'] = date_diff(date_create($details[0]->birth_date), date_create('today'))->y . " Years old";
        $result['gender'] = $this->getGeneralSettingValue($details[0]->sex);
        $result['marital_status'] = $this->getGeneralSettingValue($details[0]->marital_id);
        $result['contact_no'] = $details[0]->mobile_no;
        $result['Allergies'] = [
            'Drug Allergy' => ($details[0]->drug_allergy == '1') ? 'yes' : 'No',
            'Drug Allergy Desc' => ($details[0]->drug_allergy == '1') ? $details[0]->drug_allergy_description : '--',
            'Traditional Medication/Supplement Allergy' => ($details[0]->traditional_medication == '1') ? 'yes' : 'No',
            'Traditional Medication/Supplement Allergy Desc' => ($details[0]->traditional_medication == '1') ? $details[0]->traditional_description : '--',
            'Others Allergy' => ($details[0]->other_allergy == '1') ? 'yes' : 'No',
            'Others Allergy Desc' => ($details[0]->other_allergy == '1') ? $details[0]->other_description : '--',
        ];
        $result['nationality'] = ($details[0]->citizenship == 0) ? 'Malaysian' : (($details[0]->citizenship == 1) ? 'Permanent Resident' : 'Foreigner');

        return response()->json(["message" => "Patient Demographic Details", 'details' => $result, "code" => 200]);
    }

    public function getGeneralSettingValue($id)
    {
        $val = GeneralSetting::where('id', $id)->pluck('section_value');
        return $val[0];
    }

    public function serachPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required',
            'branch_id' => 'required|integer',
            'service_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $search = [];
        if ($request->branch_id != 0) {
            $search['branch_id'] = $request->branch_id;
        }
        if ($request->service_id != "0") {
            $search['services_type'] = $request->service_id;
        }

        $searchWord = $request->keyword;
        $resultSet = [];
        if ($searchWord) {
            $sql = PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id');
            if (count($search) > 0) {
                $sql = $sql->where($search);
            } else {
                $sql = $sql->where(function ($query) use ($searchWord) {
                    $query->where('patient_mrn', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('name_asin_nric', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('passport_no', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('nric_no', 'LIKE', '%' . $searchWord . '%');
                });
            }
            $resultSet =
                $sql->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        }
        //dd($resultSet);
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $result[$key]['patient_mrn'] = $val['patient_mrn'];
                $result[$key]['name_asin_nric'] = $val['name_asin_nric'];
                $result[$key]['id'] = $val['id'];
                $result[$key]['nric_no'] = $val['nric_no'];
                $result[$key]['passport_no'] = $val['passport_no'];
                $result[$key]['salutation'] = $val['salutation'][0]['section_value'];
                if ($val['appointments'] != null) {
                    if ($val['service'] != null) {
                        $result[$key]['service'] = $val['service']['service_name'];
                    } else {
                        $result[$key]['service'] = 'NA';
                    }
                    $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                    $team_id = $val['appointments'][0]['assign_team'];
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                    $result[$key]['team_name'] = $teamName[0]['team_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                    $result[$key]['appointments'] = 'NA';
                    $result[$key]['team_name'] = 'NA';
                }
                //  dd($result);
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }

    public function patientDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $details = PatientRegistration::select('id', 'patient_mrn', 'sex', 'birth_date', 'mobile_no', 'nric_no', 'citizenship', 'name_asin_nric', 'marital_id')->where('id', $request->patient_id)->get();
        $result = [];
        $result['id'] = $details[0]->id;
        $result['patient_name'] = $details[0]->name_asin_nric;
        $result['patient_mrn'] = $details[0]->patient_mrn;
        $result['nric'] = $details[0]->nric_no;
        $result['birth_date'] = date('d/m/Y', strtotime($details[0]->birth_date));
        $result['age'] = date_diff(date_create($details[0]->birth_date), date_create('today'))->y;
        $result['gender'] = $this->getGeneralSettingValue($details[0]->sex);
        $result['gender_id'] = $details[0]->sex;
        $result['marital_status'] = $this->getGeneralSettingValue($details[0]->marital_id);
        $result['contact_no'] = $details[0]->mobile_no;
        $result['nationality'] = ($details[0]->citizenship == 0) ? 'Malaysian' : (($details[0]->citizenship == 1) ? 'Permanent Resident' : 'Foreigner');

        return response()->json(["message" => "Patient Details", 'details' => $result, "code" => 200]);
    }
}
