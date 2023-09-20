<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CpsDischargeNote;
use App\Models\UserDiagnosis;
class CpsDischargeNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'mrn' => 'required|string',


        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $cpsdischargenote = [
            'services_id' =>  $request->services_id,
            'code_id' =>  $request->code_id,
            'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
            'add_code_id' =>  $request->add_code_id,
            'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
            'added_by' =>  $request->added_by,
            'patient_mrn_id' =>  $request->patient_mrn_id,
            'name' =>  $request->name,
            'mrn' =>  $request->mrn,
            'cps_discharge_date' =>  $request->cps_discharge_date,
            'time' =>  $request->time,
            'staff_name' =>  $request->staff_name,
            'diagnosis' =>  $request->diagnosis_type,
            'post_intervention' =>  $request->post_intervention,
            'psychopathology' =>  $request->psychopathology,
            'psychosocial' =>  $request->psychosocial,
            'potential_risk' =>  $request->potential_risk,
            'category_of_discharge' =>  $request->category_of_discharge,
            'discharge_diagnosis' =>  $request->discharge_diagnosis,
            'outcome_medication' =>  $request->outcome_medication,
            'location_service' =>  $request->location_service,
            'diagnosis_type' =>  $request->diagnosis_type,
            'add_diagnosis_type' => str_replace('"',"",$request->add_diagnosis_type),
            'service_category' =>  $request->service_category,
            'complexity_services' =>  $request->complexity_services,
            'outcome' =>  $request->outcome,
            'medication' =>  $request->medication,
            'specialist_name' =>  $request->specialist_name,
            'verification_date' =>  $request->verification_date,
            'case_manager' =>  $request->case_manager,
            'date' =>  $request->date,
            'status' => $request->status,
            'appointment_details_id' => $request->appId,
        ];
        if($request->status=='1'){
                $user_diagnosis = [
                    'app_id' => $request->appId,
                    'patient_id' =>  $request->patient_mrn_id,
                    'diagnosis_id' =>  $request->diagnosis_type,
                    'add_diagnosis_id' => str_replace('"',"",$request->add_diagnosis_type),
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                    'add_code_id'=> $request->add_code_id,
                    'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
                    'outcome_id' =>  $request->outcome,
                    'category_services' =>  $request->service_category,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                UserDiagnosis::create($user_diagnosis);
        }

        if($request->id){
            CpsDischargeNote::where(['id' => $request->id])->update($cpsdischargenote);
            return response()->json(["message" => "Updated", "code" => 200]);
        }
        else{
            CpsDischargeNote::create($cpsdischargenote);
            return response()->json(["message" => "CPS Discharge Form Successfully Created!", "code" => 200]);
        }
        //if($request->status == '0'){

        //        $cpsdischargenote = [
        //            'services_id' =>  $request->services_id,
        //            'code_id' =>  $request->code_id,
        //            'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
        //            'add_code_id' =>  $request->add_code_id,
        //            'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
        //            'added_by' =>  $request->added_by,
        //            'patient_mrn_id' =>  $request->patient_mrn_id,
        //            'name' =>  $request->name,
        //            'mrn' =>  $request->mrn,
        //            'cps_discharge_date' =>  $request->cps_discharge_date,
        //            'time' =>  $request->time,
        //            'staff_name' =>  $request->staff_name,
        //            'diagnosis' =>  $request->diagnosis_type,
        //            'post_intervention' =>  $request->post_intervention,
        //            'psychopathology' =>  $request->psychopathology,
        //            'psychosocial' =>  $request->psychosocial,
        //            'potential_risk' =>  $request->potential_risk,
        //            'category_of_discharge' =>  $request->category_of_discharge,
        //            'discharge_diagnosis' =>  $request->discharge_diagnosis,
        //            'outcome_medication' =>  $request->outcome_medication,
        //            'location_service' =>  $request->location_service,
        //            'diagnosis_type' =>  $request->diagnosis_type,
        //            'add_type_of_diagnosis' => str_replace('"',"",$request->add_diagnosis_type),
        //            'service_category' =>  $request->service_category,
        //            'complexity_services' =>  $request->complexity_services,
        //            'outcome' =>  $request->outcome,
        //            'medication' =>  $request->medication,
        //            'specialist_name' =>  $request->specialist_name,
        //            'verification_date' =>  $request->verification_date,
        //            'case_manager' =>  $request->case_manager,
        //            'date' =>  $request->date,
        //            'status' => $request->status,
        //            'appointment_details_id' => $request->appId,
        //        ];
    
        //        if($request->id){
        //            CpsDischargeNote::where(['id' => $request->id])->update($cpsdischargenote);
        //            return response()->json(["message" => "Updated", "code" => 200]);
        //        }
        //        else{
        //            CpsDischargeNote::create($cpsdischargenote);
        //            return response()->json(["message" => "CPS Discharge Form Successfully Created!", "code" => 200]);
        //        }
        //}else{
        //    $cpsdischargenote = [
        //        'services_id' =>  $request->services_id,
        //        'code_id' =>  $request->code_id,
        //        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
        //        'add_code_id' =>  $request->add_code_id,
        //        'add_sub_code_id' => str_replace('"',"",$request->add_sub_code_id),
        //        'added_by' =>  $request->added_by,
        //        'patient_mrn_id' =>  $request->patient_mrn_id,
        //        'name' =>  $request->name,
        //        'mrn' =>  $request->mrn,
        //        'cps_discharge_date' =>  $request->cps_discharge_date,
        //        'time' =>  $request->time,
        //        'staff_name' =>  $request->staff_name,
        //        'diagnosis' =>  $request->diagnosis_type,
        //        'post_intervention' =>  $request->post_intervention,
        //        'psychopathology' =>  $request->psychopathology,
        //        'psychosocial' =>  $request->psychosocial,
        //        'potential_risk' =>  $request->potential_risk,
        //        'category_of_discharge' =>  $request->category_of_discharge,
        //        'discharge_diagnosis' =>  $request->discharge_diagnosis,
        //        'outcome_medication' =>  $request->outcome_medication,
        //        'location_service' =>  $request->location_service,
        //        'diagnosis_type' =>  $request->diagnosis_type,
        //        'add_type_of_diagnosis' => str_replace('"',"",$request->add_diagnosis_type),
        //        'service_category' =>  $request->service_category,
        //        'complexity_services' =>  $request->complexity_services,
        //        'outcome' =>  $request->outcome,
        //        'medication' =>  $request->medication,
        //        'specialist_name' =>  $request->specialist_name,
        //        'verification_date' =>  $request->verification_date,
        //        'case_manager' =>  $request->case_manager,
        //        'date' =>  $request->date,
        //        'status' => "1",
        //        'appointment_details_id' => $request->appId,
        //    ];

        //    if($request->id){
        //        CpsDischargeNote::where(['id' => $request->id])->update($cpsdischargenote);
        //        return response()->json(["message" => "Updated", "code" => 200]);
        //    }
        //    else{
        //        CpsDischargeNote::create($cpsdischargenote);
        //        return response()->json(["message" => "CPS Discharge Form Successfully Created!", "code" => 200]);
        //    }

        //}
    }
}

