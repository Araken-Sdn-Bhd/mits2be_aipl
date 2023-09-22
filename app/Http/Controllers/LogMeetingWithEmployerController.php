<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogMeetingWithEmployer;
use App\Models\UserDiagnosis;
class LogMeetingWithEmployerController extends Controller
{

    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'appId' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $logmeetingwithemployer = [
                    'patient_id' => $request->patient_id,
                    'added_by' => $request->added_by,
                    'date' => $request->date,
                    'employee_name' => $request->employee_name,
                    'company_name' => $request->company_name,
                    'purpose_of_meeting' => $request->purpose_of_meeting,
                    'discussion_start_time' => $request->discussion_start_time,
                    'discussion_end_time' => $request->discussion_end_time,
                    'staff_name' => $request->staff_name,

                    'location_services' => $request->location_services,
                    'services_id' => $request->services_id,
                    'type_diagnosis_id' => $request->type_diagnosis_id,
                    'add_type_of_diagnosis' => str_replace('"',"",$request->add_diagnosis_type),

                    'code_id' => $request->code_id,
                    'sub_code_id' =>  str_replace('"',"",$request-> sub_code_id),
                    'add_code_id' => $request-> add_code_id,
                    'add_sub_code_id' => str_replace('"',"",$request-> add_sub_code_id),
                   
                    'category_services' => $request->category_services,
                    'complexity_services' => $request->complexity_services,
                    'outcome' => $request->outcome,
                    'medication_des' => $request->medication_des,
                    'status' => $request->status,
                    'appointment_details_id' => $request->appId,
                ];
    
                $validateLogMeetingWithEmployer = [];
    
                if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                    $validateLogMeetingWithEmployer['services_id'] = 'required';
                    $logmeetingwithemployer['services_id'] =  $request->services_id;
                } else if ($request->category_services == 'clinical-work') {
                    $validateLogMeetingWithEmployer['code_id'] = 'required';
                    $logmeetingwithemployer['code_id'] =  $request->code_id;
                    $validateLogMeetingWithEmployer['sub_code_id'] = 'required';
                    $logmeetingwithemployer['sub_code_id'] = str_replace('"',"",$request-> sub_code_id);
                }
                $validator = Validator::make($request->all(), $validateLogMeetingWithEmployer);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                if($request->status=='1'){
                    $user_diagnosis = [
                        'app_id' => $request->appId,
                        'patient_id' =>  $request->patient_id,
                        'diagnosis_id' =>  $request->type_diagnosis_id,
                        'add_diagnosis_id' => str_replace('"',"",$request->add_diagnosis_type),
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request-> sub_code_id),
                        'add_code_id'=> $request-> add_code_id,
                        'add_sub_code_id' => str_replace('"',"",$request-> add_sub_code_id),
                        'outcome_id' =>  $request->outcome,
                        'category_services' =>  $request->category_services,
                        'remarks' => 'log_meeting_with_employer',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    UserDiagnosis::create($user_diagnosis);
                }

        if($request->id){
            LogMeetingWithEmployer::where(['id' => $request->id])->update($logmeetingwithemployer);
            return response()->json(["message" => "Updated", "code" => 200]);
        }else{
            LogMeetingWithEmployer::create($logmeetingwithemployer);
            return response()->json(["message" => "Created", "code" => 200]);
        }
    }

    public function GetEmployerList()
    {
        $list = LogMeetingWithEmployer::select('id', 'employee_name')
            ->get();
        return response()->json(["message" => "Employer Name from Log Meeting with Employer", 'list' => $list, "code" => 200]);
    }
}
