<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogMeetingWithEmployer;

class LogMeetingWithEmployerController extends Controller
{
    //

    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_id' => 'required|integer',
             'date' => 'required|date',
             'employee_name' => 'required|string',
             'company_name' => 'required|string',
             'purpose_of_meeting' => 'required|string',
             'discussion_start_time' => 'required',
             'discussion_end_time' => 'required',
             'staff_name' => 'required|string',
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required|string',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => ''
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
        
            $logmeetingwithemployer = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'date' => $request->date,
            'employee_name' => $request->employee_name,
            'company_name' => $request->company_name,
            'purpose_of_meeting' => $request->purpose_of_meeting,
            'discussion_start_time' => $request->discussion_start_time,
            'discussion_end_time' => $request->discussion_end_time,
            'staff_name' => $request->staff_name,
           
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1"
            ];
 
            $validateLogMeetingWithEmployer = [];
 
         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateLogMeetingWithEmployer['services_id'] = 'required';
             $logmeetingwithemployer['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateLogMeetingWithEmployer['code_id'] = 'required';
             $logmeetingwithemployer['code_id'] =  $request->code_id;
             $validateLogMeetingWithEmployer['sub_code_id'] = 'required';
             $logmeetingwithemployer['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateLogMeetingWithEmployer);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
         LogMeetingWithEmployer::firstOrCreate($logmeetingwithemployer);  
         return response()->json(["message" => "Log Meeting Employer Created Successfully!", "code" => 200]);
        
    }
}
