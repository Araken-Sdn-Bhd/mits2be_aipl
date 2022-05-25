<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientCbiOnlineTest;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientCbiOnlineTestController extends Controller
{
    public function store(Request $request)
    {
        if ($request->Type == 'Personal Burnout' || $request->Type == 'Work Burnout' || $request->Type == 'Client/Customer Burnout') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'Answer4' => 'required|integer',
                'Answer5' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'DASS') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|integer',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test Dass Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'PHQ-9') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|integer',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test PHQ 9 Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'Understanding & Communication' || $request->Type == 'GA' || $request->Type == 'SC' || $request->Type == 'GAWP' || $request->Type == 'LA-H' || $request->Type == 'LA-S/W' || $request->Type == 'PIS') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'Answer4' => 'required|integer',
                'Answer5' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test WHODAS Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'BDI') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|string',
                'Answer1' => 'required|string',
                'Answer2' => 'required|string',
                'Answer3' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test BDI Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'BAI') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|integer',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test BAI Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'ATQ') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'Answer4' => 'required|integer',
                'Answer5' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test ATQ Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'PSP') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                // 'Answer0' => 'required|string',
                // 'Answer1' => 'required|string',
                // 'Answer2' => 'required|string',
                // 'Answer3' => 'required|string',
                // 'Answer4' => 'required|string',
                // 'Answer5' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                // 'Answer0' => $request->Answer0,
                // 'Answer1' => $request->Answer1,
                // 'Answer2' => $request->Answer2,
                // 'Answer3' => $request->Answer3,
                // 'Answer4' => $request->Answer4,
                // 'Answer5' => $request->Answer5,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test PSP Created Successfully!", "code" => 200]);
        } else if ($request->Type == 'Suicidal Scale') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|string',
                'Answer1' => 'required|string',
                'Answer2' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $module = [
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'status' => "1"
            ];
            PatientCbiOnlineTest::firstOrCreate($module);
            return response()->json(["message" => "Test Suicidal Scale Created Successfully!", "code" => 200]);
        }

        return response()->json(["message" => "TYPE does not match", "code" => 200]);
    }

    public function getPatientOnlineSelfTestList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientCbiOnlineTest::select('id', 'added_by', 'Type', 'status', 'Question', 'question_ml', 'Answer0', 'Answer1', 'Answer2', 'Answer3', 'Answer4', 'Answer5', 'question_order', 'status')
            ->where('status', '=', '1')
            ->where('Type', '=', $request->Type)
            ->orderBy('question_order', 'asc')
            ->get();
        return response()->json(["message" => "Test List", 'list' => $list, "code" => 200]);
    }


    public function update(Request $request)
    {
        if ($request->Type == 'Personal Burnout' || $request->Type == 'Work Burnout' || $request->Type == 'Client/Customer Burnout') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'Answer4' => 'required|integer',
                'Answer5' => 'required|integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'DASS') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|integer',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'PHQ-9') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'required|integer',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test PHQ-9 Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'Understanding & Communication' || $request->Type == 'GA' || $request->Type == 'SC' || $request->Type == 'GAWP' || $request->Type == 'LA-H' || $request->Type == 'LA-S/W' || $request->Type == 'PIS') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'required|integer',
                'Answer2' => 'required|integer',
                'Answer3' => 'required|integer',
                'Answer4' => 'required|integer',
                'Answer5' => 'required|integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'BDI') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'string',
                'Answer1' => 'string',
                'Answer2' => 'string',
                'Answer3' => 'string',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test BDI Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'BAI') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'integer',
                'Answer1' => 'integer',
                'Answer2' => 'integer',
                'Answer3' => 'integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test BAI Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'ATQ') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer1' => 'integer',
                'Answer2' => 'integer',
                'Answer3' => 'integer',
                'Answer4' => 'integer',
                'Answer5' => 'integer',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test ATQ Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'PSP') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'string',
                'Answer1' => 'string',
                'Answer2' => 'string',
                'Answer3' => 'string',
                'Answer4' => 'string',
                'Answer5' => 'string',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'Answer3' => $request->Answer3,
                'Answer4' => $request->Answer4,
                'Answer5' => $request->Answer5,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test PSP Updated Successfully!", "code" => 200]);
        } else if ($request->Type == 'Suicidal Scale') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'Type' => 'required|string',
                'Question' => 'required|string',
                'Answer0' => 'string',
                'Answer1' => 'string',
                'Answer2' => 'string',
                'id' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }

            PatientCbiOnlineTest::where(
                ['id' => $request->id]
            )->where(['Type' => $request->Type])->update([
                'added_by' => $request->added_by,
                'Type' => $request->Type,
                'Question' => $request->Question,
                'Answer0' => $request->Answer0,
                'Answer1' => $request->Answer1,
                'Answer2' => $request->Answer2,
                'status' => "1"
            ]);

            return response()->json(["message" => "Test Suicidal Scale Updated Successfully!", "code" => 200]);
        }

        return response()->json(["message" => "TYPE does not match", "code" => 200]);
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'Type' => 'required|string',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        PatientCbiOnlineTest::where(
            ['id' => $request->id]
        )->where(['Type' => $request->Type])->update([
            'status' => "0"
        ]);

        return response()->json(["message" => "Test Deleted Successfully!", "code" => 200]);
    }
}
