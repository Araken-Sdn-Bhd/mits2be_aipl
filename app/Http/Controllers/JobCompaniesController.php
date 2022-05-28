<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCompanies;
use Validator;
use DB;

class JobCompaniesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'added_by' => 'required|integer',
            'company_name' => 'required|string',
            'company_registration_number' => 'required|string',
            'company_address_1' => 'required|string',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'postcode' => 'required|string',
            'corporate_body_sector' => 'required|json',
            'is_existing_training_program' => 'required|integer',
            'employment_sector' => 'required|json'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $job = [
            'added_by' => $request->added_by,
            'company_name' => $request->company_name,
            'company_registration_number' => $request->company_registration_number,
            'company_address_1' => $request->company_address_1,
            'company_address_2' => $request->company_address_2,
            'company_address_3' => $request->company_address_3,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode' => $request->postcode,
            'corporate_body_sector' => $request->corporate_body_sector,
            'is_existing_training_program' => $request->is_existing_training_program,
            'employment_sector' => $request->employment_sector,
            'created_at' =>  date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s')
        ];
        if ($request->type == 'add') {
            try {
                $id = JobCompanies::create($job);
                return response()->json(["message" => "Company Created", "id" => $id->id, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
        } else if ($request->type == 'update') {
            try {
                $job['status'] = $request->status;
                JobCompanies::where('id', $request->company_id)->update($job);
                return response()->json(["message" => "Job updated", "result" => $job, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Action is not allowed!", "code" => 200]);
        }
    }

    public function addContactPerson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|integer',
            'contact_name' => 'required|string',
            'contact_number' => 'required|string',
            'contact_email' => 'required|string',
            'contact_position' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $job = [
            'contact_name' => $request->contact_name,
            'contact_number' => $request->contact_number,
            'contact_email' => $request->contact_email,
            'contact_position' => $request->contact_position,
            'updated_at' =>  date('Y-m-d H:i:s')
        ];

        try {
            JobCompanies::where('id', $request->company_id)->update($job);
            return response()->json(["message" => "Job updated", "result" => $job, "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 200]);
        }
    }

    public function getInterviewerList()
    {
        return JobCompanies::select('id','contact_name','contact_name','contact_number')->get();
    }
}
