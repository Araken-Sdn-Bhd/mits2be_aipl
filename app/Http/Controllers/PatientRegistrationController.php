<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use App\Models\ServiceRegister;
use App\Models\Notifications;
use App\Models\PatientAttachment;
use App\Models\TransactionLog;
use App\Models\AppointmentRequest;
use DateTime;
use DateTimeZone;
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
            'salutation_id' => '',
            'name_asin_nric' => 'required|string',
            'citizenship' => 'required|integer',
            'sex' => 'required|integer',
            'birth_date' => '',
            'age' => '',
            'mobile_no' => '',
            'house_no' => '',
            'services_type' => '',
            'referral_type' => '',
            'referral_letter' => 'max:10240',
            'address1' => '',
            'kin_name_asin_nric' => '',
            'kin_relationship_id' => '',
            'kin_mobile_no' => '',
            'kin_nric_no' => '',
            'kin_address1' => '',
            'drug_allergy' => '',
            'traditional_medication' => '',
            'other_allergy' => '',
            'employment_status' => '',
            'household_income' => '',
            'ethnic_group' => '',
            'patient_need_triage_screening' => '',
            'Sharp' => '',
            'branch_id' => '',
            'other_race' => '',
            'other_religion' => '',
            'other_accommodation' => '',
            'other_maritalList' => '',
            'other_feeExemptionStatus' => '',
            'other_occupationStatus' => '',


        ]);
        if ($request->Sharp) {
            $request->Sharp = "1";
        } else {
            $request->Sharp = "0";
        }
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $patientregistration = [
            'added_by' =>  $request->added_by,
            'branch_id' => $request->branch_id,
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
            'kin_nric_no' => $request->kin_nric_no,
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
            'patient_need_triage_screening' => $request->patient_need_triage_screening,
            'employment_status' => $request->employment_status,
            'household_income' => $request->household_income,
            'status' => "1",
            'sharp' => $request->Sharp, //1 represents for sharp registration patient list
            'other_race' => $request->other_race,
            'other_religion' => $request->other_religion,
            'other_accommodation' => $request->other_accommodation,
            'other_feeExemptionStatus' => $request->other_feeExemptionStatus,
            'other_occupationStatus' => $request->other_occupationStatus,
        ];


        $validateCitizenship = [];

        if ($request->citizentype == 'Malaysian') {
            $validateCitizenship['nric_type'] = 'required';
            $validateCitizenship['nric_no'] = 'required|unique:patient_registration';
            $patientregistration['nric_type'] =  $request->nric_type;
            $patientregistration['nric_no'] =  $request->nric_no;
        } else if ($request->citizentype == 'Permanent Resident') {
            $validateCitizenship['nric_no'] = 'required|unique:patient_registration';
            $patientregistration['nric_no'] =  $request->nric_no;
            $patientregistration['nric_no'] =  $request->nric_no1;
        } else if ($request->citizentype == 'Foreigner') {
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
        try {
            $Patient = PatientRegistration::firstOrCreate($patientregistration);
            if ($request->hasFile('referral_letter')) {
                $files = $request->file('referral_letter');
                $fileName = $files->getClientOriginalName();
                $isUploaded = upload_file($files, 'PatientRegistration');
                $filePath = $isUploaded->getData()->path;
                $patientregistration['referral_letter'] =  $filePath;
                $fileData = [
                    'added_by' =>  $request->added_by,
                    'patient_id' => $Patient['id'],
                    'file_name' => $fileName,
                    'uploaded_path' => $filePath,
                ];


                PatientAttachment::insert($fileData);
                PatientRegistration::updateOrCreate(['id' => $Patient['id']], ['referral_letter'  => $request->referral_letter]);
            } else {
                $patientRefLetter = [
                    'referral_letter'  => '',
                ];
                PatientRegistration::updateOrCreate(['id' => $Patient['id']], [$patientRefLetter]);
            }

            $MRN = $this->generateMRNString(10, $Patient['id']);
            PatientRegistration::where('id', $Patient['id'])->update(['patient_mrn' => $MRN]);
            $tran = [
                'patient_id' =>  $Patient['id'],
                'added_by' =>  $Patient['added_by'],
                'date' =>  date("Y-m-d h:i:s"),
                'time' =>  $Patient['created_at'],
                'activity' => "Patient Registration",
            ];
            $HOD = TransactionLog::insert($tran);
            $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
            if ($Patient['patient_need_triage_screening']) {
                $notifi = [
                    'added_by' => $Patient['added_by'],
                    'branch_id' => $request->branch_id,
                    'role' => 'Triage Personnel',
                    'patient_mrn' =>   $Patient['id'],
                    'url_route' => "/Modules/Intervention/patient-summary?id=" . $Patient['id'],
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'message' =>  'Request for patient screening',
                ];
                $HOD = Notifications::insert($notifi);
            }
            if($request->patient_request_id){
                AppointmentRequest::where('id',$request->patient_request_id)->update(['status'=>'0']);
            }
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

        $list = PatientRegistration::where('id', '=', $request->id)
            ->with('salutation:section_value,id')->with('typeic:code,id')
            ->with('gender:section_value,id')->with('maritialstatus:section_value,id')
            ->with('city:city_name,id')->with('kincity:city_name,id')
            ->with('race:section_value,id')->with('religion:section_value,id')
            ->with('occupation:section_value,id')
            ->with('fee:section_value,id')
            ->with('accomondation:section_value,id')
            ->with('citizenships:section_value,id')
            ->get();
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            $result[$key]['nric_no'] = $val['nric_no'] ?? 'NA';
            $result[$key]['passport_no'] = $val['passport_no'] ?? 'NA';
            if ($val['service'] != null) {
                $result[$key]['salutation'] = $val['salutation'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['salutation'] = 'NA';
            }

            if ($val['gender'] != null) {
                $result[$key]['gender'] = $val['gender'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['gender'] = 'NA';
            }
            if (!empty($val['maritialstatus'][0])) {
                $result[$key]['maritialstatus'] = $val['maritialstatus'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['maritialstatus'] = 'NA';
            }
            if ($val['citizenships'] != null) {
                $result[$key]['citizenships'] = $val['citizenships'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['citizenships'] = 'NA';
            }


            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            $result[$key]['birth_date'] = $val['birth_date'] ?? 'NA';
            $result[$key]['drug_allergy_description'] = $val['drug_allergy_description'] ?? 'NA';
        }
        return response()->json(["message" => "Patients List", 'list' => $list, "code" => 200]);
    }

    public function getPatientRegistrationByIdShortDetails(Request $request)
    {
        DB::enableQueryLog();
        $list = PatientRegistration::where('id', '=', $request->id)->with('salutation:section_value,id')
            ->with('gender:section_value,id')->with('maritialstatus:section_value,id')
            ->with('citizenships:section_value,id')->get();
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            $result[$key]['nric_no'] = $val['nric_no'] ?? 'NA';
            $result[$key]['passport_no'] = $val['passport_no'] ?? 'NA';
            if ($val['service'] != null) {
                $result[$key]['salutation'] = $val['salutation'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['salutation'] = 'NA';
            }

            if ($val['gender'] != null) {
                $result[$key]['gender'] = $val['gender'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['gender'] = 'NA';
            }
            if ($val['maritialstatus'] != null) {
                $result[$key]['maritialstatus'] = $val['maritialstatus'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['maritialstatus'] = 'NA';
            }
            if ($val['citizenships'] != null) {
                $result[$key]['citizenships'] = $val['citizenships'][0]['section_value'] ?? 'NA';
            } else {
                $result[$key]['citizenships'] = 'NA';
            }


            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            $result[$key]['mobile_no'] = $val['mobile_no'] ?? 'NA';
            $result[$key]['birth_date'] = $val['birth_date'] ?? 'NA';
            $result[$key]['drug_allergy_description'] = $val['drug_allergy_description'] ?? 'NA';
            $result[$key]['kin_name_asin_nric'] = $val['kin_name_asin_nric'] ?? 'NA';
            $result[$key]['kin_nric_no'] = $val['kin_nric_no'] ?? 'NA';
            $result[$key]['kin_mobile_no'] = $val['kin_mobile_no'] ?? 'NA';
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }

    public function getPatientRegistrationList()
    {
        $list = PatientRegistration::where('status', '=', '1')->where('sharp', '=', '0')
            ->with('salutation:section_value,id')->with('service:service_name,id')
            ->with('appointments', function ($query) {
                $query->where('appointment_status', '=', '1');
            })
            ->get()->toArray();
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            if ($val['nric_no'] != null) {
                $result[$key]['nric_id'] = $val['nric_no'];
            }
            if ($val['passport_no'] != null) {
                $result[$key]['nric_id'] = $val['passport_no'];
            }

            if ($val['nric_no'] == null && $val['passport_no'] == null) {
                $result[$key]['nric_id'] = 'NA';
            }
            $result[$key]['salutation'] = $val['salutation'][0]['section_value'] ?? 'NA';

            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            if ($val['appointments'] != null) {
                $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                $team_id = $val['appointments'][0]['assign_team'];
                $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                $result[$key]['team_name'] = $teamName[0]['team_name'];
            } else {
                $result[$key]['appointments'] = 'NA';
                $result[$key]['team_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }

    public function getPatientRegistrationListMobile()
    {
        db::enableQueryLog();
        $list = PatientRegistration::where('status', '=', '1')->where('sharp', '=', '0')
            ->with('salutation:section_value,id')->with('service:service_name,id')
            ->with('appointments', function ($query) {
                $query->where('appointment_status', '=', '1');
            })
            ->get()->toArray();

        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['section_value'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            $result[$key]['nric_no'] = $val['nric_no'] ?? 'NA';
            $result[$key]['passport_no'] = $val['passport_no'] ?? 'NA';
            $result[$key]['salutation'] = $val['salutation'][0]['section_value'] ?? 'NA';
            $result[$key]['branch_id'] = $val['branch_id'];

            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            if ($val['appointments'] != null) {

                $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                $team_id = $val['appointments'][0]['assign_team'];
                $teamName = ServiceRegister::where('id', $team_id)->get();
                $result[$key]['team_name'] = $teamName[0]['service_name'];
            } else {
                $result[$key]['appointments'] = 'NA';
                $result[$key]['team_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }

    public function getPatientRegistrationListByScreening(Request $request)
    {
        $role = DB::table('staff_management')
            ->select('roles.code')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->where('staff_management.email', '=', $request->email)
            ->first();

        if ($role->code == 'superadmin') {
            $list = PatientRegistration::where('status', '=', '1')->where('patient_need_triage_screening', '=', '1')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        } else {
            $list = PatientRegistration::where('status', '=', '1')->where('branch_id', $request->branch_id)->where('patient_need_triage_screening', '=', '1')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        }
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            $result[$key]['nric_no'] = $val['nric_no'] ?? 'NA';
            $result[$key]['passport_no'] = $val['passport_no'] ?? 'NA';

            if ($val['nric_no'] != null) {
                $result[$key]['nric_id'] = $val['nric_no'];
            }
            if ($val['passport_no'] != null) {
                $result[$key]['nric_id'] = $val['passport_no'];
            }

            if ($val['nric_no'] == null && $val['passport_no'] == null) {
                $result[$key]['nric_id'] = 'NA';
            }
            if ($val['salutation'] != null) {
                $result[$key]['salutation'] = $val['salutation'][0]['section_value'];
            } else {
                $result[$key]['salutation'] = 'NA';
            }
            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }


            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            if ($val['appointments'] != null) {
                $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                $team_id = $val['appointments'][0]['assign_team'];
                $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                $result[$key]['team_name'] = $teamName[0]['team_name'];
            } else {
                $result[$key]['appointments'] = 'NA';
                $result[$key]['team_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }


    public function updatePatientRegistration(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'salutation_id' => '',
            'name_asin_nric' => 'required|string',
            'citizenship' => 'required|integer',
            'sex' => 'required|integer',
            'birth_date' => '',
            'age' => 'integer',
            'mobile_no' => '',
            'house_no' => '',
            'services_type' => '',
            'referral_type' => '',
            'referral_letter' => 'max:10240',
            'address1' => '',
            'kin_name_asin_nric' => '',
            'kin_relationship_id' => '',
            'kin_mobile_no' => '',
            'kin_address1' => '',
            'drug_allergy' => '',
            'traditional_medication' => '',
            'other_allergy' => '',
            'id' => 'required',
            'branch_id' =>'',
            'other_race' => '',
            'other_religion' => '',
            'other_accommodation' => '',
            'other_maritalList' => '',
            'other_feeExemptionStatus' => '',
            'other_occupationStatus' => '',

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
            'status' => "1",
            'updated_at' =>  $request->update_at,
            'branch_id' => $request->branch_id,
            'other_race' => $request->other_race,
            'other_religion' => $request->other_religion,
            'other_accommodation' => $request->other_accommodation,
            'other_feeExemptionStatus' => $request->other_feeExemptionStatus,
            'other_occupationStatus' => $request->other_occupationStatus,

        ];

        $validateCitizenship = [];

        if ($request->citizentype == 'Malaysian') {
            $validateCitizenship['nric_type'] = 'required';
            $patientregistration['nric_type'] =  $request->nric_type;
            $patientregistration['nric_no'] =  $request->nric_no;
            if (!$this->checkIFPatientExists('nric_no', $request->nric_no, $request->id)) {
                return response()->json(["message" => "Patient NRIC NO already exists", "code" => 422]);
            }
        } else if ($request->citizentype == 'Permanent Resident') {
            $patientregistration['nric_no'] =  $request->nric_no1;
            if (!$this->checkIFPatientExists('nric_no', $request->nric_no1, $request->id)) {
                return response()->json(["message" => "Patient NRIC NO already exists", "code" => 422]);
            }
        } else if ($request->citizentype == 'Foreigner') {
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


        PatientRegistration::where(
            ['id' => $request->id]
        )->update($patientregistration);
        $tran = [
            'patient_id' =>  $request->id,
            'added_by' =>  $request->added_by,
            'date' =>  date('Y-m-d'),
            'time' =>  date('h:i:s'),
            'created_at' =>  date('Y-m-d h:i:s'),
            'activity' => "Update Patient Demographic",
        ];
        $HOD = TransactionLog::insert($tran);
        return response()->json(["message" => "Patient Registration has updated successfully", "Result" => $HOD, "code" => 200]);
    }
    public function validatePatientNric(Request $request)
    {
        $runByIC = PatientRegistration::where('nric_no', $request->ic)->count();

        if ($runByIC != 0) {
            return response()->json(["message" => "Patient NRIC NO already exists", "code" => 422]);
        } else {
            return response()->json(["message" => "New Patient", "code" => 200]);
        }
    }

    public function checkIFPatientExists($columnName, $columnValue, $id)
    {
        return (PatientRegistration::where([$columnName => $columnValue])->where('id', '!=', $id)->count() > 0) ? false : true;
    }

    public function getTransactionlog(Request $request)
    {
        $list = DB::table('transaction_log')
            ->leftjoin('users', 'transaction_log.added_by', '=', 'users.id')
            ->select(
                DB::raw("DATE_FORMAT(transaction_log.date, '%d-%m-%Y') as date"),
                'transaction_log.activity',
                'users.name',

                DB::raw("DATE_FORMAT(transaction_log.time, '%h:%i %p') as time")
            )
            ->where('transaction_log.patient_id', '=', $request->patient_id)
            ->get();
        if (count($list) > 0) {
            return response()->json(["message" => "Transaction Log List", 'list' => $list, "code" => 200]);
        } else {
            return response()->json(["message" => "No Data Found",  "code" => 400]);
        }
    }

    public function demographic_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'salutation_id' => '',
            'name_asin_nric' => 'required|string',
            'citizenship' => 'required|integer',
            'sex' => 'required|integer',
            'birth_date' => '',
            'age' => '',
            'mobile_no' => 'required',
            'house_no' => '',
            'branch_id' => '',
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
            'race_id' =>  $request->race_id,
            'religion_id' =>  $request->religion_id,
            'marital_id' =>  $request->marital_id,
            'education_level' => $request->education_level,
            'branch_id' =>  $request->branch_id,
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
            $tran = [
                'patient_id' =>  $Patient['id'],
                'added_by' =>  $Patient['added_by'],
                'date' =>  date("Y-m-d h:i:s"),
                'time' =>  $Patient['created_at'],
                'activity' => "Patient Registration",
            ];
            $HOD = TransactionLog::insert($tran);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Patient Registration' => $patientregistration, "code" => 200]);
        }
        return response()->json(["message" => "Patient Registration has been done successfully!", "code" => 200]);
    }

    public function getPatientRegistrationListbyBranch(Request $request)
    {
        if ($request->branch_id != 0) {
            $list = PatientRegistration::where('status', '=', '1')
                ->where('sharp', '=', '0')
                ->where('branch_id', $request->branch_id)
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        } else if ($request->branch_id == 0) {
            $list = PatientRegistration::where('status', '=', '1')
                ->where('sharp', '=', '0')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        }
        $result = [];
        foreach ($list as $key => $val) {
            $result[$key]['patient_mrn'] = $val['patient_mrn'] ?? 'NA';
            $result[$key]['name_asin_nric'] = $val['name_asin_nric'] ?? 'NA';
            $result[$key]['id'] = $val['id'];
            $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y ?? 'NA';
            if ($val['nric_no'] != null) {
                $result[$key]['nric_id'] = $val['nric_no'];
            }
            if ($val['passport_no'] != null) {
                $result[$key]['nric_id'] = $val['passport_no'];
            }

            if ($val['nric_no'] == null && $val['passport_no'] == null) {
                $result[$key]['nric_id'] = 'NA';
            }
            $result[$key]['salutation'] = $val['salutation'][0]['section_value'] ?? 'NA';

            if ($val['service'] != null) {
                $result[$key]['service'] = $val['service']['service_name'];
            } else {
                $result[$key]['service'] = 'NA';
            }
            //dd($val['appointments'] != null);
            if ($val['appointments'] != null) {
                $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                $team_id = $val['appointments'][0]['assign_team'];
                $teamName = ServiceRegister::where('id', $team_id)->get();
                $result[$key]['team_name'] = $teamName[0]['service_name'];
            } else {
                $result[$key]['appointments'] = 'NA';
                $result[$key]['team_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }


}
