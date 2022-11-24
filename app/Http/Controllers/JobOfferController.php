<?php

namespace App\Http\Controllers;

use App\Models\AttemptTest;
use Illuminate\Http\Request;
use App\Models\JobOffers;
use App\Models\JobCompanies;
use App\Models\HospitalBranchManagement;
use App\Models\PatientRegistration;
use App\Models\SEConsentForm;
use App\Models\CPSReferralForm;
use App\Models\LASERAssesmenForm;
use App\Models\PatientCarePaln;
use App\Models\User;
use App\Models\CpsHomevisitConsentForm;
use App\Models\PhotographyConsentForm;
use App\Models\EtpConsentForm;
use App\Models\JobClubConsentForm;
use App\Models\CpsHomevisitWithdrawalForm;
use App\Models\HospitalBranchTeamManagement;
use App\Models\JobStartForm;
use App\Models\JobEndReport;
use App\Models\JobTransitionReport;
use App\Models\HospitalManagement;
use App\Models\JobStartFormList;
use App\Models\Notifications;
use App\Models\TestResult;
use DateTime;
use DateTimeZone;
use Validator;
use DB;
use Exception;
use Illuminate\Support\Facades\DB as FacadesDB;

class JobOfferController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'added_by' => 'required|integer',
            'company_id' => 'required|integer',
            'position_offered' => 'required|string',
            'position_location_1' => 'required|string',
            'education_id' => 'required|integer',
            'duration_of_employment' => 'required|integer',
            'salary_offered' => 'required|string',
            'work_schedule' => 'required|string',
            'is_transport' => 'required|integer',
            'is_accommodation' => 'required|integer',
            'work_requirement' => 'required|json',
            'branch_id' => 'required|integer',
            'job_availability' => 'required|integer',
            'position_location_2' => '',
            'position_location_3' => '',
            'id' => '',
            'appointment_details_id' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $job = [
            'added_by' => $request->added_by,
            'company_id' => $request->company_id,
            'position_offered' => $request->position_offered,
            'position_location_1' => $request->position_location_1,
            'position_location_2' => $request->position_location_2,
            'position_location_3' => $request->position_location_3,
            'education_id' => $request->education_id,
            'duration_of_employment' => $request->duration_of_employment,
            'salary_offered' => $request->salary_offered,
            'work_schedule' => $request->work_schedule,
            'is_transport' => $request->is_transport,
            'is_accommodation' => $request->is_accommodation,
            'work_requirement' => $request->work_requirement,
            'branch_id' => $request->branch_id,
            'job_availability' => $request->job_availability,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s'),
        ];
        if ($request->type == 'add') {
            try {
                JobOffers::create($job);
                return response()->json(["message" => "Job Created", "result" => $job, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
        } else if ($request->type == 'update') {
            try {
                // $job['status'] = $request->status;
                JobOffers::where('id', $request->job_id)->update($job);
                return response()->json(["message" => "Job updated", "result" => $job, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Action is not allowed!", "code" => 422]);
        }
    }

    public function addJob(Request $request)
    {

        $data = JobOffers::where('added_by', $request->user_id)->where('position_offered', $request->job_id)->first();
        $job = [
            'added_by' => $data->added_by,
            'company_id' => $data->company_id,
            'position_offered' => $data->position_offered,
            'position_location_1' => $data->position_location_1,
            'position_location_2' => $data->position_location_2,
            'position_location_3' => $data->position_location_3,
            'education_id' => $data->education_id,
            'duration_of_employment' => $data->duration_of_employment,
            'salary_offered' => $data->salary_offered,
            'work_schedule' => $data->work_schedule,
            'is_transport' => $data->is_transport,
            'is_accommodation' => $data->is_accommodation,
            'work_requirement' => $data->work_requirement,
            'branch_id' => $data->branch_id,
            'job_availability' => "1",
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
        try {
            JobOffers::create($job);
            return response()->json(["message" => "Job Created", "result" => $job, "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 200]);
        }
    }
    public function jobList(Request $request)
    {
        DB::enableQueryLog();
        // JobOffers::select(DB::raw("count('id') as job_posted"), 'id', 'position_offered')->where('added_by', $request->added_by)->groupBy('position_offered', 'id')->get();
        $result = DB::table('job_offers as A')
        ->select('A.id', 'A.position_offered',DB::raw("DATE_FORMAT(A.created_at, '%d-%M-%y') as job_posted"))
        ->where('A.added_by', $request->added_by)
        ->groupBy('A.id','A.position_offered','A.created_at')
        ->get();
        // dd(DB::getQueryLog());
        return $result;
    }

    public function jobListById(Request $request)
    {
        return JobOffers::select('*', 'position_offered')->where('added_by', $request->added_by)->where('id', $request->id)->get();
    }

    public function jobApproveOrReject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
 //1->pending  0->rejected 2->Approved
        JobOffers::where(
            ['id' => $request->id]
        )->update([
            'status' =>  $request->status,
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Job Updated Successfully", "code" => 200]);
    }


    public function JobRequestList(Request $request)
    {
        $result = [];
        $list = JobOffers::select(DB::raw("count('id') as job_posted"), DB::raw("COUNT(CASE WHEN status = '0' THEN 1 END) NewJobs"), 'company_id')->groupBy('company_id')->get()->toArray();
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $result[$k] = $v;
                $company = JobCompanies::where('id', $v['company_id'])->get();
                if (count($company) > 0)
                    $result[$k]['company_name'] = $company[0]['company_name'];
                else
                    $result[$k]['company_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Job Request List!", "list" => $result, "code" => 200]);
    }
    public function getListByTitle(Request $request)
    {
        $users = DB::table('job_offers')
            ->join('hospital_branch__details', 'job_offers.branch_id', '=', 'hospital_branch__details.id')
            ->select('*', DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as posted_at"))->where(['job_offers.added_by' => $request->added_by, 'job_offers.position_offered' => $request->title])->get();
        return response()->json(["message" => "Job List", 'list' => $users, "code" => 200]);
    }

    public function getCompanyJobApprovalList(Request $request)
    {
        $users = DB::table('job_offers')
            ->join('hospital_branch__details', 'job_offers.branch_id', '=', 'hospital_branch__details.id')
            ->join('job_companies', 'job_offers.company_id', '=', 'job_companies.id')
            ->select('*', 'hospital_branch__details.id as idbranch', 'job_offers.id as id', 'job_offers.status as status', DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as posted_at"))->where(['job_offers.added_by' => $request->added_by, 'job_offers.company_id' => $request->company_id])
            ->where('job_offers.status',"=",'1')
            ->get();
        $company = DB::table('job_companies')->select('company_name')->where('id', $request->company_id)->first();
        return response()->json(["message" => "Job List", 'list' => $users, "companyname" => $company, "code" => 200]);
    }

    public function setStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        JobOffers::where('id', $request->id)->update(['approval_status' => $request->status, 'approve_by' => $request->added_by]);
        return response()->json(["message" => "Job status updated!", "code" => 200]);
    }
    public function CompaniesJobs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $result = [];
        $list = JobOffers::select('id', 'position_offered', 'duration_of_employment', 'position_location_1', 'salary_offered', 'work_schedule', 'is_transport', 'is_accommodation', 'work_requirement', 'branch_id', 'status', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as posted_at"), 'company_id')->where('company_id', $request->company_id)->get()->toArray();
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $result[$k] = $v;
                $company = HospitalBranchManagement::where('id', $v['branch_id'])->get();
                if (count($company) > 0)
                    $result[$k]['hospital_branch_name'] = $company[0]['hospital_branch_name'];
                else
                    $result[$k]['hospital_branch_name'] = 'NA';
            }
        }
        $comp = JobCompanies::where('id', $v['company_id'])->get();
        $company = [];
        if (count($comp) > 0)
            $company['name'] = $comp[0]['company_name'];
        else
            $company['name']  = 'NA';

        $company['id']  = $request->company_id;
        return response()->json(["message" => "Job Request List!", "list" => $result, "companyName" => $company, "code" => 200]);
    }
    public function CompaniesJobsSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|integer',
            'position' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $result = [];
        $list = JobOffers::select('id', 'position_offered', 'duration_of_employment', 'position_location_1', 'salary_offered', 'work_schedule', 'is_transport', 'is_accommodation', 'work_requirement', 'branch_id', 'status', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as posted_at"), 'company_id')
            ->where(['company_id' => $request->company_id, 'position_offered' => $request->position])->get()->toArray();
        if (count($list) > 0) {
            foreach ($list as $k => $v) {
                $result[$k] = $v;
                $company = HospitalBranchManagement::where('id', $v['branch_id'])->get();
                if (count($company) > 0)
                    $result[$k]['hospital_branch_name'] = $company[0]['hospital_branch_name'];
                else
                    $result[$k]['hospital_branch_name'] = 'NA';
            }
        }
        return response()->json(["message" => "Job Request Search List!", "list" => $result,  "code" => 200]);
    }

    public function jobRecordList()
    {
        $result = [];
        $list = JobOffers::select('id', 'position_offered', 'duration_of_employment', 'position_location_1', 'salary_offered', 'work_schedule', 'company_id', 'job_availability')
        ->where("status","!=","0")
        ->get()->toArray();
        if (count($list) > 0) {
            foreach ($list as $k => $v) {

                if ($v['job_availability'] == '1') {
                    $v['job_availability'] = 'Available';
                } else {
                    $v['job_availability'] = 'Not Available';
                }

                // if ($v['work_schedule'] == '1') {
                //     $v['work_schedule'] = 'Part Time';
                // } else if ($v['work_schedule'] == '2') {
                //     $v['work_schedule'] = 'Full Time';
                // } else {
                //     $v['work_schedule'] = 'Part & Full Time';
                // }
                $result[$k] = $v;
                $company = JobCompanies::where('id', $v['company_id'])->get();
                if (count($company) > 0) {
                    $result[$k]['company_name'] = $company[0]['company_name'];
                    $result[$k]['contact_name'] = $company[0]['contact_name'];
                    $result[$k]['contact_number'] = $company[0]['contact_number'];
                } else {
                    $result[$k]['company_name'] = 'NA';
                    $result[$k]['contact_name'] = 'NA';
                    $result[$k]['contact_number'] = 'NA';
                }
            }
        }
        return response()->json(["message" => "Job Request Search List!", "list" => $result,  "code" => 200]);
    }

    public function jobRecordSearchList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'position' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $result = [];
        $list = JobOffers::select('id', 'position_offered', 'duration_of_employment', 'position_location_1', 'salary_offered', 'work_schedule', 'company_id', 'job_availability')
            ->where('position_offered', $request->position)->get()->toArray();
        if (count($list) > 0) {
            foreach ($list as $k => $v) {

                if ($v['job_availability'] == '1') {
                    $v['job_availability'] = 'Available';
                } else {
                    $v['job_availability'] = 'Not Available';
                }

                if ($v['work_schedule'] == '1') {
                    $v['work_schedule'] = 'Part Time';
                } else if ($v['work_schedule'] == '2') {
                    $v['work_schedule'] = 'Full Time';
                } else {
                    $v['work_schedule'] = 'Part & Full Time';
                }
                $result[$k] = $v;
                $company = JobCompanies::where('id', $v['company_id'])->get();
                if (count($company) > 0) {
                    $result[$k]['company_name'] = $company[0]['company_name'];
                    $result[$k]['contact_name'] = $company[0]['contact_name'];
                    $result[$k]['contact_number'] = $company[0]['contact_number'];
                } else {
                    $result[$k]['company_name'] = 'NA';
                    $result[$k]['contact_name'] = 'NA';
                    $result[$k]['contact_number'] = 'NA';
                }
            }
        }
        return response()->json(["message" => "Job Request Search List!", "list" => $result,  "code" => 200]);
    }

    public function getSEForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no')->where('id', $patient_id)->get();
        $hospital = HospitalManagement::select('hospital_name')->where('added_by', $request->added_by)->get();
        $hospitalbranch = HospitalBranchManagement::select('hospital_branch_name')->where('added_by', $request->added_by)->get();
        // dd($hospitalbranch[0]['hospital_branch_name']);
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
         if(!empty($hospital[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => $hospital[0]['hospital_name'],
                'hospital_branch_name' => $hospitalbranch[0]['hospital_branch_name'] ?? 'NA'
            ];
            // return response()->json(["message" => "SE Consent Form", "list" => $response,  "code" => 200]);
        }else if(!empty($hospitalbranch[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => $hospital[0]['hospital_name'] ?? 'NA',
                'hospital_branch_name' => $hospitalbranch[0]['hospital_branch_name']
            ];
            return response()->json(["message" => "SE Consent Form", "list" => $response,  "code" => 200]);
        }else if(!empty($hospital[0]) && !empty($hospitalbranch[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => $hospital[0]['hospital_name'],
                'hospital_branch_name' => $hospitalbranch[0]['hospital_branch_name']
            ];
            return response()->json(["message" => "SE Consent Form", "list" => $response,  "code" => 200]);
        }else{
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => "NA",
                'hospital_branch_name' => "NA"
            ];
            return response()->json(["message" => "SE Consent Form", "list" => $response,  "code" => 200]);
        }
        return response()->json(["message" => "SE Consent Form", "list" => $response,  "code" => 200]);

    }

    public function setSEConsentForm(Request $request)
    {
        if($request->id){
            SEConsentForm::where(['id' => $request->id])->update([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'consent_for_participation' => $request->consent_for_participation,
                'consent_for_disclosure' => (string) $request->consent_for_disclosure,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
        SEConsentForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'consent_for_participation' => $request->consent_for_participation,
            'consent_for_disclosure' => (string) $request->consent_for_disclosure,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }

    public function setCPSReferralForm(Request $request)
    {
        CPSReferralForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'treatment_needs_individual' => $request->treatment_needs_individual,
            'treatment_needs_medication' => $request->treatment_needs_medication,
            'treatment_needs_support' => $request->treatment_needs_support,
            'location_of_service' => $request->location_of_service,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_des' => $request->medication,
            'medication_referrer_name' => $request->medication_referrer_name,
            'medication_referrer_designation' => $request->medication_referrer_designation,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }

    public function setLASERReferralForm(Request $request)
    {

        $result = json_decode($request->result, true);
        // dd($result);
        $addTestResult = [];
        $level = [];
        if (count($result) > 0) {
            $i = 0;
            $whoDasTotal = 0;
            foreach ($result as $key => $val) {
                foreach ($val as $kk => $vv) {
                    //  $TestResult[$request->test_name][$kk] =  $this->prepareResult($vv, $request->test_name);
                    if (
                        $request->test_name == 'laser'
                    ) {
                        $testResult[$i] =
                            [
                                'added_by' =>  $request->added_by,
                                'patient_id' =>  $request->patient_id,
                                'test_name' =>  $request->test_name,
                                'ip_address' => $request->user_ip_address,
                                'created_at' =>  date('Y-m-d H:i:s'),
                                'updated_at' =>  date('Y-m-d H:i:s'),
                                'test_section_name' => $kk,
                                'result' => $this->prepareLaserResult($vv),
                                'appointment_details_id' => $request->appId,
                            ];
                        // if ($request->test_name != 'bdi' && $request->test_name != 'bai' && $request->test_name != 'atq' && $request->test_name != 'psp' && $request->test_name != 'si') {
                            if ($request->test_name == 'laser') {
                                $level[$kk] =  ['score' => $this->prepareLaserResult($vv)];
                            }else if($request->test_name == 'contemplation'){
                                $level[$kk] =  ['score' => $this->prepareLaserResult($vv)];
                            } else {
                                $level[$kk] = $this->prepareLaserResult($vv);
                            }
                        // }

                    }
                    // else if ($request->test_name == 'dass') {
                    //     $testResult = $this->prepareDASSResult($vv, $request);
                    //     $level = $this->getDassLevel($testResult);
                    // }

                    foreach ($vv as $k => $v) {
                        $addTestResult[$i] = [
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_id,
                            'test_name' =>  $request->test_name,
                            'test_section_name' => $kk,
                            'question_id' =>  $k,
                            'answer_id' => $v,
                            'user_ip_address' => $request->user_ip_address,
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' =>  date('Y-m-d H:i:s'),
                            'appointment_details_id' => $request->appId,
                        ];
                        $i++;
                    }
                }
            }
            //  dd($testResult);
            // try {
            //     AttemptTest::insert($addTestResult);
            //     TestResult::insert($testResult);
            //     return response()->json(["message" => "Answer submitted", "result" => $level, "code" => 200]);
            // } catch (Exception $e) {
            //     return response()->json(["message" => $e->getMessage(), 'Exception' => $addTestResult, "code" => 200]);
            // }
        }
        $laserreferral=[
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'pre_contemplation' => $request->pre_contemplation,
            'contemplation' => $request->contemplation,
            'action' => $request->action,
            'location_of_service' => $request->location_of_service,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_prescription' => $request->medication_prescription,
            'created_at' => date('Y-m-d H:i:s'),
            'appointment_details_id' => $request->appId,
        ];
        if($request->id){
            LASERAssesmenForm::where(['id' => $request->id])->update($laserreferral);
            // RehabDischargeNote::firstOrCreate($rehabdischarge);
            return response()->json(["message" => "Updated", "code" => 200]);
         }else{
            LASERAssesmenForm::create($laserreferral);
            try {
                AttemptTest::insert($addTestResult);
                TestResult::insert($testResult);
                return response()->json(["message" => "Answer submitted", "result" => $level, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Exception' => $addTestResult, "code" => 200]);
            }
        //  return response()->json(["message" => "Created", "code" => 200]);
         }
    }

    public function prepareLaserResult($resultSet)
    {
        $result = 0;
        $values = ['1' => 1, '2' => 2, '3'=>3,'4'=>4,'5'=>5];
        $revValues = ['5' => 100, '4' => 75, '3'=>35,'2'=>25,'1'=>0];
        $i = 1;
        foreach ($resultSet as $k => $v) {
            if($i<7)
            $result += $values[$v];
            else
            $result += $revValues[$v];

            $i++;
        }
        return $result;
    }

    public function setPatientCarePlan(Request $request)
    {
        $patientcarepln=[
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'plan_date' => $request->plan_date,
            'reason_of_review' => $request->reason_of_review,
            'diagnosis' => $request->diagnosis,
            'medication_oral' => $request->medication_oral,
            'medication_depot' => $request->medication_depot,
            'medication_im' => $request->medication_im,
            'background_history' => $request->background_history,
            'staff_incharge_dr' => $request->staff_incharge_dr,
            'treatment_plan' => $request->treatment_plan,
            'next_review_date' => $request->next_review_date,
            'case_manager_date' => $request->case_manager_date,
            'case_manager_name' => $request->case_manager_name,
            'case_manager_designation' => $request->case_manager_designation,
            'specialist_incharge_date' => $request->specialist_incharge_date,
            'specialist_incharge_name' => $request->specialist_incharge_name,
            'specialist_incharge_designation' => $request->specialist_incharge_designation,
            'location_of_service' => $request->location_of_service,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_prescription' => $request->medication_prescription,
            'created_at' => date('Y-m-d H:i:s'),
            'appointment_details_id' => $request->appId,
        ];
        // PatientCarePaln::create()
        if($request->id){
            PatientCarePaln::where(['id' => $request->id])->update($patientcarepln);
            // RehabDischargeNote::firstOrCreate($rehabdischarge);
            return response()->json(["message" => "Updated", "code" => 200]);
         }else{
            $HOD=PatientCarePaln::create($patientcarepln);
            $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
            $notifi=[
                'added_by' => $HOD['added_by'],
                'patient_mrn' =>   $HOD['patient_id'],
                'created_at' => $date->format('Y-m-d H:i:s'),    //$date->format
                'message' =>  'upcoming review for Patient Care Plan',
            ];
            $HOD1 = Notifications::insert($notifi);
         return response()->json(["message" => "Created", "code" => 200]);
         }

    }

    public function dischareCategory()
    {
        // $arr = ['1' => 'At Own Risk', '2' => 'Death', '3' => 'Discharged Well', '4' => 'Technical Discharge', '5' => 'Transfer'];
        // $arr = ['1' => 'At Own Risk', '2' => 'Death', '3' => 'Discharged Well', '4' => 'Technical Discharge', '5' => 'Transfer'];
        $arr = array(
            array('id' => '1','name' => 'At Own Risk'),
            array('id' => '2','name' => 'Death'),
            array('id' => '3','name' => 'Discharged Well'),
            array('id' => '4','name' => 'Technical Discharge'),
            array('id' => '5','name' => 'Transfer'));
        return response()->json(["message" => "List", 'list' => $arr, "code" => 200]);
    }
    public function screeningTypes()
    {
        // $arr = ['1' => 'CBI', '2' => 'DASS', '3' => 'PHQ9', '4' => 'WHODAS'];
        $arr = array(
            array('id' => '1','name' => 'CBI'),
            array('id' => '2','name' => 'DASS'),
            array('id' => '3','name' => 'PHQ9'),
            array('id' => '4','name' => 'WHODAS'));
        return response()->json(["message" => "List", 'list' => $arr, "code" => 200]);
    }
    public function setCpsHomevisitConsentForm(Request $request)
    {
        if($request->id){
            CpsHomevisitConsentForm::where(['id' => $request->id])->update([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'consent_for_homevisit' => $request->consent_for_homevisit,
                'consent_for_hereby_already_give_explanation' =>(string) $request->consent_for_hereby_already_give_explanation,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Created", "code" => 200]);
        }else{
        CpsHomevisitConsentForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'consent_for_homevisit' => $request->consent_for_homevisit,
            'consent_for_hereby_already_give_explanation' =>(string) $request->consent_for_hereby_already_give_explanation,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }

    public function getCpsHomevisitForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no','kin_name_asin_nric','kin_nric_no')->where('id', $patient_id)->get();
        $hospital = HospitalManagement::select('hospital_name','hospital_adrress_1','hospital_adrress_2','hospital_adrress_3','hospital_state','hospital_city','hospital_postcode','state.state_name as state_name','postcode.city_name as city_name','postcode.postcode as postcode')
        ->leftJoin('state', 'state.id', '=', 'hospital_management.hospital_state')
        ->leftJoin('postcode', 'postcode.id', '=', 'hospital_management.hospital_postcode')
        ->where('hospital_management.added_by', $request->added_by)
        ->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        if(!empty($hospital[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name1' => $user[0]['name'],
                'user_name' => $patient[0]['kin_name_asin_nric'] ?? 'NA',
                'guardian_nric' =>  $patient[0]['kin_nric_no'] ?? 'NA',
                'designation' => $user[0]['name'],
                'hospital_name' => $hospital[0]['hospital_name'],
                'hospital_adrress_1' => $hospital[0]['hospital_adrress_1'],
                'hospital_adrress_2' => $hospital[0]['hospital_adrress_2'],
                'hospital_adrress_3' => $hospital[0]['hospital_adrress_3'],
                'hospital_adrress_3' => $hospital[0]['hospital_adrress_3'],
                'city_name' => $hospital[0]['city_name'],
                'postcode' => $hospital[0]['postcode'],
                'state_name' => $hospital[0]['state_name'],
            ];
            return response()->json(["message" => "CPS HomeVisit Consent Form", "list" => $response,  "code" => 200]);
        }
        else{
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name1' => $user[0]['name'],
                'designation' => $user[0]['name'],
                'hospital_name' => "HOSPITAL [NA]",
                'user_name' => $patient[0]['kin_name_asin_nric']  ?? 'NA',
                'guardian_nric' =>  $patient[0]['kin_nric_no'] ?? 'NA',
            ];
            return response()->json(["message" => "CPS HomeVisit Consent Form", "list" => $response,  "code" => 200]);
        }

    }

    public function getJobClubForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no')->where('id', $patient_id)->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        $response = [
            'patient_name' => $patient[0]['name_asin_nric'],
            'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
            'date' => date('d/m/Y'),
            'user_name' => $user[0]['name'],
            'designation' => $user[0]['role']
        ];
        return response()->json(["message" => "Job Club Consent Form", "list" => $response,  "code" => 200]);
    }

    public function setJobClubConsentForm(Request $request)
    {
        if($request->id){
            JobClubConsentForm::where(['id' =>$request->id])->update([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'consent_for_participation' => $request->consent_for_participation,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Update", "code" => 200]);
        }else{
        JobClubConsentForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'consent_for_participation' => $request->consent_for_participation,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }

    public function getEtpForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no')->where('id', $patient_id)->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        $response = [
            'patient_name' => $patient[0]['name_asin_nric'],
            'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
            'date' => date('d/m/Y'),
            'user_name' => $user[0]['name'],
            'designation' => $user[0]['role']
        ];
        return response()->json(["message" => "ETP Consent Form", "list" => $response,  "code" => 200]);
    }

    public function setEtpConsentForm(Request $request)
    {
        if($request->id){
            EtpConsentForm::where(['id' =>$request->id])->update([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'consent_for_participation' => $request->consent_for_participation,
                'consent_for_disclosure' =>(string) $request->consent_for_disclosure,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
        EtpConsentForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'consent_for_participation' => $request->consent_for_participation,
            'consent_for_disclosure' =>(string) $request->consent_for_disclosure,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }

    public function setCpsHomevisitWithdrawalForm(Request $request)
    {
        if($request->id){
            CpsHomevisitWithdrawalForm::where(['id' => $request->id])->update([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'community_psychiatry_services' => $request->community_psychiatry_services,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
        CpsHomevisitWithdrawalForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'community_psychiatry_services' => $request->community_psychiatry_services,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }

    public function getCpsHomevisitWithdrawalForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no')->where('id', $patient_id)->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        $response = [
            'patient_name' => $patient[0]['name_asin_nric'],
            'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
            'date' => date('d/m/Y'),
            'user_name' => $user[0]['name'],
            'designation' => $user[0]['role']
        ];
        return response()->json(["message" => "CPS HomeVisit Withdrawal Form", "list" => $response,  "code" => 200]);
    }

    public function getPhotographyForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no','kin_name_asin_nric','kin_nric_no')->where('id', $patient_id)->get();
        $hospital = HospitalManagement::select('hospital_name')->where('added_by', $request->added_by)->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        if(!empty($hospital[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => $hospital[0]['hospital_name'] ?? 'NA',
                'guardian' => $patient[0]['kin_name_asin_nric'] ?? "NA",
                'nric_guardian' => $patient[0]['kin_nric_no'] ?? "NA",
                'date_guardian' => date('d/m/Y') ?? "NA",
                'witness_name' =>  "NA",
                'designation_guardian' =>  "NA",
            ];
            return response()->json(["message" => "Photography Consent Form", "list" => $response,  "code" => 200]);
        }else{
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name' => $user[0]['name'],
                'designation' => $user[0]['role'],
                'hospital_name' => $hospital[0]['hospital_name'] ?? 'NA',
                'guardian' => $patient[0]['kin_name_asin_nric'] ?? "NA",
                'nric_guardian' => $patient[0]['kin_nric_no'] ?? "NA",
                'date_guardian' => date('d/m/Y') ?? "NA",
                'witness_name' =>  "NA",
                'designation_guardian' =>  "NA",
            ];
            return response()->json(["message" => "Photography Consent Form", "list" => $response,  "code" => 200]);
        }

    }

    public function setPhotographyConsentForm(Request $request)
    {
        if($request->id){
            PhotographyConsentForm::where(['id' => $request->id])->create([
                'patient_id' => $request->patient_id,
                'added_by' => $request->added_by,
                'photography_consent_form_agree' => $request->photography_consent_form_agree,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(["message" => "Created", "code" => 200]);
        }else{
        PhotographyConsentForm::create([
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'photography_consent_form_agree' => $request->photography_consent_form_agree,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["message" => "Created", "code" => 200]);
    }
    }
    public function setJobStartForm(Request $request)
    {
        $jobstart=[
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'client' => $request->client,
            'employment_specialist' => $request->employment_specialist,
            'case_manager' => $request->case_manager,
            'first_date_of_work' => $request->first_date_of_work,
            'job_title' => $request->job_title,
            'duties_field' => $request->duties_field,
            'rate_of_pay' => $request->rate_of_pay,
            'benefits_field' => $request->benefits_field,
            'work_schedule' => $request->work_schedule,
            'disclosure' => $request->disclosure,
            'name_of_employer' => $request->name_of_employer,
            'name_of_superviser' => $request->name_of_superviser,
            'address' => $request->address,
            'location_of_service' => $request->location_of_service,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_prescription' => $request->medication_prescription,
            'created_at' => date('Y-m-d H:i:s'),
            'appointment_details_id' => $request->appId,
        ];
        if($request->id){
            JobStartForm::where(['id' => $request->id])->update($jobstart);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
        JobStartForm::create($jobstart);
        return response()->json(["message" => "Created", "code" => 200]);
        }
    }

    public function setJobEndReport(Request $request)
    {
        $jobend=[
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'name' => $request->name,
            'job_title' => $request->job_title,
            'employer_name' => $request->employer_name,
            'job_start_date' => $request->job_start_date,
            'job_end_date' => $request->job_end_date,
            'changes_in_job_duties' => $request->changes_in_job_duties,
            'reason_for_job_end' => $request->reason_for_job_end,
            'clients_perspective' => $request->clients_perspective,
            'staff_comments_regarding_job' => $request->staff_comments_regarding_job,
            'employer_comments' => $request->employer_comments,
            'type_of_support' => $request->type_of_support,
            'person_wish_for_another_job' => $request->person_wish_for_another_job,
            'clients_preferences' => $request->clients_preferences,
            'staff_name' => $request->staff_name,
            'date' => $request->date,
            'location_of_service' => $request->location_services_id,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_prescription' => $request->medication_prescription,
            'created_at' => date('Y-m-d H:i:s'),
            'appointment_details_id' => $request->appId,
        ];
        if($request->id){
            JobEndReport::where(['id' => $request->id])->update($jobend);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
            JobEndReport::create($jobend);
        return response()->json(["message" => "Created", "code" => 200]);
        }
    }

    public function setJobTransitionReport(Request $request)
    {
        $jobtransition=[
            'patient_id' => $request->patient_id,
            'added_by' => $request->added_by,
            'future_plan' => $request->future_plan,
            'short_term_goal' => $request->short_term_goal,
            'long_term_goal' => $request->long_term_goal,
            'who_have_you_called_past' => $request->who_have_you_called_past,
            'my_case_manager_yes_no' => $request->my_case_manager_yes_no,
            'my_case_manager_name' => $request->my_case_manager_name,
            'my_case_manager_contact' => $request->my_case_manager_contact,
            'my_therapist_yes_no' => $request->my_therapist_yes_no,
            'my_therapist_name' => $request->my_therapist_name,
            'my_therapist_contact' => $request->my_therapist_contact,
            'my_family_yes_no' => $request->my_family_yes_no,
            'my_family_name' => $request->my_family_name,
            'my_family_contact' => $request->my_family_contact,
            'my_friend_yes_no' => $request->my_friend_yes_no,
            'my_friend_name' => $request->my_friend_name,
            'my_friend_contact' => $request->my_friend_contact,
            'my_significant_other_yes_no' => $request->my_significant_other_yes_no,
            'my_significant_other_name' => $request->my_significant_other_name,
            'my_significant_other_contact' => $request->my_significant_other_contact,
            'clergy_yes_no' => $request->clergy_yes_no,
            'clergy_name' => $request->clergy_name,
            'clergy_contact' => $request->clergy_contact,
            'benefit_planner_yes_no' => $request->benefit_planner_yes_no,
            'benefit_planner_name' => $request->benefit_planner_name,
            'benefit_planner_contact' => $request->benefit_planner_contact,
            'other_yes_no' => $request->other_yes_no,
            'other_name' => $request->other_name,
            'other_contact' => $request->other_contact,
            'schedule_meeting_discuss_for_transition' => $request->schedule_meeting_discuss_for_transition,
            'who_check_in_with_you' => $request->who_check_in_with_you,
            'who_contact_you' => $request->who_contact_you,
            'how_would_like_to_contacted' => $request->how_would_like_to_contacted,
            'coping_strategies' => $request->coping_strategies,
            'dissatisfied_with_your_job' => $request->dissatisfied_with_your_job,
            'reasons_to_re_connect_to_ips' => $request->reasons_to_re_connect_to_ips,
            'patient_name' => $request->patient_name,
            'doctor_name' => $request->doctor_name,
            'transition_report_date' => $request->transition_report_date,
            'date' => $request->date,
            'location_of_service' => $request->location_of_service,
            'type_of_diagnosis' => $request->type_of_diagnosis,
            'category_of_services' => $request->category_of_services,
            'services' => $request->services,
            'complexity_of_services' => $request->complexity_of_services,
            'outcome' => $request->outcome,
            'icd_9_code' => $request->icd_9_code,
            'icd_9_subcode' => $request->icd_9_subcode,
            'medication_prescription' => $request->medication_prescription,
            'created_at' => date('Y-m-d H:i:s'),
            'appointment_details_id' => $request->appId,
        ];
        if($request->patient_id){
            JobTransitionReport::updateOrCreate( ['patient_id' => $request->patient_id], $jobtransition); 
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
            JobTransitionReport::create($jobtransition);
        return response()->json(["message" => "Created", "code" => 200]);
        }
    }
    public function GetJobStartList()
    {
       $list =JobStartFormList::select('id','job_title')->get();
       return response()->json(["message" => "Job Start Form List", 'list' => $list, "code" => 200]);
    }
    public function GetJobStartForm()
    {
       $list =JobStartForm::select('id', 'case_manager', 'name_of_employer')->get();
       return response()->json(["message" => "Job Start Form", 'list' => $list, "code" => 200]);
    }
    public function postCpsHomevisitForm(Request $request)
    {
        $patient_id = $request->patient_id;
        $patient = PatientRegistration::select('name_asin_nric', 'nric_no', 'passport_no','kin_name_asin_nric','kin_nric_no')->where('id', $patient_id)->get();
        $hospital = HospitalManagement::select('hospital_name','hospital_adrress_1','hospital_adrress_2','hospital_adrress_3','hospital_state','hospital_city','hospital_postcode','state.state_name as state_name','postcode.city_name as city_name','postcode.postcode as postcode')
        ->leftJoin('state', 'state.id', '=', 'hospital_management.hospital_state')
        ->leftJoin('postcode', 'postcode.id', '=', 'hospital_management.hospital_postcode')
        ->where('hospital_management.id', $request->hospital_id)
        ->get();
        $user = User::select('name', 'role')->where('id', $request->added_by)->get();
        if(!empty($hospital[0])){
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name1' => $user[0]['name'],
                'user_name' => $patient[0]['kin_name_asin_nric'] ?? 'NA',
                'guardian_nric' =>  $patient[0]['kin_nric_no'] ?? 'NA',
                'designation' => $user[0]['name'],
                'hospital_name' => $hospital[0]['hospital_name'],
                'hospital_adrress_1' => $hospital[0]['hospital_adrress_1'],
                'hospital_adrress_2' => $hospital[0]['hospital_adrress_2'],
                'hospital_adrress_3' => $hospital[0]['hospital_adrress_3'],
                'hospital_adrress_3' => $hospital[0]['hospital_adrress_3'],
                'city_name' => $hospital[0]['city_name'],
                'postcode' => $hospital[0]['postcode'],
                'state_name' => $hospital[0]['state_name'],
            ];
            return response()->json(["message" => "CPS HomeVisit Consent Form", "list" => $response,  "code" => 200]);
        }
        else{
            $response = [
                'patient_name' => $patient[0]['name_asin_nric'],
                'nric_no' => ($patient[0]['nric_no']) ? $patient[0]['nric_no'] : $patient[0]['passport_no'],
                'date' => date('d/m/Y'),
                'user_name1' => $user[0]['name'],
                'designation' => $user[0]['name'],
                'hospital_name' => "HOSPITAL [NA]",
                'user_name' => $patient[0]['kin_name_asin_nric']  ?? 'NA',
                'guardian_nric' =>  $patient[0]['kin_nric_no'] ?? 'NA',
            ];
            return response()->json(["message" => "CPS HomeVisit Consent Form", "list" => $response,  "code" => 200]);
        }

    }
}
