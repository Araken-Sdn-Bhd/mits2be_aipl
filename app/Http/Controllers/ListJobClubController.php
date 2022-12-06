<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListJobClub;

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
            'sub_code_id' => $request->sub_code_id,
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id' => $request->appId,
            ];
 
            $validateListJobClub = [];
 
         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateListJobClub['services_id'] = 'required';
             $listjobclub['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateListJobClub['code_id'] = 'required';
             $listjobclub['code_id'] =  $request->code_id;
             $validateListJobClub['sub_code_id'] = 'required';
             $listjobclub['sub_code_id'] =  $request->sub_code_id;
         }
         $validator = Validator::make($request->all(), $validateListJobClub);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }
 
         ListJobClub::updateOrCreate( ['patient_id' => $request->patient_id], $listjobclub);   
         return response()->json(["message" => "Job Club list Created Successfully!", "code" => 200]);
        
    }

}
