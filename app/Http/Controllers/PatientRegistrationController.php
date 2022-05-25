<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'salutation_id' => 'required|integer',
            'name_asin_nric' => 'required|string',
            'citizenship' => 'required|integer',
            'sex' => 'required|integer',
            'birth_date' => 'required',
            'age' => 'integer',
            'mobile_no' => 'required',
            'house_no' => '',
            'services_type' => 'required',
            'referral_type' => 'required',
            'referral_letter' => 'max:10240',
            'address1' => 'required',
            'kin_name_asin_nric' => 'required|string',
            'kin_relationship_id' => 'required|integer',
            'kin_mobile_no' => 'required|string',
            'kin_address1' => 'required|string',
            'drug_allergy' => 'required',
            'traditional_medication' => 'required',
            'other_allergy' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $patientregistration = [
            'added_by' =>  $request->added_by,
            'citizenship' =>  $request->citizenship,
            'salutation_id' =>  $request->salutation_id,
            'name_asin_nric' =>  $request->name_asin_nric,
            'sex' =>  $request->sex,
            'birth_date' =>  $request->birth_date,
            'age' =>  $request->age,
            'mobile_no' =>  $request->mobile_no,
            'house_no' =>  $request->house_no,
            'hospital_mrn_no' =>  $request->hospital_mrn_no,
            'mintari_mrn_no' =>  $request->mintari_mrn_no,
            'services_type' =>  $request->services_type,
            'referral_type' =>  $request->referral_type,
            'referral_letter' =>  $request->referral_letter,
            'address1' =>  $request->address1,
            'address2' =>  $request->address2,
            'address3' =>  $request->address3,
            'state_id' =>  $request->state_id,
            'city_id' =>  $request->city_id,
            'postcode' =>  $request->postcode,
            'race_id' =>  $request->race_id,
            'religion_id' =>  $request->religion_id,
            'marital_id' =>  $request->marital_id,
            'accomodation_id' => $request->accomodation_id,
            'education_level' => $request->education_level,
            'occupation_status' => $request->occupation_status,
            'fee_exemption_status' => $request->fee_exemption_status,
            'occupation_sector' => $request->occupation_sector,
            'kin_name_asin_nric' => $request->kin_name_asin_nric,
            'kin_relationship_id' => $request->kin_relationship_id,
            'kin_mobile_no' => $request->kin_mobile_no,
            'kin_house_no' => $request->kin_house_no,
            'kin_address1' => $request->kin_address1,
            'kin_address2' => $request->kin_address2,
            'kin_address3' => $request->kin_address3,
            'kin_state_id' => $request->kin_state_id,
            'kin_city_id' => $request->kin_city_id,
            'kin_postcode' => $request->kin_postcode,
            'drug_allergy' => $request->drug_allergy,
            'drug_allergy_description' => $request->drug_allergy_description,
            'traditional_medication' => $request->traditional_medication,
            'traditional_description' => $request->traditional_description,
            'other_allergy' => $request->other_allergy,
            'other_description' => $request->other_description,
            'status' => "1"
        ];


        $validateCitizenship = [];

        if ($request->citizenship == '1') {
            $validateCitizenship['nric_type'] = 'required';
            $validateCitizenship['nric_no'] = 'required|unique:patient_registration';
            $patientregistration['nric_type'] =  $request->nric_type;
            $patientregistration['nric_no'] =  $request->nric_no;
        } else if ($request->citizenship == '2') {
            $validateCitizenship['nric_no'] = 'required|unique:patient_registration';
            $patientregistration['nric_no'] =  $request->nric_no;
        } else if ($request->citizenship == '3') {
            $validateCitizenship['passport_no'] = 'required|string|unique:patient_registration';
            $validateCitizenship['expiry_date'] = 'required';
            $validateCitizenship['country_id'] = 'required|integer';
            $patientregistration['passport_no'] =  $request->passport_no;
            $patientregistration['expiry_date'] =  $request->expiry_date;
            $patientregistration['country_id'] =  $request->country_id;
        }
        $validator = Validator::make($request->all(), $validateCitizenship);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }


        if (!empty($request->input('referral_letter '))) {
            $files = $request->file('referral_letter');
            $isUploaded = upload_file($files, 'PatientRegistration');
            $patientregistration['referral_letter'] =  $isUploaded->getData()->path;
        } else {
            $patientregistration['referral_letter'] = '';
        }

        try {
            $Patient = PatientRegistration::firstOrCreate($patientregistration);
            $MRN = $this->generateMRNString(10, $Patient['id']);
            PatientRegistration::where('id', $Patient['id'])->update(['patient_mrn' => $MRN]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Patient Registration' => $patientregistration, "code" => 200]);
        }
        return response()->json(["message" => "Patient Registration has been done successfully!", "code" => 200]);
    }

    public function generateMRNString($length = 9, $id)
    {
        $randomString = '';
        for ($i = 1; $i < ($length - strlen($id)); $i++) {
            $randomString .= 0;
        }
        return 'MRN' . $randomString . $id;
    }


    public function getPatientRegistrationById(Request $request)
    {
        $list = PatientRegistration::where('id', '=', $request->id)->with('salutation:section_value,id')
            ->with('gender:section_value,id')->with('maritialstatus:section_value,id')
            ->with('citizenships:citizenship_name,id')->get();
        return response()->json(["message" => "Patient Detail", 'list' => $list, "code" => 200]);
    }

    public function getPatientRegistrationList()
    {
        $list = PatientRegistration::where('status', '=', '1')
            ->with('salutation:section_value,id')->with('service:service_name,id')
            ->with('appointments', function ($query) {
                $query->where('appointment_status', '=', '1');
            })
            ->get()->toArray();
        //dd($list[0]['service']);
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'];
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'];
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y;
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
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }


    public function updatePatientRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'salutation_id' => 'required|integer',
            'name_asin_nric' => 'required|string',
            'citizenship' => 'required|integer',
            'sex' => 'required|integer',
            'birth_date' => 'required',
            'age' => 'integer',
            'mobile_no' => 'required',
            'house_no' => '',
            'services_type' => 'required',
            'referral_type' => 'required',
            'referral_letter' => 'max:10240',
            'address1' => 'required',
            'kin_name_asin_nric' => 'required|string',
            'kin_relationship_id' => 'required|integer',
            'kin_mobile_no' => 'required|string',
            'kin_address1' => 'required|string',
            'drug_allergy' => 'required',
            'traditional_medication' => 'required',
            'other_allergy' => 'required',
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $patientregistration = [
            'added_by' =>  $request->added_by,
            'citizenship' =>  $request->citizenship,
            'salutation_id' =>  $request->salutation_id,
            'name_asin_nric' =>  $request->name_asin_nric,
            'sex' =>  $request->sex,
            'birth_date' =>  $request->birth_date,
            'age' =>  $request->age,
            'mobile_no' =>  $request->mobile_no,
            'house_no' =>  $request->house_no,
            'hospital_mrn_no' =>  $request->hospital_mrn_no,
            'mintari_mrn_no' =>  $request->mintari_mrn_no,
            'services_type' =>  $request->services_type,
            'referral_type' =>  $request->referral_type,
            'referral_letter' =>  $request->referral_letter,
            'address1' =>  $request->address1,
            'address2' =>  $request->address2,
            'address3' =>  $request->address3,
            'state_id' =>  $request->state_id,
            'city_id' =>  $request->city_id,
            'postcode' =>  $request->postcode,
            'race_id' =>  $request->race_id,
            'religion_id' =>  $request->religion_id,
            'marital_id' =>  $request->marital_id,
            'accomodation_id' => $request->accomodation_id,
            'education_level' => $request->education_level,
            'occupation_status' => $request->occupation_status,
            'fee_exemption_status' => $request->fee_exemption_status,
            'occupation_sector' => $request->occupation_sector,
            'kin_name_asin_nric' => $request->kin_name_asin_nric,
            'kin_relationship_id' => $request->kin_relationship_id,
            'kin_mobile_no' => $request->kin_mobile_no,
            'kin_house_no' => $request->kin_house_no,
            'kin_address1' => $request->kin_address1,
            'kin_address2' => $request->kin_address2,
            'kin_address3' => $request->kin_address3,
            'kin_state_id' => $request->kin_state_id,
            'kin_city_id' => $request->kin_city_id,
            'kin_postcode' => $request->kin_postcode,
            'drug_allergy' => $request->drug_allergy,
            'drug_allergy_description' => $request->drug_allergy_description,
            'traditional_medication' => $request->traditional_medication,
            'traditional_description' => $request->traditional_description,
            'other_allergy' => $request->other_allergy,
            'other_description' => $request->other_description,
            'status' => "1"
        ];

        $validateCitizenship = [];

        if ($request->citizenship == '1') {
            $validateCitizenship['nric_type'] = 'required';
            $validateCitizenship['nric_no'] = 'required';
            $patientregistration['nric_type'] =  $request->nric_type;
            $patientregistration['nric_no'] =  $request->nric_no;
            if (!$this->checkIFPatientExists('nric_no', $request->nric_no, $request->id)) {
                return response()->json(["message" => "Patient NRIC NO already exists", "code" => 422]);
            }
        } else if ($request->citizenship == '2') {
            $validateCitizenship['nric_no'] = 'required';
            $patientregistration['nric_no'] =  $request->nric_no;
            if (!$this->checkIFPatientExists('nric_no', $request->nric_no, $request->id)) {
                return response()->json(["message" => "Patient NRIC NO already exists", "code" => 422]);
            }
        } else if ($request->citizenship == '3') {
            $validateCitizenship['passport_no'] = 'required';
            $validateCitizenship['expiry_date'] = 'required';
            $validateCitizenship['country_id'] = 'required|integer';
            $patientregistration['passport_no'] =  $request->passport_no;
            $patientregistration['expiry_date'] =  $request->expiry_date;
            $patientregistration['country_id'] =  $request->country_id;
            if (!$this->checkIFPatientExists('passport_no', $request->passport_no, $request->id)) {
                return response()->json(["message" => "Patient Passort NO already exists", "code" => 422]);
            }
        }
        $validator = Validator::make($request->all(), $validateCitizenship);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if (!empty($request->input('referral_letter '))) {
            $files = $request->file('referral_letter');
            $isUploaded = upload_file($files, 'PatientRegistration');
            $patientregistration['referral_letter'] =  $isUploaded->getData()->path;
        } else {
            $patientregistration['referral_letter'] = '';
        }

        // if ($request->referral_letter) {
        //     $files = $request->file('referral_letter');
        //     $isUploaded = upload_file($files, 'PatientRegistration');
        //     $patientregistration['referral_letter'] =  $isUploaded->getData()->path;
        // } else {
        //     $patientregistration['referral_letter'] = '';
        // }

        PatientRegistration::where(
            ['id' => $request->id]
        )->update($patientregistration);
        return response()->json(["message" => "Patient Registration has updated successfully", "code" => 200]);
    }

    public function checkIFPatientExists($columnName, $columnValue, $id)
    {
        return (PatientRegistration::where([$columnName => $columnValue])->where('id', '!=', $id)->count() > 0) ? false : true;
    }
}
