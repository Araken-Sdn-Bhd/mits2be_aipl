<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EtpProgressNote;

class EtpProgressNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($request->status == "0") {
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
                    'status' => "0",
                    'appointment_details_id' => $request->appId,
                    'additional_diagnosis' => $additional_diagnosis,
                ];

                if($request->id) {
                    EtpProgressNote::where(
                        ['id' => $request->id]
                    )->update($etpprogressnote);
                    return response()->json(["message" => "ETP progress note updated", "code" => 200]);
                } else {
                    EtpProgressNote::firstOrCreate($etpprogressnote);
                    return response()->json(["message" => "ETP progress note created successfully", "code" => 200]);
                }
            } else if ($request->service_category == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $EtpProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
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
                    'status' => "0",
                    'appointment_details_id' => $request->appId,
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_code_id' => $request->additional_code_id,
                    'additional_subcode' => $additional_subcode,
                ];

                if($request->id) {
                    EtpProgressNote::where(
                        ['id' => $request->id]
                    )->update($EtpProgress);
                    return response()->json(["message" => "ETP progress note updated", "code" => 200]);
                } else {
                    EtpProgressNote::firstOrCreate($EtpProgress);
                    return response()->json(["message" => "ETP progress note created successfully", "code" => 200]);
                }
            }
        } else if ($request->status == "1") {
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
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                    ];

                    if($request->id) {
                        EtpProgressNote::where(
                            ['id' => $request->id]
                        )->update($etpprogressnote);
                        return response()->json(["message" => "ETP progress note updated", "code" => 200]);
                    } else {
                        EtpProgressNote::firstOrCreate($etpprogressnote);
                        return response()->json(["message" => "ETP progress note created successfully", "code" => 200]);
                    }
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $EtpProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
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
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    if($request->id) {
                        EtpProgressNote::where(
                            ['id' => $request->id]
                        )->update($EtpProgress);
                        return response()->json(["message" => "ETP progress note updated", "code" => 200]);
                    } else {
                        EtpProgressNote::firstOrCreate($EtpProgress);
                        return response()->json(["message" => "ETP progress note created successfully", "code" => 200]);
                    }
                }
    }
}}
