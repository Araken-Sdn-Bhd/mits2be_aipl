<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListJobClub;
use App\Models\UserDiagnosis;

class ListJobClubController extends Controller
{
    public function store(Request $request)
    {
   
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_id' => 'required|integer',
             'job_club' => 'required',
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'appointment_details_id' => '',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }


            $listjobclub = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'job_club' => $request->job_club,

            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => str_replace('"',"",$request->sub_code_id),
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'add_type_of_diagnosis' =>str_replace('"',"",$request->add_type_of_diagnosis),
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id' => $request->appId,

            'add_code_id' =>$request->add_code_id,
            'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
            ];

            $validateListJobClub = [];

         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateListJobClub['services_id'] = 'required';
             $listjobclub['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateListJobClub['code_id'] = 'required';
             $listjobclub['code_id'] =  $request->code_id;
             $validateListJobClub['sub_code_id'] = 'required';
             $listjobclub['sub_code_id'] = str_replace('"',"",$request->add_sub_code_id);
         }
         $validator = Validator::make($request->all(), $validateListJobClub);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
         $user_diagnosis = [
            'app_id' => $request->appId,
            'patient_id' =>  $request->patient_id,
            'diagnosis_id' =>  $request->type_diagnosis_id,
            'add_diagnosis_id' => str_replace('"',"",$request->add_type_of_diagnosis),
            'code_id' =>  $request->code_id,
            'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
            'add_code_id'=> $request->add_code_id,
            'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
            'outcome_id' =>  $request->outcome,
            'category_services' =>  $request->category_services,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        UserDiagnosis::create($user_diagnosis);

         $res=ListJobClub::create($listjobclub);
        
         return response()->json(["message" => "Job Club list Created Successfully!", "code" => 200]);

    }

}
