<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\PatientShharpRegistrationDataProducer;
use App\Models\SharpRegistrationFinalStep;

class PatientShharpRegistrationDataProducerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer', //patient_mrn_id is treated as patient_id
            'name_registering_officer' => '',
            'hospital_name' => '',
            'designation' => '',
            'psychiatrist_name' => '',
            'reporting_date' => '',
            'sharp_register_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module = [
            'added_by' => $request->added_by,
            'patient_mrn_id' => $request->patient_id,
            'shharp_register_id' => $request->sharp_register_id,
            'name_registering_officer' => $request->name_registering_officer,
            'hospital_name' => $request->hospital_name,
            'designation' => $request->designation,
            'psychiatrist_name' => $request->psychiatrist_name,
            'reporting_date' => $request->reporting_date,
            'status' => $request->status
        ];
        $sri = $request->sharp_register_id;
        //dd($module);
        if ($sri == 0) {
            return response()->json(["message" => "Can't inserted data.Please fill all forms first!", 'id' => $sri, "code" => 400]);
        } else {

            $chk = PatientShharpRegistrationDataProducer::where('shharp_register_id', $sri)->count();
            if ($chk == 0) {
                PatientShharpRegistrationDataProducer::insert($module);
            } else {
                PatientShharpRegistrationDataProducer::where('shharp_register_id', $sri)->update($module);
            }
            SharpRegistrationFinalStep::where('id', $sri)->update(['status' => $request->status]);
            return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
        }
    }
}
