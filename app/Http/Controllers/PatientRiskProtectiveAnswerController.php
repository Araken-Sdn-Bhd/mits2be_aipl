<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\SharpRegistrationSuicideRiskResult;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientShharpRegistrationDataProducer;
use App\Models\PatientShharpRegistrationRiskProtective;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;

class PatientRiskProtectiveAnswerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => '',
            'added_by' => 'required|integer',
            'result' => '',
            'sharp_register_id' => 'required|integer',
            'risk_factor_yes_value' => ''
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $sri = $request->sharp_register_id;

        $result = json_decode($request->result, true);
        $riskArray = [];
        if ($sri != 0) {
            $risk = SharpRegistrationFinalStep::where('id', $sri)->get()->pluck('risk')->toArray();
            if(count($risk)>0 && $risk[0] != '')
                $riskArray = explode('^', $risk[0]);
        }
        $insertArray = [];
        if ($result) {
            foreach ($result[0] as $key => $val) {
                $txt = '';
                if ($val == '1') {
                    $answer = 'Yes';
                }else if($val == '2'){
                    $answer = '';
                }else{
                $answer = 'No';
                }
                if (count($riskArray) == 0) {
                    $insertArray[] = [
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_id,
                        'factor_type' => 'risk',
                        'QuestionId' => $key,
                        'Answer' => $answer,
                        'Answer_text' => $txt,
                        'status' => $request->status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'risk_factor_yes_value' => $request->risk_factor_yes_value
                    ];
                } else {
                    if(array_key_exists($key-1,$riskArray)){
                    PatientRiskProtectiveAnswer::where(['id' => $riskArray[$key - 1]])->update([
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_id,
                        'factor_type' => 'risk',
                        'QuestionId' => $key,
                        'Answer' => $answer,
                        'Answer_text' => $txt,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'risk_factor_yes_value' => $request->risk_factor_yes_value
                    ]);
                }else{
                    $table=PatientRiskProtectiveAnswer::insert([
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_id,
                        'factor_type' => 'risk',
                        'QuestionId' => $key,
                        'Answer' => $answer,
                        'Answer_text' => $txt,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'risk_factor_yes_value' => $request->risk_factor_yes_value
                    ]);

                }
                }
            }
        }
        try {
            if (count($riskArray) == 0) {
                DB::beginTransaction();
                $lastIdBeforeInsertion = (PatientRiskProtectiveAnswer::all()->last()) ? PatientRiskProtectiveAnswer::all()->last()->id : 0;
                PatientRiskProtectiveAnswer::insert($insertArray);
                $insertedIds = [];
                for ($i = 1; $i <= count($insertArray); $i++)
                    array_push($insertedIds, $lastIdBeforeInsertion + $i);

                DB::commit();
                $insertID = 0;
                if ($sri == 0) {
                    $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    $id = SharpRegistrationFinalStep::create([
                        'added_by' => $request->added_by, 'patient_id' => $request->patient_id,
                        'risk' => implode('^', $insertedIds),
                        'protective' => '',
                        'self_harm' => '',
                        'suicide_risk' => '',
                        'hospital_mgmt' => '',
                        'status' =>  $request->status,
                        'created_at' => $date->format('Y-m-d H:i:s'),
                    ]);
                    $insertID = $id->id;
                    return response()->json(["message" => "Data Inserted Successfully!", 'id' => $insertID, "code" => 201]);
                } else {
                    SharpRegistrationFinalStep::where('id', $sri)->update(['risk' => implode('^', $insertedIds)]);
                    return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
                }
            } else {
                return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage(), 'Data' => $insertArray, "code" => 400]);
        }
    }

    public function storeProtective(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => '',
            'added_by' => 'required|integer',
            'result' => '',
            'sharp_register_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $sri = $request->sharp_register_id;
        $result = json_decode($request->result, true);
        $preIds = [];
        if ($sri != 0) {
            $risk = SharpRegistrationFinalStep::where('id', $sri)->get()->pluck('protective')->toArray();
            if ($risk[0] != '')
                $preIds = explode('^', $risk[0]);
        }
        $insertArray = [];
        if ($result) {
            foreach ($result[0] as $key => $val) {
                $txt = '';
                if ($val == '1') {
                    $answer = 'Yes';
                }else if($val == '2'){
                    $answer = '';
                }else{
                    $answer = 'No';
                }
                if (in_array($key, [1, 3, 4, 6, 7, 8, 10])) {
                    if ($val != '0') {
                        if ($request->factor_type == 'risk') {
                            $txt = $val;
                        }
                    }
                }
                if (count($preIds) == 0) {
                    $insertArray[] = [
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_id,
                        'factor_type' => 'protective',
                        'QuestionId' => $key,
                        'Answer' => $answer,
                        'Answer_text' => $txt,
                        'status' =>  $request->status,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                } else {
                    if(array_key_exists($key-13,$preIds)){
                    PatientRiskProtectiveAnswer::where(['id' => $preIds[$key-13]])->update([
                        'added_by' => $request->added_by,
                        'patient_mrn_id' => $request->patient_id,
                        'factor_type' => 'protective',
                        'QuestionId' => $key,
                        'Answer' => $answer,
                        'Answer_text' => $txt,
                        'status' =>  $request->status,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                }
            }
        }
        try {
            if (count($preIds) == 0) {
                DB::beginTransaction();
                $lastIdBeforeInsertion = (PatientRiskProtectiveAnswer::all()->last()) ? PatientRiskProtectiveAnswer::all()->last()->id : 0;
                PatientRiskProtectiveAnswer::insert($insertArray);
                $insertedIds = [];
                for ($i = 1; $i <= count($insertArray); $i++)
                    array_push($insertedIds, $lastIdBeforeInsertion + $i);

                DB::commit();
                $insertID = 0;
                if ($sri == 0) {
                    $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    $id = SharpRegistrationFinalStep::create([
                        'added_by' => $request->added_by, 'patient_id' => $request->patient_id,
                        'risk' =>  '',
                        'protective' => implode('^', $insertedIds),
                        'self_harm' => '',
                        'suicide_risk' => '',
                        'hospital_mgmt' => '',
                        'status' =>  $request->status,
                        'created_at' => $date->format('Y-m-d H:i:s')
                    ]);
                    $insertID = $id->id;
                    return response()->json(["message" => "Data Inserted Successfully!", 'id' => $insertID, "code" => 201]);
                } else {
                    SharpRegistrationFinalStep::where('id', $sri)->update(['protective' => implode('^', $insertedIds)]);
                    return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
                }
            } else {
                return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage(), 'Data' => $insertArray, "code" => 400]);
        }
    }

    public function storeSelfHarmResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => '',
            'added_by' => 'required|integer',
            'result' => 'required|json',
            'sharp_register_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $sri = $request->sharp_register_id;

        $result = json_decode($request->result, true);
        $riskArray = [];
        if ($sri != 0) {
            $self_harm = SharpRegistrationFinalStep::where('id', $sri)->get()->pluck('self_harm')->toArray();
            if ($self_harm[0] != '')
                $riskArray = explode('^', $self_harm[0]);
        }
        $harmTime = '';
        $harmDate = '';
        $insertArray = [];

        if ($result) {
            foreach ($result as $key => $val) {
                if (array_keys($val)[0] === 'CURRENT SELF HARM ACT') {
                    $harmTime = $val['CURRENT SELF HARM ACT']['Time'];
                    $harmDate = $val['CURRENT SELF HARM ACT']['Date'];
                }
                if (count($riskArray) == 0) {
                    $insertArray[] = [
                        'added_by' => $request->added_by,
                        'patient_id' => $request->patient_id,
                        'section' => array_keys($val)[0],
                        'section_value' => json_encode($val),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                } else {
                    SharpRegistrationSelfHarmResult::where(['id' => $riskArray[$key]])->update([
                        'added_by' => $request->added_by,
                        'patient_id' => $request->patient_id,
                        'section' => array_keys($val)[0],
                        'section_value' => json_encode($val),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        try {
            if (count($riskArray) == 0) {
                DB::beginTransaction();
                $lastIdBeforeInsertion = (SharpRegistrationSelfHarmResult::all()->last()) ? SharpRegistrationSelfHarmResult::all()->last()->id : 0;
                SharpRegistrationSelfHarmResult::insert($insertArray);
                $insertedIds = [];
                for ($i = 1; $i <= count($insertArray); $i++)
                    array_push($insertedIds, $lastIdBeforeInsertion + $i);

                DB::commit();
                $insertID = 0;
                if ($sri == 0) {
                    $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    $id = SharpRegistrationFinalStep::create([
                        'added_by' => $request->added_by, 'patient_id' => $request->patient_id,
                        'risk' => '',
                        'protective' => '',
                        'self_harm' => implode('^', $insertedIds),
                        'harm_time' => $harmTime,
                        'harm_date' => $harmDate,
                        'suicide_risk' => '',
                        'hospital_mgmt' => '',
                        'status' =>  $request->status,
                        'created_at' => $date->format('Y-m-d H:i:s')
                    ]);

                    $insertID = $id->id;
                    return response()->json(["message" => "Data Inserted Successfully!", 'id' => $insertID, "code" => 201]);
                } else {
                    SharpRegistrationFinalStep::where('id', $sri)->update(['self_harm' => implode('^', $insertedIds), 'harm_time' => $harmTime, 'harm_date' => $harmDate]);
                    return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
                }
            } else {
                SharpRegistrationFinalStep::where('id', $sri)->update(['self_harm' => implode('^', $riskArray), 'harm_time' => $harmTime, 'harm_date' => $harmDate]);
                return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage(), 'Data' => $insertArray, "code" => 400]);
        }
    }

    public function storeSuicideRisk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => '',
            'added_by' => 'required|integer',
            'result' => '',
            'sharp_register_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $sri = $request->sharp_register_id;

        $riskArray = 0;
        if ($sri != 0) {
            $self_harm = SharpRegistrationFinalStep::where('id', $sri)->get()->pluck('suicide_risk')->toArray();
            if ($self_harm[0] != '')
                $riskArray = $self_harm[0];
        }
        $insertArray = [];


        if ($riskArray == 0) {
            $insertArray = [
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'result' => $request->result,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        } else {
            SharpRegistrationSuicideRiskResult::where(['id' => $riskArray])->update([
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'result' => $request->result,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        try {
            if ($riskArray == 0) {
                DB::beginTransaction();
                $suicideRiskId = SharpRegistrationSuicideRiskResult::create($insertArray);
                DB::commit();
                $insertID = 0;
                if ($sri == 0) {
                    $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    $id = SharpRegistrationFinalStep::create([
                        'added_by' => $request->added_by, 'patient_id' => $request->patient_id,
                        'risk' => '',
                        'protective' => '',
                        'self_harm' => '',
                        'suicide_risk' => $suicideRiskId->id,
                        'hospital_mgmt' => '',
                        'status' =>  $request->status,
                        'created_at' => $date->format('Y-m-d H:i:s')
                    ]);
                    $insertID = $id->id;
                    return response()->json(["message" => "Data Inserted Successfully!", 'id' => $insertID, "code" => 201]);
                } else {
                    SharpRegistrationFinalStep::where('id', $sri)->update(['suicide_risk' => $suicideRiskId->id]);
                    return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
                }
            } else {
                return response()->json(["message" => "Data Updated Successfully!", 'id' => $sri, "code" => 201]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage(), 'Data' => $insertArray, "code" => 400]);
        }
    }

    public function fetchList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $records = DB::table('sharp_registraion_final_step')
            ->join('patient_shharp_registration_data_producer', 'sharp_registraion_final_step.id', '=', 'patient_shharp_registration_data_producer.shharp_register_id')

            ->join('patient_risk_protective_answers', 'sharp_registraion_final_step.id', '=', 'patient_shharp_registration_data_producer.shharp_register_id')
            ->select(
                'sharp_registraion_final_step.id',
                DB::raw("DATE_FORMAT(sharp_registraion_final_step.created_at, '%d/%m/%Y') as Date"),
                DB::raw("DATE_FORMAT(sharp_registraion_final_step.created_at, '%H:%i') as Time"),
                'sharp_registraion_final_step.status',
                'patient_shharp_registration_data_producer.hospital_name',
                'patient_shharp_registration_data_producer.name_registering_officer'
            )
            ->where('sharp_registraion_final_step.patient_id', $request->patient_id)
            ->orderBy('sharp_registraion_final_step.id', 'desc')->groupBy('sharp_registraion_final_step.id',
            'sharp_registraion_final_step.created_at','patient_shharp_registration_data_producer.name_registering_officer',
            'sharp_registraion_final_step.status','patient_shharp_registration_data_producer.hospital_name')
            ->get();
        return response()->json(["message" => "List", 'Data' => $records, "code" => 200]);
    }

    public function fetchRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shharp_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $shharp = SharpRegistrationFinalStep::where('id', $request->shharp_id)->get()->toArray();
        $result = [];
        $riskArray = [];
        $protectiveArray = [];
        $selfharmArray = [];
        $suicideRiskArray = [];
        $hospitalArray = [];
        $dataSourceArray = [];
        if (count($shharp) > 0) {
            $risk = !empty($shharp[0]['risk']) ? explode('^', $shharp[0]['risk']) : [];
            $riskData = PatientRiskProtectiveAnswer::select('QuestionId', 'Answer', 'Answer_text', 'risk_factor_yes_value')->whereIn('id', $risk)->get()->toArray();
            if ($riskData) {
                foreach ($riskData as $k => $v) {
                    $ques = PatientShharpRegistrationRiskProtective::where('id', $v['QuestionId'])->get();
                    $riskArray[$k] = ['questionId' => $v['QuestionId'], 'Question_detail' => $ques, 'answer' => $v['Answer'], 'text' => $v['Answer_text'], 'risk_factor_yes_value' => $v['risk_factor_yes_value']];
                }
            }
            $protective = !empty($shharp[0]['protective']) ? explode('^', $shharp[0]['protective']) : [];
            $protectiveData = PatientRiskProtectiveAnswer::select('QuestionId', 'Answer', 'Answer_text')->whereIn('id', $protective)->get()->toArray();
            if ($protectiveData) {
                foreach ($protectiveData as $k => $v) {
                    $ques = PatientShharpRegistrationRiskProtective::where('id', $v['QuestionId'])->get();
                    $protectiveArray[$k] = ['questionId' => $v['QuestionId'], 'Question_detail' => $ques, 'answer' => $v['Answer'], 'text' => $v['Answer_text']];
                }
            }
            $selfharm = !empty($shharp[0]['self_harm']) ? explode('^', $shharp[0]['self_harm']) : [];
            $selfharmData = SharpRegistrationSelfHarmResult::select('section', 'section_value')->whereIn('id', $selfharm)->get()->toArray();
            if ($selfharmData) {
                foreach ($selfharmData as $k => $v) {

                    $jsonDecode = json_decode($v['section_value'], true);
                    if (array_key_exists('CURRENT SELF HARM ACT', $jsonDecode)) {
                        // dd($jsonDecode['CURRENT SELF HARM ACT']);
                        $jsonDecode['CURRENT SELF HARM ACT']['Place_of_Occurance'] = $jsonDecode['CURRENT SELF HARM ACT']['Place of Occurance'];
                    }
                    if (array_key_exists('Method of Self-Harm', $jsonDecode)) {
                        // dd($jsonDecode['Method of Self-Harm']);
                        $jsonDecode['Method of Self-Harm']['Firearms_or_explosives'] = $jsonDecode['Method of Self-Harm']['Firearms or explosives'];
                        $jsonDecode['Method of Self-Harm']['Cutting_or_Piercing'] = $jsonDecode['Method of Self-Harm']['Cutting or Piercing'];
                        $jsonDecode['Method of Self-Harm']['Jumping_from_height'] = $jsonDecode['Method of Self-Harm']['Jumping from height'];
                        $jsonDecode['Method of Self-Harm']['Overdose_Poisoning'] = $jsonDecode['Method of Self-Harm']['Overdose/Poisoning'];
                        $jsonDecode['Method of Self-Harm']['Hanging_Suffocation'] = $jsonDecode['Method of Self-Harm']['Hanging/Suffocation'];
                        $jsonDecode['Method of Self-Harm']['Fire_flames'] = $jsonDecode['Method of Self-Harm']['Fire/flames'];
                        $jsonDecode['Method of Self-Harm']['Other_'] = $jsonDecode['Method of Self-Harm']['Other'];
                    }
                    if (array_key_exists('How did Patient Get Idea about Method', $jsonDecode)) {
                        $jsonDecode['How did Patient Get Idea about Method']['Family_friends_peer_group'] = $jsonDecode['How did Patient Get Idea about Method']['Family, friends, peer group'];
                        $jsonDecode['How did Patient Get Idea about Method']['Internet_website_social_media_platform_app_blogs_forum_video_photosharing'] = $jsonDecode['How did Patient Get Idea about Method']['Internet (website, social media platform, app, blogs, forum, video/photosharing)'];
                        $jsonDecode['How did Patient Get Idea about Method']['Printed_media_newspaper_books_magazine_etc'] = $jsonDecode['How did Patient Get Idea about Method']['Printed media (newspaper, books, magazine, etc)'];
                        $jsonDecode['How did Patient Get Idea about Method']['Broadcast_media_television_radio'] = $jsonDecode['How did Patient Get Idea about Method']['Broadcast media (television, radio)'];
                        $jsonDecode['How did Patient Get Idea about Method']['Own_ideas'] = $jsonDecode['How did Patient Get Idea about Method']['Own ideas'];
                        $jsonDecode['How did Patient Get Idea about Method']['Specify_patient_actual_words'] = $jsonDecode['How did Patient Get Idea about Method']['Specify patient actual words'];
                    }

                    $selfharmArray[$k] = ['section' => $v['section'], 'section_value' => $jsonDecode[$v['section']]];
                }
            }
            $suicide_risk = !empty($shharp[0]['suicide_risk']) ? explode('^', $shharp[0]['suicide_risk']) : [];
            $suicide_riskData = SharpRegistrationSuicideRiskResult::select('result')->whereIn('id', $suicide_risk)->get()->toArray();
            if ($suicide_riskData) {
                foreach ($suicide_riskData as $k => $v) {
                    $suicideRiskArray[$k] = ['result' => $v['result']];
                }
            }
            $hospital = !empty($shharp[0]['hospital_mgmt']) ? explode('^', $shharp[0]['hospital_mgmt']) : [];
            $hospitalData = PatientShharpRegistrationHospitalManagement::select('*')->whereIn('id', $hospital)->get()->toArray();
            // dd($hospitalArray);
            if ($hospitalData) {
                foreach ($hospitalData as $k => $v) {
                    $hospitalArray[$k] = $v;
                }
            }
            $dataSource = PatientShharpRegistrationDataProducer::where('shharp_register_id', $request->shharp_id)->get()->toArray();
            if ($dataSource) {
                foreach ($dataSource as $k => $v) {
                    $dataSourceArray[$k] = $v;
                }
            }
        }
        $result = ['risk' => $riskArray, 'protective' => $protectiveArray, 'selfharm' => $selfharmArray, 'suicideRisk' => $suicideRiskArray, 'hospital' => $hospitalArray, 'dataSource' => $dataSourceArray];
        return response()->json(["message" => "Data!", 'result' => $result, "code" => 200]);
    }
}
