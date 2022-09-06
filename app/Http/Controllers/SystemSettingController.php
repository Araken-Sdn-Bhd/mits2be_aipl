<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Validator;

class SystemSettingController extends Controller
{
    public function get_setting($section)
    {
        $ss = SystemSetting::where('section', $section)->get();
        return response()->json(["message" => "Fetched Successfully", "setting" => $ss, "code" => 200]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variable_name' => 'required|string',
            'variable_value' => 'required|string',
            'section' => 'required|string',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->section == 'password-characteristic' or $request->section == 'default-password' or $request->section == 'email-setting') {
            
            $prop = explode(',', $request->variable_name);
            $prop_val = explode(',', $request->variable_value);
            $prop_status = explode(',', $request->status);
            foreach ($prop as $key => $val) {
                $ss = SystemSetting::updateOrCreate(
                    ['section' => $request->section, 'variable_name' => $val],
                    [
                        'variable_name' => $val,
                        'variable_value' => $prop_val[$key],
                        'status' =>  $prop_status[$key]
                    ]
                );
            }
            return response()->json(["message" => "Setting has updated successfully", "code" => 200]);
        } else {
            
            $ss = SystemSetting::updateOrCreate(
                ['section' => $request->section],
                [
                    'variable_name' => $request->variable_name,
                    'variable_value' => $request->variable_value,
                    'status' => $request->status
                ]
            );
            if ($ss)
                return response()->json(["message" => "Setting has updated successfully", "code" => 200]);
        }
    }
}
