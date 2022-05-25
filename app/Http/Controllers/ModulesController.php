<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modules;
use Validator;

class ModulesController extends Controller
{
    public function index()
    {
        $modules = Modules::with(['children' => function ($query) {
            $query->select('id', 'module_parent_id', 'module_name', 'module_type', 'module_code');
        }])->where(["module_parent_id" => 0])->get();
        return response()->json(["modules" => $modules, "code" => 200]);
    }

    public function check_point($module_name, $module_id, $parent_id)
    {
        if ($module_id == 0)
            return Modules::where(['module_name' => $module_name, 'module_parent_id' => $parent_id])->get()->count();
        else
            return Modules::where(['module_name' => $module_name, 'module_parent_id' => $parent_id])->where('id', '!=', $module_id)->get()->count();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'name' => 'required',
            'type' => 'required',
            'code' => 'required',
            'status' => 'required|integer',
            'parent_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $modules = $this->check_point($request->name, 0, $request->parent_id);
        if ($modules > 0) {
            return response()->json(["message" => "Module already exists for this parent.", "code" => 200]);
        }

        $module = new Modules;
        $module->module_parent_id =  $request->parent_id;
        $module->module_name = $request->name;
        $module->module_type = $request->type;
        $module->module_code = $request->code;
        $module->status = $request->status;
        $module->added_by = $request->user_id;
        $module->save();
        return response()->json(["message" => "A new module has been added into the system", "code" => 200]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|integer',
            'user_id' => 'required|integer',
            'name' => 'required',
            'type' => 'required',
            'code' => 'required',
            'status' => 'required|integer',
            'parent_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $modules = $this->check_point($request->name, $request->module_id, $request->parent_id);
        if ($modules > 0) {
            return response()->json(["message" => "Module already exists for this parent.", "code" => 200]);
        }

        $module = Modules::find($request->module_id);
        $module->module_parent_id =  $request->parent_id;
        $module->module_name = $request->name;
        $module->module_type = $request->type;
        $module->module_code = $request->code;
        $module->status = $request->status;
        $module->added_by = $request->user_id;
        $module->save();
        return response()->json(["message" => "Module has been updated", "code" => 200]);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|integer',
            'user_id' => 'required|integer',
            'status' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $module = Modules::find($request->module_id);
        $module->status = $request->status;
        $module->added_by = $request->user_id;
        $module->save();
        return response()->json(["message" => "Module has been removed", "code" => 200]);
    }

    public function get_child_from_type($type)
    {
        if ($type) {
            $module_type = '';
            switch ($type) {
                case 'Sub':
                    $module_type = 'Main';
                    break;
                case 'Sub-1':
                    $module_type = 'Sub';
                    break;
                case 'Sub-2':
                    $module_type = 'Sub-1';
                    break;
                default:
                    $module_type = 'No Parent';
            }

            $modules = Modules::select('id', 'module_name', 'module_code')->where(['module_type' =>  $module_type, 'status' => 1])->get();
            return response()->json(["message" => "Child List", "modules" => $modules, "code" => 200]);
        } else {
            return response()->json(["message" => "Please provide type to get child", "code" => 404]);
        }
    }

    public function assign_module_to_role()
    {
        $validator = Validator::make($request->all(), [
            'parent_module_id' => 'required|integer',
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
            'status' => 'required|integer',
            'child_module_ids' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    }
}
