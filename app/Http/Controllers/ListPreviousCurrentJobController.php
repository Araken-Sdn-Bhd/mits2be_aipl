<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListPreviousCurrentJob;
use App\Models\PreviousOrCurrentJobRecord;

class ListPreviousCurrentJobController extends Controller
{
    //
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

            $listpreviouscurrentjob = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,

            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
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
             $listpreviouscurrentjob['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateListPreviousCurrentJob);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }

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
