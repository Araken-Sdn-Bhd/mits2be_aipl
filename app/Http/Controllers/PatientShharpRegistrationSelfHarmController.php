<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationSelfHarm;

class PatientShharpRegistrationSelfHarmController extends Controller
{
    public function registerselfharm(Request $request){
        $validator = Validator::make($request->all(), [
               'added_by' => 'required|integer',
               'date' => 'required',
               'time' => 'required',
               'patient_mrn_no' => 'required|integer',
               'place_occurence' => 'required|integer',
               'method_of_self_harm' => 'required|string',
               'patient_get_idea_about_method' => 'required|string',
               'specify_patient_actual_word' => '',
               'suicidal_intent' => 'required|string', 
               'overdose_poisoning' => '',
               'other' => '',
               'suicidal_intent_yes' => '',
               'suicidal_intent_other' =>''   
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           if($request->method_of_self_harm=='Overdose/Poisoning'){
            $validator = Validator::make($request->all(), [
                'overdose_poisoning' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }
        else if($request->method_of_self_harm=='other'){
            $validator = Validator::make($request->all(), [
                'other' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }
        else if($request->method_of_self_harm=='Overdose/Poisoning' && $request->method_of_self_harm=='other'){
            $validator = Validator::make($request->all(), [
                'overdose_poisoning' => 'required|integer',
                'other' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }

        else if($request->suicidal_intent=='Yes'){
            if($request->suicidal_intent_yes=='other'){
                $validator = Validator::make($request->all(), [
                    'suicidal_intent_other' => 'required|string'
                ]);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                }
                $module = [
                    'added_by' => $request->added_by,
                    'date' => $request->date,
                    'time' => $request->time,
                    'patient_mrn_no' => $request->patient_mrn_no,
                    'place_occurence' => $request->place_occurence,
                    'method_of_self_harm' => $request->method_of_self_harm,
                    'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                    'specify_patient_actual_word' => $request->specify_patient_actual_word,
                    'suicidal_intent' => $request->suicidal_intent,
                    'overdose_poisoning' => $request->overdose_poisoning,
                    'other' => $request->other,
                    'suicidal_intent_yes' => $request->suicidal_intent_yes,
                    'suicidal_intent_other' => $request->suicidal_intent_other,
                    'status' => "1"
                ];
                PatientShharpRegistrationSelfHarm::firstOrCreate($module);
                return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
            } 
            $validator = Validator::make($request->all(), [
                'suicidal_intent_yes' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }

        else if($request->suicidal_intent=='Yes' && $request->method_of_self_harm=='Overdose/Poisoning' && $request->method_of_self_harm=='other'){
            $validator = Validator::make($request->all(), [
                'suicidal_intent_yes' => 'required|string',
                'overdose_poisoning' => 'required|integer',
                'other' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }

        else if($request->suicidal_intent=='Yes'  && $request->method_of_self_harm=='other'){
            $validator = Validator::make($request->all(), [
                'suicidal_intent_yes' => 'required|string',
                'other' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }

        else if($request->suicidal_intent=='Yes' && $request->method_of_self_harm=='Overdose/Poisoning'){
            $validator = Validator::make($request->all(), [
                'suicidal_intent_yes' => 'required|string',
                'overdose_poisoning' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }
        else{
            $module = [
                'added_by' => $request->added_by,
                'date' => $request->date,
                'time' => $request->time,
                'patient_mrn_no' => $request->patient_mrn_no,
                'place_occurence' => $request->place_occurence,
                'method_of_self_harm' => $request->method_of_self_harm,
                'patient_get_idea_about_method' => $request->patient_get_idea_about_method,
                'specify_patient_actual_word' => $request->specify_patient_actual_word,
                'suicidal_intent' => $request->suicidal_intent,
                'overdose_poisoning' => $request->overdose_poisoning,
                'other' => $request->other,
                'suicidal_intent_yes' => $request->suicidal_intent_yes,
                'suicidal_intent_other' => $request->suicidal_intent_other,
                'status' => "1"
            ];
            PatientShharpRegistrationSelfHarm::firstOrCreate($module);
            return response()->json(["message" => "Self Harm Created Successfully!", "code" => 200]);
        }
       }
}
