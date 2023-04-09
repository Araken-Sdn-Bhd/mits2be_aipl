<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationDischargeNote;
use Validator;

class ConsultationDischargeNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'id' => '',
            'appId' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->status == 1) {
            $consultationdischarge = [
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,

                'diagnosis_id' => $request->diagnosis_id,
                'category_discharge' => $request->category_discharge,
                'comment' => $request->comment,
                'specialist_name_id' => $request->specialist_name_id,
                'date' => $request->date,

                'location_services' => $request->location_services,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $request->sub_code_id,
                'add_code_id' =>  $request->add_code_id,
                'add_sub_code_id' =>  $request->add_sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "1",
                'appointment_details_id' => $request->appId,
            ];

            $validateConsultationDischarge = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateConsultationDischarge['services_id'] = 'required';
                $consultationdischarge['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateConsultationDischarge['code_id'] = 'required';
                $consultationdischarge['code_id'] =  $request->code_id;
                $validateConsultationDischarge['sub_code_id'] = 'required';
                $consultationdischarge['sub_code_id'] =  $request->sub_code_id;
                $consultationdischarge['add_code_id'] =  $request->add_code_id;
                $consultationdischarge['add_sub_code_id'] =  $request->add_sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateConsultationDischarge);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            if ($request->id) {
                ConsultationDischargeNote::where(
                    ['id' => $request->id]
                )->update($consultationdischarge);
                return response()->json(["message" => "Consultation Discharge Note Updated Successfully!", "code" => 200]);
            } else {
                ConsultationDischargeNote::firstOrCreate($consultationdischarge);
                return response()->json(["message" => "Consultation Discharge Note Created Successfully!", "code" => 200]);
            }
        } else if ($request->status == 0) {
            $consultationdischarge = [
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'diagnosis_id' => $request->diagnosis_id,
                'category_discharge' => $request->category_discharge,
                'comment' => $request->comment,
                'specialist_name_id' => $request->specialist_name_id,
                'date' => $request->date,
                'location_services' => $request->location_services,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => $request->sub_code_id,
                'add_code_id' => $request->add_code_id,
                'add_sub_code_id' => $request->add_sub_code_id,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "0",
                'appointment_details_id' => $request->appId,
            ];

            $validateConsultationDischarge = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateConsultationDischarge['services_id'] = 'required';
                $consultationdischarge['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateConsultationDischarge['code_id'] = 'required';
                $consultationdischarge['code_id'] =  $request->code_id;
                $validateConsultationDischarge['sub_code_id'] = 'required';
                $consultationdischarge['sub_code_id'] =  $request->sub_code_id;
                $consultationdischarge['add_code_id'] =  $request->add_code_id;
                $consultationdischarge['add_sub_code_id'] =  $request->add_sub_code_id;
            }
            $validator = Validator::make($request->all(), $validateConsultationDischarge);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            if ($request->id) {
                ConsultationDischargeNote::where(
                    ['id' => $request->id]
                )->update($consultationdischarge);
                return response()->json(["message" => "Consultation Discharge Note Updated Successfully!", "code" => 200]);
            } else {
                ConsultationDischargeNote::firstOrCreate($consultationdischarge);
                return response()->json(["message" => "Consultation Discharge Note Created Successfully!", "code" => 200]);
            }
        }
    }
}
