<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PsychiatricProgressNote;
use DateTime;
use DateTimeZone;

class PsychiatricProgressNoteController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required',
            'patient_mrn_id' => 'required|integer',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

        if ($request->status == "0") {
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                        $psychiatryprogressnote = [
                            'services_id' =>  $request->services_id,
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_mrn_id,
                            'diagnosis' =>  $request->diagnosis,
                            'clinical_notes' =>  $request->clinical_notes,
                            'management' =>  $request->management,
                            'location_services_id' =>  $request->location_services_id,
                            'type_diagnosis_id' =>  $request->type_diagnosis_id,
                            'category_services' =>  $request->category_services,
                            'complexity_services_id' =>  $request->complexity_services_id,
                            'outcome_id' =>  $request->outcome_id,
                            'medication_des' =>  $request->medication_des,
                            'status' => "0",
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'appointment_details_id' => $request->appId,
                            'additional_diagnosis' => $additional_diagnosis
                        ];
                        if($request->id) {
                            PsychiatricProgressNote::where(
                                        ['id' => $request->id]
                                    )->update($psychiatryprogressnote);
                                    return response()->json(["message" => "Psychiatry progress note updated", "code" => 200]);
                        } else {
                            PsychiatricProgressNote::create($psychiatryprogressnote);
                            return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
                    }
                } else if ($request->category_services == 'clinical-work') {
                        $psychiatryprogressnote = [
                            'code_id' =>  $request->code_id,
                            'sub_code_id' =>  $sub_code_id,
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_mrn_id,
                            'diagnosis' =>  $request->diagnosis,
                            'clinical_notes' =>  $request->clinical_notes,
                            'management' =>  $request->management,
                            'location_services_id' =>  $request->location_services_id,
                            'type_diagnosis_id' =>  $request->type_diagnosis_id,
                            'category_services' =>  $request->category_services,
                            'complexity_services_id' =>  $request->complexity_services_id,
                            'outcome_id' =>  $request->outcome_id,
                            'medication_des' =>  $request->medication_des,
                            'status' => "0",
                            'appointment_details_id' => $request->appId,
                            'additional_diagnosis' => $additional_diagnosis,
                            'additional_code_id'=> $request->additional_code_id,
                            'additional_subcode' => $additional_subcode,
                        ];

                        if($request->id) {
                            PsychiatricProgressNote::where(
                                        ['id' => $request->id]
                                    )->update($psychiatryprogressnote);
                                    return response()->json(["message" => "Psychiatry progress note updated", "code" => 200]);
                        } else {
                            PsychiatricProgressNote::create($psychiatryprogressnote);
                            return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
                    }
                }
            }

            else if ($request->status == "1") {
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                        $psychiatryprogressnote = [
                            'services_id' =>  $request->services_id,
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_mrn_id,
                            'diagnosis' =>  $request->diagnosis,
                            'clinical_notes' =>  $request->clinical_notes,
                            'management' =>  $request->management,
                            'location_services_id' =>  $request->location_services_id,
                            'type_diagnosis_id' =>  $request->type_diagnosis_id,
                            'category_services' =>  $request->category_services,
                            'complexity_services_id' =>  $request->complexity_services_id,
                            'outcome_id' =>  $request->outcome_id,
                            'medication_des' =>  $request->medication_des,
                            'status' => "1",
                            'created_at' => $date->format('Y-m-d H:i:s'),
                            'appointment_details_id' => $request->appId,
                            'additional_diagnosis' => $additional_diagnosis
                        ];
                        if($request->id) {
                            PsychiatricProgressNote::where(
                                        ['id' => $request->id]
                                    )->update($psychiatryprogressnote);
                                    return response()->json(["message" => "Psychiatry progress note updated", "code" => 200]);
                        } else {
                            PsychiatricProgressNote::create($psychiatryprogressnote);
                            return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
                    }
                } else if ($request->category_services == 'clinical-work') {
                        $psychiatryprogressnote = [
                            'code_id' =>  $request->code_id,
                            'sub_code_id' =>  $sub_code_id,
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_mrn_id,
                            'diagnosis' =>  $request->diagnosis,
                            'clinical_notes' =>  $request->clinical_notes,
                            'management' =>  $request->management,
                            'location_services_id' =>  $request->location_services_id,
                            'type_diagnosis_id' =>  $request->type_diagnosis_id,
                            'category_services' =>  $request->category_services,
                            'complexity_services_id' =>  $request->complexity_services_id,
                            'outcome_id' =>  $request->outcome_id,
                            'medication_des' =>  $request->medication_des,
                            'status' => "1",
                            'appointment_details_id' => $request->appId,
                            'additional_diagnosis' => $additional_diagnosis,
                            'additional_code_id'=> $request->additional_code_id,
                            'additional_subcode' => $additional_subcode,
                        ];

                        if($request->id) {
                            PsychiatricProgressNote::where(
                                        ['id' => $request->id]
                                    )->update($psychiatryprogressnote);
                                    return response()->json(["message" => "Psychiatry progress note updated", "code" => 200]);
                        } else {
                            PsychiatricProgressNote::create($psychiatryprogressnote);
                            return response()->json(["message" => "Psychiatry progress note", "code" => 200]);
                    }
                }
            }
    }
}
