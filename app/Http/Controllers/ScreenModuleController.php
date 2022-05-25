<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\ScreensModule;
use App\Models\ScreenSubModule;
use App\Models\ScreenPageModule;
use App\Models\ScreenAccessRoles;
use App\Models\HospitalBranchTeamManagement;
use Illuminate\Support\Facades\DB;

class ScreenModuleController extends Controller
{
    public function storeModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_name' => 'required|string|unique:screen_modules',
            'module_code' => 'required|string|unique:screen_modules',
            'module_short_name' => 'required|string|unique:screen_modules',
            'module_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module = [
            'added_by' => $request->added_by,
            'module_name' => $request->module_name,
            'module_code' => $request->module_code,
            'module_short_name' => $request->module_short_name,
            'module_order' => $request->module_order,
            'module_status' => 1
        ];
        ScreensModule::firstOrCreate($module);
        return response()->json(["message" => "Module Created Successfully!", "code" => 200]);
    }

    public function storeSubModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|not_in:0',
            'sub_module_code' => 'required|string|unique:screen_sub_modules',
            'sub_module_name' => 'required|string|unique:screen_sub_modules'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::find($request->module_id)->get()->toArray();

        $module = [
            'added_by' => $request->added_by,
            'module_name' => $module_name[0]['module_name'],
            'sub_module_code' => $request->sub_module_code,
            'sub_module_name' => $request->sub_module_name,
            'module_id' => $request->module_id,
            'sub_module_status' => 1
        ];
        ScreenSubModule::firstOrCreate($module);
        return response()->json(["message" => "Sub Module Created Successfully!", "code" => 200]);
    }

    public function storeScreen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|not_in:0',
            'sub_module_id' => 'required|not_in:0',
            'screen_name' => 'required|string|unique:screens',
            'screen_route' => 'required|string|unique:screens',
            'screen_description' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::find($request->module_id)->get()->toArray();
        $sub_module_name = ScreenSubModule::find($request->sub_module_id)->get()->toArray();
        $module = [
            'added_by' => $request->added_by,
            'module_id' => $request->module_id,
            'module_name' => $module_name[0]['module_name'],
            'sub_module_id' => $request->sub_module_id,
            'sub_module_name' => $sub_module_name[0]['sub_module_name'],
            'screen_name' => $request->screen_name,
            'screen_route' => $request->screen_route,
            'screen_description' => $request->screen_description,
            'screen_status' => 1
        ];
        ScreenPageModule::firstOrCreate($module);
        return response()->json(["message" => "Screen Created Successfully!", "code" => 200]);
    }

    public function getModuleList()
    {
        $list = ScreensModule::select('id', 'module_name','module_code','module_short_name','module_order')
        ->where('module_status','=', '1')
        ->get();
        return response()->json(["message" => "Module List", 'list' => $list, "code" => 200]);
    }

    public function getModuleListByModuleId(Request $request)
    {
        $validator = Validator::make($request->all(), ['module_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreensModule::select('id', 'module_name','module_code','module_short_name','module_order')->where('id', $request->module_id)->get();
        return response()->json(["message" => "Module List", 'list' => $list, "code" => 200]);
    }

    public function getSubModuleList()
    {
        $list = DB::table('screen_sub_modules')
            ->join('screen_modules', 'screen_sub_modules.module_id', '=', 'screen_modules.id')
            ->select('screen_sub_modules.id','screen_sub_modules.sub_module_name','screen_sub_modules.sub_module_code','screen_modules.module_code')
            ->where('screen_sub_modules.sub_module_status','=', '1')
            ->get();
        //$list = ScreenSubModule::select('id', 'sub_module_name')->get();
        return response()->json(["message" => "SubModule List", 'list' => $list, "code" => 200]);
    }

     public function getScreenPageList()
    {
        $list = DB::table('screens')
            ->join('screen_modules', 'screens.module_id', '=', 'screen_modules.id')
            ->join('screen_sub_modules', 'screens.sub_module_id', '=', 'screen_sub_modules.id')
            ->select('screens.id as screen_id','screens.screen_name','screens.screen_route','screens.screen_description','screen_modules.module_name','screen_sub_modules.sub_module_name')
            ->where('screens.screen_status','=', '1')
            ->get();
        return response()->json(["message" => "Screen Page Module List", 'list' => $list, "code" => 200]);
    }

    public function getScreenPageListByModuleIdAndSubModuleId(Request $request)
    {
        $validator = Validator::make($request->all(), ['module_id' => 'required|integer','sub_module_id' => 'required|integer',]);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = DB::table('screens')
            ->join('screen_modules', 'screens.module_id', '=', 'screen_modules.id')
            ->join('screen_sub_modules', 'screens.sub_module_id', '=', 'screen_sub_modules.id')
            ->select('screens.id as screen_id','screens.screen_name','screens.screen_route','screens.screen_description','screen_modules.module_name','screen_sub_modules.sub_module_name')
            ->where('screens.module_id','=', $request->module_id)
            ->where('screens.sub_module_id','=', $request->sub_module_id)
            ->where('screens.screen_status','=', '1')
            ->get();
        return response()->json(["message" => "Screen Page Module List", 'list' => $list, "code" => 200]);
    }


    public function getSubModuleListByModuleId(Request $request)
    {
        $validator = Validator::make($request->all(), ['module_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreenSubModule::select('id', 'sub_module_name')->where('module_id', $request->module_id)->get();
        return response()->json(["message" => "SubModule List", 'list' => $list, "code" => 200]);
    }

    public function getSubModuleListBySubModuleId(Request $request)
    {
        $validator = Validator::make($request->all(), ['sub_module_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreenSubModule::select('id', 'sub_module_name','module_id','sub_module_code')->where('id', $request->sub_module_id)->get();
        return response()->json(["message" => "SubModule List", 'list' => $list, "code" => 200]);
    }


    public function getScreenModuleListById(Request $request)
    {
        $validator = Validator::make($request->all(), ['screen_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreenPageModule::select('id', 'screen_name','screen_route','screen_description','module_id','sub_module_id')->where('id', $request->screen_id)->get();
        return response()->json(["message" => "ScreenModule List", 'list' => $list, "code" => 200]);
    }

    public function getScreenByModuleAndSubModule(Request $request)
    {
        $validator = Validator::make($request->all(), ['module_id' => 'required|integer', 'sub_module_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreenPageModule::select('id', 'module_name', 'sub_module_name', 'screen_name', 'screen_description')->where(['module_id' => $request->module_id, 'sub_module_id' => $request->sub_module_id])->get();
        return response()->json(["message" => "Screen List", 'list' => $list, "code" => 200]);
    }

    public function addScreenRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|integer',
            'sub_module_id' => 'required|integer',
            'screen_ids' => 'required|string',
            'hospital_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'team_id' => 'required|integer',
            'staff_id' => 'required|integer',
        ]);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $screen_ids = explode(',', $request->screen_ids);
        foreach ($screen_ids as $k => $v) {
            $screen = [
                'module_id' => $request->module_id,
                'sub_module_id' => $request->sub_module_id,
                'screen_id' => $v,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
                'team_id' => $request->team_id,
                'staff_id' => $request->staff_id
            ];

            if (ScreenAccessRoles::where($screen)->count() == 0) {
                $screen['added_by'] = $request->added_by;
                ScreenAccessRoles::Create($screen);
            }
        }
        return response()->json(["message" => "Roles has been assigned successfully!", "code" => 200]);
    }


    public function updateModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_name' => 'required|string',
            'module_code' => 'required|string',
            'module_short_name' => 'required|string',
            'module_order' => 'required|integer',
            'module_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = $request->module_name;
        $module_code = $request->module_code;
        $module_short_name = $request->module_short_name;
        $chkPoint =  ScreensModule::where(function ($query) use ($module_name, $module_code,$module_short_name) {
            $query->where('module_name', '=', $module_name)->orWhere('module_code', '=', $module_code)->orWhere('module_short_name', '=', $module_short_name);
        })->where('id', '!=', $request->module_id)->where('module_status', '1')->get();
        if ($chkPoint->count() == 0) {
            ScreensModule::where(
                ['id' => $request->module_id]
            )->update([
            'added_by' => $request->added_by,
            'module_name' => $request->module_name,
            'module_code' => $request->module_code,
            'module_short_name' => $request->module_short_name,
            'module_order' => $request->module_order,
            'module_status' => '1'
            ]);
            return response()->json(["message" => "Module Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
   }

   public function removeModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ScreensModule::where(
            ['id' => $request->module_id]
        )->update([
            'module_status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Module Deleted Successfully", "code" => 200]);
   }

   public function updateSubModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|not_in:0',
            'sub_module_code' => 'required|string',
            'sub_module_name' => 'required|string',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::find($request->module_id)->get()->toArray();
        $sub_module_name = $request->sub_module_name;
        $sub_module_code = $request->sub_module_code;
        $chkPoint =  ScreenSubModule::where(function ($query) use ($sub_module_name, $sub_module_code) {
            $query->where('sub_module_name', '=', $sub_module_name)->orWhere('sub_module_code', '=', $sub_module_code);
        })->where('id', '!=', $request->id)->where('sub_module_status', '1')->get();
        if ($chkPoint->count() == 0) {
            ScreenSubModule::where(
                ['id' => $request->id]
            )->update([
            'added_by' => $request->added_by,
            'module_name' => $module_name[0]['module_name'],
            'sub_module_code' => $request->sub_module_code,
            'sub_module_name' => $request->sub_module_name,
            'module_id' => $request->module_id,
            'sub_module_status' => '1'
            ]);
            return response()->json(["message" => "Module Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
   }

   public function removeSubModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'sub_module_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ScreenSubModule::where(
            ['id' => $request->sub_module_id]
        )->update([
            'sub_module_status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "SubModule Deleted Successfully", "code" => 200]);
   }


   public function updateScreenModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
           'added_by' => 'required|integer',
           'module_id' => 'required|not_in:0',
           'sub_module_id' => 'required|not_in:0',
           'screen_name' => 'required|string',
           'screen_route' => 'required|string',
           'screen_description' => 'required|string',
           'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::find($request->module_id)->get()->toArray();
        $sub_module_name = ScreenSubModule::find($request->sub_module_id)->get()->toArray();

        $screen_name = $request->screen_name;
        $screen_route = $request->screen_route;
        $chkPoint =  ScreenPageModule::where(function ($query) use ($screen_name, $screen_route) {
            $query->where('screen_name', '=', $screen_name)->orWhere('screen_route', '=', $screen_route);
        })->where('id', '!=', $request->id)->where('screen_status', '1')->get();
        if ($chkPoint->count() == 0) {
            ScreenPageModule::where(
                ['id' => $request->id]
            )->update([
            'added_by' => $request->added_by,
            'module_id' => $request->module_id,
            'module_name' => $module_name[0]['module_name'],
            'sub_module_id' => $request->sub_module_id,
            'sub_module_name' => $sub_module_name[0]['sub_module_name'],
            'screen_name' => $request->screen_name,
            'screen_route' => $request->screen_route,
            'screen_description' => $request->screen_description,
            'screen_status' => '1'
            ]);
            return response()->json(["message" => "Screen Module Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
   }

   public function removeScreenModule(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'screen_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ScreenPageModule::where(
            ['id' => $request->screen_id]
        )->update([
            'screen_status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Screen Module Deleted Successfully", "code" => 200]);
   }

   public function getTeamListByHospitalIdAndBranchId(Request $request)
    {
        $validator = Validator::make($request->all(), ['hospital_id' => 'required|integer','branch_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = HospitalBranchTeamManagement::select('id', 'team_name')->where('hospital_id', $request->hospital_id)->where('hospital_branch_id', $request->branch_id)->get();
        return response()->json(["message" => "Team List", 'list' => $list, "code" => 200]);
    }

}
