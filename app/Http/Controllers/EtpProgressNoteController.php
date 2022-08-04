<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EtpProgressNote;

class EtpProgressNoteController extends Controller
{
    //
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'mrn' => 'required|string',
            'date' => 'required|date',
            'time' => '',
            'staff_name' => 'required|string',
            'work_readiness' => 'required|string',
            'progress_note' => 'required|string',
            'management_plan' => 'required|string',
            'location_service' => 'required|integer',
            'diagnosis_type' => 'required|integer',
            'service_category' => 'required|string',
            'complexity_service' => 'required|integer',
            'outcome' => 'required|integer',
            'medication' => 'required|string'


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
    
                $etpprogressnote = [
                    'services_id' =>  $request->services_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'work_readiness' =>  $request->work_readiness,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "1"
                ];
    
                try {
                    EtpProgressNote::where(
                        ['id' => $request->id]
                    )->update($etpprogressnote);
                    // $HOD = EtpProgressNote::firstOrCreate($etpprogressnote);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $etpprogressnote, "code" => 200]);
                }
                return response()->json(["message" => "ETP Progress Form Successfully00", "code" => 200]);
            } else if ($request->service_category == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
    
                $EtpProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'work_readiness' =>  $request->work_readiness,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "1"
                ];
    
                try {
                    EtpProgressNote::where(
                        ['id' => $request->id]
                    )->update($EtpProgress);
                    // $HOD = EtpProgressNote::firstOrCreate($EtpProgress);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $EtpProgress, "code" => 200]);
                }
                return response()->json(["message" => "ETP Progress Note Successfully11", "code" => 200]);
            }
        }else{
        if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
            $validator = Validator::make($request->all(), [
                'services_id' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            $etpprogressnote = [
                'services_id' =>  $request->services_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'work_readiness' =>  $request->work_readiness,
                'progress_note' =>  $request->progress_note,
                'management_plan' =>  $request->management_plan,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_service' =>  $request->complexity_service,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'status' => "1"
            ];

            try {
                $HOD = EtpProgressNote::firstOrCreate($etpprogressnote);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $etpprogressnote, "code" => 200]);
            }
            return response()->json(["message" => "ETP Progress Form Successfully00", "code" => 200]);
        } else if ($request->service_category == 'clinical-work') {
            $validator = Validator::make($request->all(), [
                'code_id' => 'required|integer',
                'sub_code_id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            $EtpProgress = [
                'services_id' =>  $request->services_id,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $request->sub_code_id,
                'added_by' =>  $request->added_by,
                'patient_mrn_id' =>  $request->patient_mrn_id,
                'name' =>  $request->name,
                'mrn' =>  $request->mrn,
                'date' =>  $request->date,
                'time' =>  $request->time,
                'staff_name' =>  $request->staff_name,
                'work_readiness' =>  $request->work_readiness,
                'progress_note' =>  $request->progress_note,
                'management_plan' =>  $request->management_plan,
                'location_service' =>  $request->location_service,
                'diagnosis_type' =>  $request->diagnosis_type,
                'service_category' =>  $request->service_category,
                'complexity_service' =>  $request->complexity_service,
                'outcome' =>  $request->outcome,
                'medication' =>  $request->medication,
                'status' => "1"
            ];

            try {
                $HOD = EtpProgressNote::firstOrCreate($EtpProgress);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $EtpProgress, "code" => 200]);
            }
            return response()->json(["message" => "ETP Progress Note Successfully11", "code" => 200]);
        }
    }
    }
}
