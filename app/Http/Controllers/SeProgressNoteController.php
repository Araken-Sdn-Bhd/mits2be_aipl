<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SeProgressNote;

class SeProgressNoteController extends Controller
{
    //

    public function GetActivityList()
    {
        $list = SeProgressNote::select('id', 'activity_type')
            ->get();
        return response()->json(["message" => "Se Progress Activity List", 'list' => $list, "code" => 200]);
    }

    public function GetSENamelist()
    {
        $list = SeProgressNote::select('id', 'staff_name', 'employment_status')
            ->get();
        return response()->json(["message" => "Se Progress Note Stafflist", 'list' => $list, "code" => 200]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'appId' => '',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->status == 1) {
            if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $seprogressnote = [
                    'services_id' =>  $request->services_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'employment_status' =>  $request->employment_status,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "1",
                    'appointment_details_id' => $request->appId
                ];

                try {
                    $HOD = SeProgressNote::create($seprogressnote);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                }
                return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
            } else if ($request->service_category == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $SeProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "1",
                    'appointment_details_id' => $request->appId
                ];

                try {
                    $HOD = SeProgressNote::create($SeProgress);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                }
                return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
            }
        } else if ($request->status == 0) {
            if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                $validator = Validator::make($request->all(), [
                    'services_id' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $seprogressnote = [
                    'services_id' =>  $request->services_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'employment_status' =>  $request->employment_status,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "0",
                    'appointment_details_id' => $request->appId
                ];

                try {
                    $HOD = SeProgressNote::create($seprogressnote);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                }
                return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
            } else if ($request->service_category == 'clinical-work') {
                $validator = Validator::make($request->all(), [
                    'code_id' => 'required|integer',
                    'sub_code_id' => 'required|integer'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }

                $SeProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $request->sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'progress_note' =>  $request->progress_note,
                    'management_plan' =>  $request->management_plan,
                    'location_service' =>  $request->location_service,
                    'diagnosis_type' =>  $request->diagnosis_type,
                    'service_category' =>  $request->service_category,
                    'complexity_service' =>  $request->complexity_service,
                    'outcome' =>  $request->outcome,
                    'medication' =>  $request->medication,
                    'status' => "0",
                    'appointment_details_id' => $request->appId
                ];

                try {
                    $HOD = SeProgressNote::create($SeProgress);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                }
                return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
            }
        }
    }
}
