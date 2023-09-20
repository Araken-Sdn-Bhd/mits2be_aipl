<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JobClubProgressNote;
use App\Models\UserDiagnosis;
class JobClubProgressNoteController extends Controller
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
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $jobclubprogressnote = [
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
                        'add_diagnosis_type' => str_replace('"',"", $request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];
                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($jobclubprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $jobclubprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];
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
                    
                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                }else{
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' => str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                }
            } else {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $jobclubprogressnote = [
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];
                    $user_diagnosis = [
                        'app_id' => $request->appId,
                        'patient_id' =>  $request->patient_mrn_id,
                        'diagnosis_id' =>  $request->diagnosis_type,
                        'add_diagnosis_id' => str_replace('"',"",$request->add_diagnosis_type),
                        'outcome_id' =>  $request->outcome,
                        'category_services' =>  $request->service_category,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                    UserDiagnosis::create($user_diagnosis);
                    try {
                        $HOD = JobClubProgressNote::firstOrCreate($jobclubprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $jobclubprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully00", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];
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
                    try {
                        $HOD = JobClubProgressNote::firstOrCreate($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                }else{
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                }
            }
        } else if ($request->status == 0) {
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

                    $jobclubprogressnote = [
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];
                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($jobclubprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $jobclubprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                } else {
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        JobClubProgressNote::where(
                            ['id' => $request->id]
                        )->update($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                }
            } else {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

                    $jobclubprogressnote = [
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];
                    try {
                        $HOD = JobClubProgressNote::firstOrCreate($jobclubprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $jobclubprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = JobClubProgressNote::firstOrCreate($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully Save", "code" => 200]);
                } else {
                    $JobClubProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  str_replace('"',"",$request->sub_code_id),
                        'add_code_id' =>  $request->add_code_id,
                        'add_sub_code_id' =>  str_replace('"',"",$request->add_sub_code_id),
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
                        'add_diagnosis_type' =>  str_replace('"',"",$request->add_diagnosis_type),
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = JobClubProgressNote::firstOrCreate($JobClubProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'JobClubProgress' => $JobClubProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Job Club Progress Note Successfully save", "code" => 200]);
                }
            }
        }
    }
}
