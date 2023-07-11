<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ListOfETP;

class ListOfETPController extends Controller
{
    public function store(Request $request)
    {
     
         $validator = Validator::make($request->all(), [
             'added_by' => 'required|integer',
             'patient_id' => 'required|integer',
             'program' => 'required',
             'location_services' => 'required',
             'services_id' => '',
             'code_id' => '',
             'sub_code_id' => '',
             'type_diagnosis_id' => 'required|integer',
             'category_services' => 'required',
             'complexity_services' => '',
             'outcome' => '',
             'medication_des' => '',
             'appId' => '',
         ]);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }


            $listofetp = [
            'added_by' => $request->added_by,
            'patient_id' => $request->patient_id,
            'program' => $request->program,

            'location_services' => $request->location_services,
            'services_id' => $request->services_id,
            'code_id' => $request->code_id,
            'sub_code_id' => str_replace('"',"",$request->sub_code_id),
            'type_diagnosis_id' => $request->type_diagnosis_id,
            'category_services' => $request->category_services,
            'complexity_services' => $request->complexity_services,
            'outcome' => $request->outcome,
            'medication_des' => $request->medication_des,
            'status' => "1",
            'appointment_details_id'=> $request->appId,

            'add_type_of_diagnosis' => str_replace('"',"",$request->add_type_of_diagnosis),
            'add_code_id' => str_replace('"',"",$request->add_code_id),
            'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
            ];

            $validateListOfETP = [];

         if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
             $validateListOfETP['services_id'] = 'required';
             $listofetp['services_id'] =  $request->services_id;
         } else if ($request->category_services == 'clinical-work') {
             $validateListOfETP['code_id'] = 'required';
             $listofetp['code_id'] =  $request->code_id;
             $validateListOfETP['sub_code_id'] = 'required';
             $listofetp['sub_code_id'] =  str_replace('"',"",$request->sub_code_id);
         }
         $validator = Validator::make($request->all(), $validateListOfETP);
         if ($validator->fails()) {
             return response()->json(["message" => $validator->errors(), "code" => 422]);
         }

         ListOfETP::create($listofetp);
         return response()->json(["message" => "Successfully Saved!", "code" => 200]);

    }


}
