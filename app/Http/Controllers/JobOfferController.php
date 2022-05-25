<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobOffers;
use App\Models\JobCompanies;
use App\Models\HospitalBranchManagement;
use Validator;
use DB;

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
            'position_location_2'=>'',
            'position_location_3'=>''
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
            'updated_at' =>  date('Y-m-d H:i:s')
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
                $job['status'] = $request->status;
                JobOffers::where('id', $request->job_id)->update($job);
                return response()->json(["message" => "Job updated", "result" => $job, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Action is not allowed!", "code" => 422]);
        }
    }

    public function jobList(Request $request)
    {
        return JobOffers::select(DB::raw("count('id') as job_posted"), 'id','position_offered')->where('added_by', $request->added_by)->groupBy('position_offered','id')->get();
    }

    public function jobListById(Request $request)
    {
        return JobOffers::select( '*','position_offered')->where('added_by', $request->added_by)->where('id', $request->id)->get();
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
        ->select('*','hospital_branch__details.id as idbranch','job_offers.id as id','job_offers.status as status', DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as posted_at"))->where(['job_offers.added_by' => $request->added_by, 'job_offers.company_id' => $request->company_id])->get();
        $company=DB::table('job_companies')->select('company_name')->where('id',$request->company_id)->first();
        return response()->json(["message" => "Job List", 'list' => $users,"companyname"=>$company, "code" => 200]);
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
        JobOffers::where('id', $request->id)->update(['status' => $request->status, 'status_changed_by' => $request->added_by]);
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
        $list = JobOffers::select('id','position_offered', 'duration_of_employment', 'position_location_1', 'salary_offered', 'work_schedule', 'company_id', 'job_availability')
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
}
