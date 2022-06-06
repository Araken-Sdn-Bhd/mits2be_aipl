<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\SharpRegistrationFinalStep;

class PatientShharpRegistrationHospitalManagementController extends Controller
{
    public function addHospitalManagemnt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'referral_or_contact' => 'required|integer',
            'referral_or_contact_other' => '',
            'arrival_mode' => 'required|integer',
            'arrival_mode_other' => '',
            'date' => 'required',
            'time' => 'required',
            'physical_consequences' => 'required|string',
            'physical_consequences_des' => '',
            'patient_admitted' => 'required|string',
            'patient_admitted_des' => '',
            'discharge_status' => 'required',
            'discharge_date' => 'required',
            'discharge_number_days_in_ward' => 'required',
            'main_psychiatric_diagnosis' => 'required|integer',
            'external_cause_inquiry' => 'required|integer',
            'discharge_psy_mx' => 'required|string',
            'discharge_psy_mx_des' => '',
            'sharp_register_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $sri = $request->sharp_register_id;
        $riskArray = 0;
        if ($sri != 0) {
            $self_harm = SharpRegistrationFinalStep::where('id', $sri)->get()->pluck('hospital_mgmt')->toArray();
            if ($self_harm[0] != '')
                $riskArray = $self_harm[0];
        }
        //dd($riskArray);
        $module = [];
        if ($riskArray == 0) {
            $module = [
                'added_by' => $request->added_by,
                'patient_mrn_no' => $request->patient_id,
                'referral_or_contact' => $request->referral_or_contact,
                'arrival_mode' => $request->arrival_mode,
                'referral_or_contact_other' => $request->referral_or_contact_other,
                'arrival_mode_other' => $request->arrival_mode_other,
                'date' => $request->date,
                'time' => $request->time,
                'physical_consequences' => $request->physical_consequences,
                'patient_admitted' => $request->patient_admitted,
                'discharge_status' => $request->discharge_status,
                'discharge_date' => $request->discharge_date,
                'discharge_number_days_in_ward' => $request->discharge_number_days_in_ward,
                'main_psychiatric_diagnosis' => $request->main_psychiatric_diagnosis,
                'external_cause_inquiry' => $request->external_cause_inquiry,
                'discharge_psy_mx' => $request->discharge_psy_mx,
                'status' => "1"
            ];
            if ($request->physical_consequences == 'Aborted') {
                $validator = Validator::make($request->all(), [
                    'physical_consequences_des' => 'required|string'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $module['physical_consequences_des'] = $request->physical_consequences_des;
            } else  if ($request->patient_admitted == 'Yes') {
                $validator = Validator::make($request->all(), [
                    'patient_admitted_des' => 'required|string'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $module['patient_admitted_des'] = $request->patient_admitted_des;
            } else  if ($request->discharge_psy_mx == 'Other') {
                $validator = Validator::make($request->all(), [
                    'discharge_psy_mx_des' => 'required|string'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $module['discharge_psy_mx_des'] = $request->discharge_psy_mx_des;
            }
        } else {
            PatientShharpRegistrationHospitalManagement::where(['id' => $riskArray])->update($module);
        }

        try {
            if ($riskArray == 0) {
                DB::beginTransaction();
                $suicideRiskId = PatientShharpRegistrationHospitalManagement::create($module);
                DB::commit();
                $insertID = 0;
                if ($sri == 0) {
                    $id = SharpRegistrationFinalStep::create([
                        'added_by' => $request->added_by, 'patient_id' => $request->patient_id,
                        'risk' => '',
                        'protective' => '',
                        'self_harm' => '',
                        'suicide_risk' => '',
                        'hospital_mgmt' => $suicideRiskId->id,
                        'status' => '0',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $insertID = $id->id;
                    return response()->json(["message" => "Data Inserted Successfully!", 'id' => $insertID, "code" => 201]);
                } else {
                    SharpRegistrationFinalStep::where('id', $sri)->update(['hospital_mgmt' => $suicideRiskId->id]);
                    return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
                }
            } else {
                return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage(), 'Data' => $module, "code" => 400]);
        }
    }
}
