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
            
            'added_by' => 'required',
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
 
       
        $company = DB::table('employee_registration')->select('id')->where('user_id', '=', $request->added_by)->first();

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
    }

    public function update(Request $request)
    {
    }

    public function JobList(Request $request)
    {
    }

    public function RepeatList(Request $request)
    {
    }
    
}
