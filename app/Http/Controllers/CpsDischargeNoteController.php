<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CpsDischargeNote;

class CpsDischargeNoteController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'mrn' => 'required|string',
            'cps_discharge_date' => 'required|date',
            'time' => '',
            'staff_name' => 'required|string',
            'diagnosis' => 'required',
            'post_intervention' => 'required|string',
            'psychopathology' => 'required|string',
            'psychosocial' => 'required|string',
            'potential_risk' => 'required|string',
            'category_of_discharge' => 'required|string',
            'discharge_diagnosis' => 'required',
            'outcome_medication' => 'required|string',
            'location_service' => 'required|integer',
            'diagnosis_type' => 'required|integer',
            'service_category' => 'required|string',
            'complexity_services' => 'required|integer',
            'outcome' => 'required|integer',
            'medication' => 'required',
            'specialist_name' => 'required|string',
            'verification_date' => 'required|date',
            'case_manager' => 'required|string',
            'date' => 'required|date'


        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if($request->id){
            if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
    
                $cpsdischargenote = [
                    'services_id' =>  $request->services_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'cps_discharge_date' =>  $request->cps_discharge_date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'diagnosis' =>  $request->diagnosis,
                    'post_intervention' =>  $request->post_intervention,
                    'psychopathology' =>  $request->psychopathology,
                    'psychosocial' =>  $request->psychosocial,
                    'potential_risk' =>  $request->potential_risk,
                    'category_of_discharge' =>  $request->category_of_discharge,
                    'discharge_diagnosis' =>  $request->discharge_diagnosis,
                    'outcome_medication' =>  $request->outcome_medication,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_services' =>  $request->complexity_services,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'specialist_name' =>  $request->specialist_name,
                    'verification_date' =>  $request->verification_date,
                    'case_manager' =>  $request->case_manager,
                    'date' =>  $request->date,
                    'status' => "1"
                ];
    
                try {
                    $HOD = CpsDischargeNote::firstOrCreate($cpsdischargenote);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $cpsdischargenote, "code" => 200]);
                }
                return response()->json(["message" => "CPS Discharge Form Successfully00", "code" => 200]);
            } else if ($request->service_category == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
    
                $CpsDischarge = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'cps_discharge_date' =>  $request->cps_discharge_date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'diagnosis' =>  $request->diagnosis,
                    'post_intervention' =>  $request->post_intervention,
                    'psychopathology' =>  $request->psychopathology,
                    'psychosocial' =>  $request->psychosocial,
                    'potential_risk' =>  $request->potential_risk,
                    'category_of_discharge' =>  $request->category_of_discharge,
                    'discharge_diagnosis' =>  $request->discharge_diagnosis,
                    'outcome_medication' =>  $request->outcome_medication,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_services' =>  $request->complexity_services,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'specialist_name' =>  $request->specialist_name,
                    'verification_date' =>  $request->verification_date,
                    'case_manager' =>  $request->case_manager,
                    'date' =>  $request->date,
                    'status' => "1"
                ];
    
                try {
                    CpsDischargeNote::where(['id' => $request->id])->update($CpsDischarge);
                    // $HOD = CpsDischargeNote::firstOrCreate($CpsDischarge);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'CpsDischarge' => $CpsDischarge, "code" => 200]);
                }
                return response()->json(["message" => "Cps Discharge Note Successfully11", "code" => 200]);
            }
        }else{
        if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
            $validator = Validator::make($request->all(), [
                'services_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            $cpsdischargenote = [
                'services_id' =>  $request->services_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'cps_discharge_date' =>  $request->cps_discharge_date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'diagnosis' =>  $request->diagnosis,
                'post_intervention' =>  $request->post_intervention,
                'psychopathology' =>  $request->psychopathology,
                'psychosocial' =>  $request->psychosocial,
                'potential_risk' =>  $request->potential_risk,
                'category_of_discharge' =>  $request->category_of_discharge,
                'discharge_diagnosis' =>  $request->discharge_diagnosis,
                'outcome_medication' =>  $request->outcome_medication,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'specialist_name' =>  $request->specialist_name,
                'verification_date' =>  $request->verification_date,
                'case_manager' =>  $request->case_manager,
                'date' =>  $request->date,
                'status' => "1"
            ];

            try {
                $HOD = CpsDischargeNote::firstOrCreate($cpsdischargenote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $cpsdischargenote, "code" => 200]);
            }
            return response()->json(["message" => "CPS Discharge Form Successfully00", "code" => 200]);
        } else if ($request->service_category == 'clinical-work') {
            $validator = Validator::make($request->all(), [
                'code_id' => 'required|integer',
                'sub_code_id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            $CpsDischarge = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $request->sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'cps_discharge_date' =>  $request->cps_discharge_date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'diagnosis' =>  $request->diagnosis,
                'post_intervention' =>  $request->post_intervention,
                'psychopathology' =>  $request->psychopathology,
                'psychosocial' =>  $request->psychosocial,
                'potential_risk' =>  $request->potential_risk,
                'category_of_discharge' =>  $request->category_of_discharge,
                'discharge_diagnosis' =>  $request->discharge_diagnosis,
                'outcome_medication' =>  $request->outcome_medication,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_services' =>  $request->complexity_services,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'specialist_name' =>  $request->specialist_name,
                'verification_date' =>  $request->verification_date,
                'case_manager' =>  $request->case_manager,
                'date' =>  $request->date,
                'status' => "1"
            ];

            try {
                $HOD = CpsDischargeNote::firstOrCreate($CpsDischarge);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'CpsDischarge' => $CpsDischarge, "code" => 200]);
            }
            return response()->json(["message" => "Cps Discharge Note Successfully11", "code" => 200]);
        }
    }
    }
}
