<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\GeneralSetting;

class GeneralSettingController extends Controller
{
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'section' => 'required|string',
            'section_value' => 'required|string',
            'section_order' => 'required|integer',
            'request_type' => 'required|string',
            'status' =>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->request_type != 'update') {
            if (GeneralSetting::where(['section' => $request->section, 'section_value' => $request->section_value])
            ->where('status', '!=', 1)->count() == 0) {
                GeneralSetting::create(
                    [
                        'section' => $request->section,
                        'section_value' => $request->section_value,
                        'section_order' =>  $request->section_order,
                        'added_by' => $request->added_by,
                        'status' => $request->status,
                    ]
                );
                return response()->json(["message" => "Setting has updated successfully", "code" => 200]);
            } else {
                return response()->json(["message" => "Value Already Exists!", "code" => 200]);
            }
        } else if ($request->request_type == 'update') {
            GeneralSetting::where(
                ['id' => $request->setting_id]
            )->update([
                'section' => $request->section,
                'section_value' => $request->section_value,
                'section_order' =>  $request->section_order,
                'added_by' => $request->added_by,
                'status' => $request->status,
            ]);
            return response()->json(["message" => "Setting has updated successfully", "code" => 200]);
        }
    }

    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = GeneralSetting::select('id', 'section', 'section_value', 'section_order','code', 'status')
        ->where('section', $request->section)->where('status', '1')->orderBy('section_order', 'asc')->get();
        return response()->json(["message" => $request->section . " List", 'list' => $list, "code" => 200]);
    }

    public function shharpEmpList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = GeneralSetting::select('*')
        ->where('section', $request->section)->where('status', '1')->where('section_value', 'not like', '%Others%')->orderBy('section_order', 'asc')->get();
        return response()->json(["message" => $request->section . " List", 'list' => $list, "code" => 200]);
    }

    public function getListSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = GeneralSetting::select('id', 'section', 'section_value', 'section_order','code', 'status')
        ->where('section', $request->section)->orderBy('section_value', 'asc')->get();
        return response()->json(["message" => $request->section . " List", 'list' => $list, "code" => 200]);
    }

    public function getSettingById(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'setting_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = GeneralSetting::select('id', 'section', 'section_value', 'section_order', 'status','code')->where(['id' => $request->setting_id])->get();
        return response()->json(["message" => " Setting Record", 'setting' => $list, "code" => 200]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'setting_id' => 'required|integer',
            'added_by' => 'required|integer',
            'section' => 'required|string',
            'section_value' => 'required|string',
            'section_order' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if (GeneralSetting::where(['section' => $request->section, 'section_value' => $request->section_value])->where('id', '!=', $request->setting_id)->count() == 0) {
            GeneralSetting::where(
                ['id' => $request->setting_id]
            )->update([
                'section' => $request->section,
                'section_value' => $request->section_value,
                'section_order' =>  $request->section_order,
                'added_by' => $request->added_by
            ]);


            return response()->json(["message" => "Setting has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->section_value . " already exists", "code" => 200]);
        }
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'setting_id' => 'required|integer',
            'added_by' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        GeneralSetting::where(
            ['id' => $request->setting_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }
}
