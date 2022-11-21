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
             'medication_des' => '',
             'appId'=> '',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }


            $logmeetingwithemployer = [
            'added_by' => $request->get('added_by'),
            'patient_id' => $request->get('patient_id'),
            'date' => $request->get('date'),
            'employee_name' => $request->get('employee_name'),
            'company_name' => $request->get('company_name'),
            'purpose_of_meeting' => $request->get('purpose_of_meeting'),
            'discussion_start_time' => $request->get('discussion_start_time'),
            'discussion_end_time' => $request->get('discussion_end_time'),
            'staff_name' => $request->get('staff_name'),

            'location_services' => $request->get('location_services'),
            'services_id' => $request->get('services_id'),
            'code_id' => $request->get('code_id'),
            'sub_code_id' => $request->get('sub_code_id'),
            'type_diagnosis_id' => $request->get('type_diagnosis_id'),
            'category_services' => $request->get('category_services'),
            'complexity_services' => $request->get('complexity_of_services'),
            'outcome' => $request->get('outcome'),
            'medication_des' => $request->get('medication_des'),
            'status' => "1",
            'appointment_details_id'=> $request->appId,
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

         LogMeetingWithEmployer::updateOrCreate( ['patient_id' => $request->patient_id], $logmeetingwithemployer);
         return response()->json(["message" => "Log Meeting Employer Created Successfully!", "code" => 200]);

    }

    public function GetEmployerList()
    {
        $list = LogMeetingWithEmployer::select('id', 'employee_name')
            ->get();
        return response()->json(["message" => "Employer Name from Log Meeting with Employer", 'list' => $list, "code" => 200]);
    }
}
