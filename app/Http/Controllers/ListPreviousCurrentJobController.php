<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListPreviousCurrentJob;
use App\Models\PreviousOrCurrentJobRecord;
use App\Models\UserDiagnosis;

class ListPreviousCurrentJobController extends Controller
{
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_id' => 'required|integer',
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'jobrecord' => '',
             'appId'=> '',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
         $additional_diagnosis=str_replace('"','',$request->additional_diagnosis);
         $additional_sub_code_id=str_replace('"','',$request->additional_sub_code_id);
         $sub_code_id=str_replace('"','',$request->sub_code_id);

            $listpreviouscurrentjob = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $sub_code_id,//newly added
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'add_type_diagnosis_id'=> $additional_diagnosis, //newly added
            'add_sub_code_id' => $additional_sub_code_id, //newly added
            'add_code_id' => $request->additional_code_id, //newly added
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id'=> $request->appId,
            ];

            $validateListPreviousCurrentJob= [];

         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateListPreviousCurrentJob['services_id'] = 'required';
             $listpreviouscurrentjob['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateListPreviousCurrentJob['code_id'] = 'required';
             $listpreviouscurrentjob['code_id'] =  $request->code_id;
             $validateListPreviousCurrentJob['sub_code_id'] = 'required';
             $listpreviouscurrentjob['sub_code_id'] =  $sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateListPreviousCurrentJob);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
         $user_diagnosis = [
            'app_id' => $request->appId,
            'patient_id' =>  $request->patient_id,
            'diagnosis_id' =>  $request->type_diagnosis_id,
            'add_diagnosis_id' => $additional_diagnosis,
            'code_id' =>  $request->code_id,
            'sub_code_id' =>  $sub_code_id,
            'add_code_id'=> $request->additional_code_id,
            'add_sub_code_id' => $additional_sub_code_id,
            'outcome_id' =>  $request->outcome,
            'category_services' =>  $request->category_services,
            'remarks' => 'list_previous_current_job',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        UserDiagnosis::create($user_diagnosis);
         $id=ListPreviousCurrentJob::firstOrCreate($listpreviouscurrentjob);
         $listpreviousid = ($id->id);
         if(!empty($request->jobrecord)){
            foreach($request->jobrecord as $key) {
                $data = array('job' => $key['job'],'patient_id' =>$request->patient_id,'salary'=>$key['salary'],'duration'=>$key['duration'],'reason_for_quit'=>$key['reason_for_quit'],'list_previous_current_job_id'=>$listpreviousid);
                PreviousOrCurrentJobRecord::insert($data);
            }
         }

         return response()->json(["message" => "Job List Created Successfully!", "code" => 200]);

    }


}
