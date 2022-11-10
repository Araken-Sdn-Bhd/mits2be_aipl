<?php

namespace App\Http\Controllers;
use App\Models\Job;
use App\Models\JobOffers;
use App\Models\EmployeeRegistration;
use Validator;
use DB;

use Illuminate\Http\Request;

class JobController extends Controller
{
    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            
            'user_id' => 'required',
            'position_offered' => 'required',
            'location_address_1' => 'required',
            'education_id' => 'required',
            'duration_of_employment' => 'required',
            'salary_offered' => 'required',
            'work_schedule' => 'required',
            'is_transport' => 'required',
            'is_accommodation' => 'required',
            'work_requirement' => 'required|json',
            'branch_id' => 'required',
            'job_availability' => 'required',
    
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
 
       
        $company = DB::table('employee_registration')->select('id')->where('user_id', '=', $request->user_id)->first();

        try {
        $job =[
            'company_id' => $company->id,
            'position' => $request->position_offered,
            'education_id' => $request->education_id,
            'work_requirement' => $request->work_requirement,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s')
        ];

        $jobId = Job::create($job);
    

        $jobOffer = [
            'job_id' => $jobId->id,
            'user_id' => $request->user_id,
            'location_address_1' => $request->location_address_1,
            'location_address_2' => $request->location_address_2,
            'location_address_3' => $request->location_address_3,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode' => $request->postcode,
            'duration_of_employment' => $request->duration_of_employment,
            'salary_offered' => $request->salary_offered,
            'work_schedule' => $request->work_schedule,
            'is_transport' => $request->is_transport,
            'is_accommodation' => $request->is_accommodation,
            'branch_id' => $request->branch_id,
            'job_availability' => $request->job_availability,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
                
                JobOffers::create($jobOffer);

                return response()->json(["message" => "Job Created", "result" => $job, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
       
    }

    public function repeat(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            
            'user_id' => 'required',
            'location_address_1' => 'required',
            'duration_of_employment' => 'required',
            'salary_offered' => 'required',
            'work_schedule' => 'required',
            'is_transport' => 'required',
            'is_accommodation' => 'required',
            'branch_id' => 'required',
            'job_availability' => 'required',
    
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
 
    
        try {
        $jobOffer = [
            'job_id' => $request->job_id,
            'user_id' => $request->user_id,
            'location_address_1' => $request->location_address_1,
            'location_address_2' => $request->location_address_2,
            'location_address_3' => $request->location_address_3,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode' => $request->postcode,
            'duration_of_employment' => $request->duration_of_employment,
            'salary_offered' => $request->salary_offered,
            'work_schedule' => $request->work_schedule,
            'is_transport' => $request->is_transport,
            'is_accommodation' => $request->is_accommodation,
            'branch_id' => $request->branch_id,
            'job_availability' => $request->job_availability,
            'is_repeated' => 1,
            'approval_status' => 2,   //1:pending,0:reject,2:approved
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
                
                JobOffers::create($jobOffer);

                return response()->json(["message" => "Job Created", "result" => $jobOffer, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
       
    }

    public function update(Request $request)
    {
    }
    public function JobListByCompany(Request $request)
    {
        $company = DB::table('employee_registration')->select('id')->where('user_id', '=', $request->user_id)->first();

        return  DB::table('jobs')
        ->select('jobs.*',DB::raw("DATE_FORMAT(jobs.created_at, '%d-%M-%y') as created_at"),'general_setting.section_value',
        'job_offers.approval_status')
       
        ->join('job_offers', 'jobs.id', '=', 'job_offers.job_id')
        ->join('general_setting', 'jobs.education_id', '=', 'general_setting.id')
        ->orderBy('jobs.id','desc')
        ->where('jobs.company_id', '=', $company->id)
        ->where('job_offers.is_repeated',0)
        ->get();
    }

    public function RepeatList(Request $request)
    {
        return  DB::table('job_offers')

        ->select('jobs.*','job_offers.*','hospital_branch__details.*',DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as created_at"),
        'general_setting.section_value')

        ->join('jobs', 'job_offers.job_id', '=', 'jobs.id')
        ->join('hospital_branch__details', 'job_offers.branch_id', '=', 'hospital_branch__details.id')
        ->join('general_setting', 'jobs.education_id', '=', 'general_setting.id')
        ->orderBy('job_offers.id','desc')
        ->where('job_offers.job_id', '=', $request->job_id)->get();
        
    }

    public function getPendingApprovalList(Request $request)
    {
        return  DB::table('job_offers')

        ->select('job_offers.id as joboffersId','jobs.*','job_offers.*','hospital_branch__details.*',DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as created_at"),
        'general_setting.section_value')

        ->join('jobs', 'job_offers.job_id', '=', 'jobs.id')
        ->join('hospital_branch__details', 'job_offers.branch_id', '=', 'hospital_branch__details.id')
        ->join('general_setting', 'jobs.education_id', '=', 'general_setting.id')
        ->orderBy('job_offers.id','desc')
        ->where('jobs.company_id', '=', $request->company_id)
        ->where('job_offers.is_repeated',0)
        ->where('job_offers.approval_status',1)
        ->get();
    }
    public function JobList(Request $request)
    {
        return  DB::table('job_offers')
        ->select('job_offers.id as jobofferId','job_offers.*','jobs.*','employee_registration.*')
       
        ->join('jobs', 'jobs.id', '=', 'job_offers.job_id')
        ->join('employee_registration', 'jobs.company_id', '=', 'employee_registration.id')
        ->orderBy('job_offers.id','desc')
        ->where('job_offers.approval_status',2)
        ->get();
    }

    public function setStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
          
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        JobOffers::where('id', $request->id)->update(['job_availability' => $request->status]);
        return response()->json(["message" => "Job availability updated!", "code" => 200]);
    }

    public function ViewJobDetails(Request $request)
    {
        return  DB::table('job_offers')

        ->select('jobs.*','job_offers.*','hospital_branch__details.*',DB::raw("DATE_FORMAT(job_offers.created_at, '%d-%M-%y') as created_at"),
        'general_setting.section_value')

        ->join('jobs', 'job_offers.job_id', '=', 'jobs.id')
        ->join('hospital_branch__details', 'job_offers.branch_id', '=', 'hospital_branch__details.id')
        ->join('general_setting', 'jobs.education_id', '=', 'general_setting.id')
        ->orderBy('job_offers.id','desc')
        ->where('job_offers.id', '=', $request->id)->get();
        
    }

   
 

}
