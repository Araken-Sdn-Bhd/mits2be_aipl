<?php

namespace App\Http\Controllers;
use App\Models\UserDiagnosis;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RehabDischargeNote;


class RehabDischargeNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => '',
            'appId' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->status == 1) {
            $rehabdischarge = [
                'added_by' => $request->added_by,
                'patient_mrn_id' => $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'diagnosis_id' => $request->diagnosis_id,
                'diagnosis_type' =>  $request->diagnosis_type,
                'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                'intervention' => $request->intervention,
                'discharge_category' => $request->discharge_category,
                'comment' => $request->comment,
                'location_services' => $request->location_services,
                'diagnosis_type' => $request->diagnosis_type,
                'service_category' => $request->service_category,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                'add_code_id' => $request->add_code_id,
                'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication' => $request->medication,
                'specialist_name' => $request->specialist_name,
                'case_manager' => $request->case_manager,
                'verification_date_1' => $request->verification_date_1,
                'verification_date_2' => $request->verification_date_2,
                'status' => "1",
                'appointment_details_id' => $request->appId,

            ];

            $validateRehabDischarge = [];

            if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                $validateRehabDischarge['services_id'] = 'required';
                $Rehabdischarge['services_id'] =  $request->services_id;
            } else if ($request->service_category == 'clinical-work') {
                $validateRehabDischarge['code_id'] = 'required';
                $Rehabdischarge['code_id'] =  $request->code_id;
                $validateRehabDischarge['sub_code_id'] = 'required';
                $Rehabdischarge['sub_code_id'] =   str_replace('"',"",$request->sub_code_id);
                $Rehabdischarge['add_code_id'] =  $request->add_code_id;
                $Rehabdischarge['add_sub_code_id'] =   str_replace('"',"",$request->add_sub_code_id);
            }
            $validator = Validator::make($request->all(), $validateRehabDischarge);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $user_diagnosis = [
                'app_id' => $request->appId,
                'patient_id' =>  $request->patient_mrn_id,
                'diagnosis_id' =>  $request->diagnosis_id,
                'add_diagnosis_id' => str_replace('"',"",$request->add_diagnosis_type),
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                'add_code_id'=> $request->add_code_id,
                'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
                'outcome_id' =>  $request->outcome,
                'category_services' =>  $request->service_category,
                'remarks' => 'rehab_discharge_note',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserDiagnosis::create($user_diagnosis);
            if ($request->id) {
                RehabDischargeNote::where(['id' => $request->id])->update($rehabdischarge);
                return response()->json(["message" => "Rehab Discharge Note Created Successfully!", "code" => 200]);
            } else {
                RehabDischargeNote::firstOrCreate($rehabdischarge);
                return response()->json(["message" => "Rehab Discharge Note Created Successfully!", "code" => 200]);
            }
        } else if ($request->status == 0) {
            $rehabdischarge = [
                'added_by' => $request->added_by,
                'patient_mrn_id' => $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'diagnosis_id' => $request->diagnosis_id,
                'intervention' => $request->intervention,
                'discharge_category' => $request->discharge_category,
                'comment' => $request->comment,
                'location_services' => $request->location_services,
                'diagnosis_id' => $request->diagnosis_id,
                'diagnosis_type' =>  $request->diagnosis_type,
                'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                'service_category' => $request->service_category,
                'services_id' => $request->services_id,
                'code_id' => $request->code_id,
                'sub_code_id' => str_replace('"',"",$request->sub_code_id),
                'add_code_id' => $request->add_code_id,
                'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication' => $request->medication,
                'specialist_name' => $request->specialist_name,
                'case_manager' => $request->case_manager,
                'verification_date_1' => $request->verification_date_1,
                'verification_date_2' => $request->verification_date_2,
                'status' => "0",
                'appointment_details_id' => $request->appId,

            ];

            $validateRehabDischarge = [];

            if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                $validateRehabDischarge['services_id'] = 'required';
                $Rehabdischarge['services_id'] =  $request->services_id;
            } else if ($request->service_category == 'clinical-work') {
                $validateRehabDischarge['code_id'] = 'required';
                $Rehabdischarge['code_id'] =  $request->code_id;
                $validateRehabDischarge['sub_code_id'] = 'required';
                $Rehabdischarge['sub_code_id'] =  str_replace('"',"",$request->sub_code_id);
                $Rehabdischarge['add_code_id'] =  $request->add_code_id;
                $Rehabdischarge['add_sub_code_id'] =  str_replace('"',"",$request->add_sub_code_id);
            }
            if ($request->id) {
                RehabDischargeNote::where(['id' => $request->id])->update($rehabdischarge);
                return response()->json(["message" => "Rehab Discharge Note Created Successfully!", "code" => 200]);
            } else {
                RehabDischargeNote::firstOrCreate($rehabdischarge);
                return response()->json(["message" => "Rehab Discharge Note Created Successfully!", "code" => 200]);
            }
        }
    }
}
