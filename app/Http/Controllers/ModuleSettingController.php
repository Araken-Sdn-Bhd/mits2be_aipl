<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModuleSettings;
use Validator;

class ModuleSettingController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|integer',
            'sub_module_id' => 'required|integer',
            'sub_module_1_id' => 'required|integer',
            'sub_module_2_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $moduleSetting =  ModuleSettings::where($request->only('module_id', 'sub_module_id', 'sub_module_1_id', 'sub_module_2_id'))->get();
        return response()->json([
            'message' => 'Module Setting',
            'setting' => $moduleSetting
        ], 201);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'module_setting_id' => 'required|integer',
            'module_id' => 'required|integer',
            'sub_module_id' => 'required|integer',
            'sub_module_1_id' => 'required|integer',
            'sub_module_2_id' => 'required|integer',
            'setting' => 'required|json',
            'status' => 'required|integer',
            'added_by' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->module_setting_id != 0) {
            $moduleSetting =  ModuleSettings::find($request->module_setting_id);
            $moduleSetting->update($request->only('module_id', 'sub_module_id', 'sub_module_1_id', 'sub_module_2_id', 'setting', 'status', 'added_by'));
        } else {
            ModuleSettings::create($request->except(['module_setting_id']));
        }
        return response()->json([
            'message' => 'Module Setting Updated',
        ], 200);
    }
}
