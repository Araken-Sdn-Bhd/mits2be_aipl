<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkAnalysisForm; 
use App\Models\JobDescription;
use App\Models\JobSpecification;
use App\Models\WorkAnalysisJobSpecification;
use CreateWorkAnalysisJobSpecificationTable;
use Validator;
use DB;
use Exception;

class WorkAnalysisFormController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'company_name' => 'required|string',
            'company_address1' => 'required|string',
            'company_address2' => '',
            'company_address3' => '',
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'postcode_id' => 'required|integer',

            'supervisor_name' => 'required|string',
            'email' => 'required|string',
            'position' => 'required|string',
            'job_position' => 'required|string',
            'client_name' => 'required|string',
            'current_wage' => 'required|string',
            'wage_specify' => '',
            'wage_change_occur' => '',
            'change_in_rate' => '',
            'from' => '',
            'to' => '',
            'on_date' => '',
            'works_hour_week' => '',
            'work_schedule' => '',
            'no_of_current_employee' => '',
            'no_of_other_employee' => '',
            'during_same_shift' => '',

            'education_level' => 'required|string',
            'grade' => 'required|string',
            'job_experience_year' => 'required|string',
            'job_experience_months' => 'required|string',
            'others' => 'required|string',
            
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'jobs' =>'required',
             'job_specification' =>'',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $WorkAnalysisForm = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'company_name' => $request->company_name,
            'company_address1' =>$request->company_address1,
            'company_address2' =>$request->company_address2,
            'company_address3' =>$request->company_address3,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postcode_id' => $request->postcode_id,

            'supervisor_name' => $request->supervisor_name,
            'email' => $request->email,
            'position' => $request->position,
            'job_position' => $request->job_position,
            'client_name' => $request->client_name,
            'current_wage' => $request->current_wage,
            'wage_specify' => $request->wage_specify,
            'wage_change_occur' => $request->wage_change_occur,

            // 'change_in_rate' => $request->change_in_rate,
            // 'from' => $request->from,
            // 'to' =>$request->to,
            // 'on_date' => $request->on_date,
            // 'works_hour_week' => $request->works_hour_week,
            // 'work_schedule' => $request->work_schedule,
            // 'no_of_current_employee' => $request->no_of_current_employee,
            // 'no_of_other_employee' => $request->no_of_other_employee,
            // 'during_same_shift' => $request->during_same_shift,
            'education_level' => $request->education_level,
            'grade' => $request->grade,
            'job_experience_year' => $request->job_experience_year,
            'job_experience_months' => $request->job_experience_months,
            'others' => $request->others,
           
            'location_services' => $request->location_services,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1"
        ];

        $validateWorkAnalysisForm = [];
 
        if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
            $validateWorkAnalysisForm['services_id'] = 'required';
            $WorkAnalysisForm['services_id'] =  $request->services_id;
        } else if ($request->category_services == 'clinical-work') {
            $validateWorkAnalysisForm['code_id'] = 'required';
            $WorkAnalysisForm['code_id'] =  $request->code_id;
            $validateWorkAnalysisForm['sub_code_id'] = 'required';
            $WorkAnalysisForm['sub_code_id'] =  $request->sub_code_id;
        }

        if ($request->wage_change_occur == 'yes') {
            $validateWorkAnalysisForm['change_in_rate'] = 'required';
            $WorkAnalysisForm['change_in_rate'] =  $request->change_in_rate;
            $validateWorkAnalysisForm['from'] = 'required';
            $WorkAnalysisForm['from'] =  $request->from;
            $validateWorkAnalysisForm['to'] = 'required';
            $WorkAnalysisForm['to'] =  $request->to;
            $validateWorkAnalysisForm['on_date'] = 'required';
            $WorkAnalysisForm['on_date'] =  $request->on_date;
            $validateWorkAnalysisForm['works_hour_week'] = 'required';
            $WorkAnalysisForm['works_hour_week'] =  $request->on_date;
            $validateWorkAnalysisForm['work_schedule'] = 'required';
            $WorkAnalysisForm['work_schedule'] =  $request->work_schedule;
            $validateWorkAnalysisForm['no_of_current_employee'] = 'required';
            $WorkAnalysisForm['no_of_current_employee'] =  $request->no_of_current_employee;
            $validateWorkAnalysisForm['no_of_other_employee'] = 'required';
            $WorkAnalysisForm['no_of_other_employee'] =  $request->no_of_other_employee;
            $validateWorkAnalysisForm['during_same_shift'] = 'required';
            $WorkAnalysisForm['during_same_shift'] =  $request->during_same_shift;

        }

        $validator = Validator::make($request->all(), $validateWorkAnalysisForm);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        
        // dd($WorkAnalysisForm);
        $ab=WorkAnalysisForm::firstOrCreate($WorkAnalysisForm);
        $WorkAnalysisFormid=($ab->id);  

        if(!empty($request->jobs)){
            foreach($request->jobs as $key) {
            if($key['task_description']){
             $data = array('task_description' => $key['task_description'],'patient_id' =>$request->patient_id,'objectives'=>$key['objectives'],'procedure'=>$key['procedure'],'rate_of_time'=>$key['rate_of_time'],'work_analysis_form_id'=>$WorkAnalysisFormid);
                JobDescription::insert($data); 
            }
            }
         } 
         if(!empty($request->job_specification)){
            foreach($request->job_specification as $key) {
                if($key['questions']){
             $data = array('question_name' => $key['questions'],'patient_id' =>$request->patient_id,'answer'=>$key['answer'],'comment'=>$key['comments'],'work_analysis_form_id'=>$WorkAnalysisFormid);
                WorkAnalysisJobSpecification::insert($data); 
                }
            }
         } 

        return response()->json(["message" => "Work Analysis Form Created Successfully!", "code" => 200]);

    }
}
