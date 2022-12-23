<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\ScreensModule;
use App\Models\ScreenSubModule;
use App\Models\ScreenPageModule;
use App\Models\ScreenAccessRoles;
use App\Models\HospitalBranchTeamManagement;
use App\Models\StaffManagement;
use Illuminate\Support\Facades\DB;

class ScreenModuleController extends Controller
{
    public function storeModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_name' => 'required|string',
            'module_code' => 'required|string',
            'module_short_name' => 'required|string',
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
            'sub_module_code' => 'required|string',
            'sub_module_name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = ScreensModule::select('module_name')->where(['id' => $request->module_id])->get();
        $module = [
            'added_by' => $request->added_by,
            'module_name' => $list[0]['module_name'],
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
            'sub_module_id' => '',
            'screen_name' => 'required|string',
            'screen_route' => 'required|string',
            'screen_description' => 'required|string',
            'icon' => 'required',
            'index' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::select('module_name')->where(['id' => $request->module_id])->get();

        if( $request->sub_module_id){
        $sub_module_name = ScreenSubModule::select('sub_module_name')->where(['id' => $request->sub_module_id])->get();
        $module = [
            'added_by' => $request->added_by,
            'module_id' => $request->module_id,
            'module_name' => $module_name[0]['module_name'],
            'sub_module_id' => $request->sub_module_id,
            'sub_module_name' => $sub_module_name[0]['sub_module_name'],
            'screen_name' => $request->screen_name,
            'screen_route' => $request->screen_route,
            'screen_description' => $request->screen_description,
            'icon' => $request->icon,
            'index_val' => $request->index,
            'screen_status' => 1
        ];
        ScreenPageModule::firstOrCreate($module);
        return response()->json(["message" => "Screen Created Successfully!", "code" => 200]);
    }else{
        $module = [
            'added_by' => $request->added_by,
            'module_id' => $request->module_id,
            'module_name' => $module_name[0]['module_name'],
            'screen_name' => $request->screen_name,
            'screen_route' => $request->screen_route,
            'screen_description' => $request->screen_description,
            'icon' => $request->icon,
            'index_val' => $request->index,
            'screen_status' => 1
        ];
        ScreenPageModule::firstOrCreate($module);
        return response()->json(["message" => "Screen Created Successfully!", "code" => 200]);
    }
    }

    public function getModuleList()
    {
        $list = ScreensModule::select('id', 'module_name','module_code','module_short_name','module_order')
        ->where('module_status','=', '1')
        ->orderBy('module_name', 'ASC')
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
            ->select('screen_sub_modules.id','screen_sub_modules.sub_module_name','screen_sub_modules.sub_module_code','screen_modules.module_code','screen_sub_modules.module_name')
            ->where('screen_sub_modules.sub_module_status','=', '1')
            ->get();
        return response()->json(["message" => "SubModule List", 'list' => $list, "code" => 200]);
    }

     public function getScreenPageList()
    {
        $list = DB::table('screens')
            ->join('screen_modules', 'screens.module_id', '=', 'screen_modules.id')
            ->leftjoin('screen_sub_modules', 'screens.sub_module_id', '=', 'screen_sub_modules.id')
            ->select('screens.id as screen_id','screens.screen_name','screens.screen_route','screens.screen_description','screen_modules.module_name','screen_sub_modules.sub_module_name as sub_module_name')
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
        $list = ScreenSubModule::select('id', 'sub_module_name as sub_module_name')->where('module_id', $request->module_id)->get();
        if(count($list)>0){
            return response()->json(["message" => "SubModule List", 'list' => $list, "code" => 200]);
        }else{
            return response()->json(["message" => "SubModule List", 'list' => "", "code" => 400]);
        }

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
        $list = ScreenPageModule::select('id', 'screen_name','screen_route','screen_description','module_id','sub_module_id','icon','index_val')->where('id', $request->screen_id)->get();
        return response()->json(["message" => "ScreenModule List", 'list' => $list, "code" => 200]);
    }

    public function getScreenByModuleAndSubModule(Request $request)
    {
        $validator = Validator::make($request->all(), ['module_id' => 'required|integer', 'sub_module_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ScreenPageModule::select('id', 'module_name', 'sub_module_name', 'screen_name', 'screen_description','icon','index_val')->where(['module_id' => $request->module_id, 'sub_module_id' => $request->sub_module_id])->get();
        return response()->json(["message" => "Screen List", 'list' => $list, "code" => 200]);
    }

    public function addScreenRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'module_id' => 'required|integer',
            'sub_module_id' => '',
            'screen_ids' => '',
            'hospital_id' => 'required|integer',
            'branch_id' => '',
            'team_id' => '',
            'staff_id' => 'required|integer',
            'type' => '',
        ]);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);

        if($request->screen_ids){
            $screen_ids = explode(',', $request->screen_ids);
            foreach ($screen_ids as $k => $v) {
                $screen = [
                    'module_id' => $request->module_id,
                    'sub_module_id' => $request->sub_module_id,
                    'screen_id' => $v,
                    'hospital_id' => $request->hospital_id,
                    'branch_id' => $request->branch_id,
                    'team_id' => $request->team_id,
                    'staff_id' => $request->staff_id,
                    'access_screen' => '1',
                    'read_writes' => '1',
                    'read_only' => '0',
                    'user_type' => $request->type,

                ];

                if (ScreenAccessRoles::where($screen)->count() == 0) {
                    $screen['added_by'] = $request->added_by;
                    ScreenAccessRoles::Create($screen);
            }
            }
            return response()->json(["message" => "Roles has been assigned successfully1!", "code" => 200]);
        }else{
            if($request->module_id){
                $list = ScreenPageModule::select('id')->where(['module_id' => $request->module_id])->get();
                    $screen = [
                        'module_id' => $request->module_id,
                        'screen_id' => $list[0]->id,
                        'hospital_id' => $request->hospital_id,
                        'branch_id' => $request->branch_id,
                        'team_id' => $request->team_id,
                        'staff_id' => $request->staff_id,
                        'access_screen' => '1',
                        'read_writes' => '1',
                        'read_only' => '0',
                        'added_by' => $request->added_by,
                        'user_type' => $request->type,


                    ];


                ScreenAccessRoles::Create($screen);
                return response()->json(["message" => "Roles has been assigned successfully!e", "code" => 200]);
            }

        }

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
        }
        else {
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
           'sub_module_id' => '',
           'screen_name' => 'required|string',
           'screen_route' => 'required|string',
           'screen_description' => 'required|string',
           'id' => 'required|integer',
           'icon' => 'required',
           'index' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $module_name = ScreensModule::find($request->module_id)->get()->toArray();
        if($request->sub_module_name){
        $sub_module_name = ScreenSubModule::find($request->sub_module_id)->get()->toArray();
        }

        $screen_name = $request->screen_name;
        $screen_route = $request->screen_route;
        $chkPoint =  ScreenPageModule::where(function ($query) use ($screen_name, $screen_route) {
            $query->where('screen_name', '=', $screen_name)->orWhere('screen_route', '=', $screen_route);
        })->where('id', '!=', $request->id)->where('screen_status', '1')->get();
        if ($chkPoint->count() == 0) {
            if($request->sub_module_name){
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
                'icon' => $request->icon,
                'index_val' => $request->index,
                'screen_status' => '1'
                ]);
                return response()->json(["message" => "Screen Module Updated Successfully!", "code" => 200]);
            }else{
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
                'icon' => $request->icon,
                'index_val' => $request->index,
                'screen_status' => '1'
                ]);
                return response()->json(["message" => "Screen Module Updated Successfully!", "code" => 200]);
            }

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
        $list =DB::table('service_division')
                ->select('service_division.id', 'service_register.service_name as team_name')
                ->join('service_register', 'service_register.id', '=', 'service_division.service_id')
                ->join('hospital_branch__details','hospital_branch__details.id','=','service_division.branch_id')
                ->where('service_division.hospital_id', '=', $request->hospital_id)
                ->where('hospital_branch__details.id', '=', $request->branch_id)
                ->where('service_division.status', '=', '1')
                ->get();

        return response()->json(["message" => "Team List", 'list' => $list, "code" => 200]);
    }

    public function getUserMatrixList(Request $request)
    {

        $list = DB::table('screen_access_roles')
            ->join('users', 'screen_access_roles.staff_id', '=', 'users.id')
            ->join('hospital_branch_team_details', 'screen_access_roles.team_id', '=', 'hospital_branch_team_details.id')
            ->select('hospital_branch_team_details.team_name','users.name','screen_access_roles.hospital_id',
            'screen_access_roles.team_id','screen_access_roles.branch_id',DB::raw("'Active' as status"),'users.id')
            ->where('screen_access_roles.status','=', '1')
            ->distinct('screen_access_roles.staff_id','screen_access_roles.team_id','users.name')
            ->get();

        return response()->json(["message" => "User Matrix List", 'list' => $list, "code" => 200]);
    }
    public function getUserMatrixListById(Request $request)
    {


        $list = DB::table('screen_access_roles')
        ->select('users.name',DB::raw("'Active' as status"))
        
            ->join('users', 'screen_access_roles.staff_id', '=', 'users.id')
            //->join('hospital_branch_team_details', 'screen_access_roles.team_id', '=', 'hospital_branch_team_details.id')
            ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
            ->where('screen_access_roles.status','=', '1')
            ->where('screen_access_roles.staff_id',$request->staff_id)
            ->first();

            $list1 = DB::table('screen_access_roles')
            ->select('screen_access_roles.id','screen_access_roles.hospital_id','screen_access_roles.team_id','screen_access_roles.branch_id',
            'screen_access_roles.module_id','screen_access_roles.sub_module_id',
            DB::raw("'Active' as status"),'screens.screen_name','screens.screen_description','screen_access_roles.screen_id',
            'screen_access_roles.access_screen','screen_access_roles.read_writes','screen_access_roles.read_only')
            ->leftjoin('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
            ->where('screen_access_roles.staff_id',$request->staff_id) //staff_id refers to the user table usersid
            ->get();
            $result1 = (array) json_decode($list1,true);
            $result=[];
            if (count($result1) > 0) {
                foreach ($result1 as $key => $val) {
                    $result[$key]['access_screen'] = $val['access_screen'] ??  'NA';
                    $result[$key]['branch_id'] = $val['branch_id'] ??  'NA';
                    $result[$key]['hospital_id'] = $val['hospital_id'] ??  'NA';
                    $result[$key]['module_id'] = $val['module_id'] ??  'NA';
                    $result[$key]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$key]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$key]['screen_description'] = $val['screen_description'] ??  'NA';
                    $result[$key]['screen_name'] = $val['screen_name'] ??  'NA';
                    $result[$key]['sub_module_id'] = $val['sub_module_id'] ??  'NA';
                    $result[$key]['team_id'] = $val['team_id'] ??  'NA';
                    $result[$key]['id'] = $val['id'] ??  'NA';
                    $result[$key]['screen_id'] = $val['screen_id'] ??  'NA';
                }
            }
        return response()->json(["message" => "User Matrix List", 'list' => $list,'user_details' => $result, "code" => 200]);
    }

    public function getAccessScreenByUserId1(Request $request)
    {
        $validator = Validator::make($request->all(), ['staff_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);


        $list1 = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->join('screen_modules', 'screen_modules.id', '=', 'screen_access_roles.module_id')
        ->select('screen_access_roles.module_id','screen_access_roles.sub_module_id',
        'screens.screen_route','screens.icon','screen_access_roles.screen_id',
        'screen_modules.module_name','screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screen_access_roles.staff_id','=', $request->staff_id)
        ->groupBy('screen_access_roles.id','screen_modules.id','screen_access_roles.module_id','screen_access_roles.sub_module_id','screens.screen_name','screens.screen_route','screen_modules.module_name','screens.icon','screens.index_val','screen_access_roles.screen_id',
        'screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();

        $result1 = (array) json_decode($list1,true);
        $result=[];
        $result2 = [];

        if (count($result1) > 0) {
            foreach ($result1 as $key => $val) {

                if(empty($result[$val['module_id']])){
                    $result[$val['module_id']]=[];
                    $result[$val['module_id']]['module_id']=$val['module_id'] ??  'NA';
                    $result[$val['module_id']]['screen_route'] = $val['screen_route'] ??  'NA';
                    $result[$val['module_id']]['screen_name'] = $val['module_name'] ??  'NA';
                    $result[$val['module_id']]['icon'] = $val['icon'] ??  'NA';
                    $result[$val['module_id']]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$val['module_id']]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$val['module_id']]['screen_acess_role_id'] = $val['id'] ??  'NA';

                    $result[$val['module_id']]['sub_module_id']=[];
                }

                if($val['sub_module_id']){
                   $ab=ScreenPageModule:: select('*')->where('sub_module_id',$val['sub_module_id'])
                   ->get();
                   $result_tmp = (array) json_decode($ab,true);
                   foreach ($result_tmp as $key => $val_) {
                    $result[$val['module_id']]['sub_module_id'][] = $val_;
                    }
                }
            }
            foreach ($result as $key => $value) {
                $result2[] = $value;
            }
        }
        if($result2){
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 200]);
        }else{
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 400]);
        }


    }

    public function getAccessScreenByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), ['staff_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);

    if($request->type=="User Administrator"){
        $list1 = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->join('screen_modules', 'screen_modules.id', '=', 'screen_access_roles.module_id')
        ->select('screen_access_roles.module_id','screen_access_roles.sub_module_id',
        'screens.screen_route','screens.icon','screen_access_roles.screen_id',
        'screen_modules.module_name','screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screen_access_roles.user_type','=', $request->type)
        ->where('screen_modules.id','!=', '26')//Workload Report
        ->where('screen_modules.id','!=', '27')//Activities Report
        ->where('screen_modules.id','!=', '28')//National KPI
        ->where('screen_modules.id','!=', '29')//SHHARP
        ->orWhere('screen_access_roles.staff_id','=', $request->staff_id)
        ->groupBy('screen_access_roles.id','screen_modules.id','screen_access_roles.module_id','screen_access_roles.sub_module_id','screens.screen_name','screens.screen_route','screen_modules.module_name','screens.icon','screens.index_val','screen_access_roles.screen_id',
        'screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();

        $result1 = (array) json_decode($list1,true);
        $result=[];
        $result2 = [];

        if (count($result1) > 0) {
            foreach ($result1 as $key => $val) {
                if(empty($result[$val['module_id']])){
                    $result[$val['module_id']]=[];
                    $result[$val['module_id']]['module_id']=$val['module_id'] ??  'NA';
                    $result[$val['module_id']]['screen_route'] = $val['screen_route'] ??  'NA';
                    $result[$val['module_id']]['screen_name'] = $val['module_name'] ??  'NA';
                    $result[$val['module_id']]['icon'] = $val['icon'] ??  'NA';
                    $result[$val['module_id']]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$val['module_id']]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$val['module_id']]['screen_acess_role_id'] = $val['id'] ??  'NA';

                    $result[$val['module_id']]['sub_module_id']=[];
                }

                if($val['sub_module_id']){
                $ab = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->select('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screens.sub_module_id','=',$val['sub_module_id'])
        ->where('screen_modules.id','!=', '26')//Workload Report
        ->where('screen_modules.id','!=', '27')//Activities Report
        ->where('screen_modules.id','!=', '28')//National KPI
        ->where('screen_modules.id','!=', '29')//SHHARP
        ->where('screen_access_roles.user_type','=', $request->type)
        ->groupBy('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();
                   $result_tmp = (array) json_decode($ab,true);
                   foreach ($result_tmp as $key => $val_) {
                    $result[$val['module_id']]['sub_module_id'][] = $val_;
                    }
                }
            }
            foreach ($result as $key => $value) {
                $result2[] = $value;
            }
        }
        if($result2){
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 200]);
        }else{
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 400]);
        }
    }else{

        $list1 = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->join('screen_modules', 'screen_modules.id', '=', 'screen_access_roles.module_id')
        ->select('screen_access_roles.module_id','screen_access_roles.sub_module_id',
        'screens.screen_route','screens.icon','screen_access_roles.screen_id',
        'screen_modules.module_name','screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screen_access_roles.staff_id','=', $request->staff_id)
        ->where('screen_modules.id','!=', '26')//Workload Report
        ->where('screen_modules.id','!=', '27')//Activities Report
        ->where('screen_modules.id','!=', '28')//National KPI
        ->where('screen_modules.id','!=', '29')//SHHARP
        ->groupBy('screen_access_roles.id','screen_modules.id','screen_access_roles.module_id','screen_access_roles.sub_module_id','screens.screen_name','screens.screen_route','screen_modules.module_name','screens.icon','screens.index_val','screen_access_roles.screen_id',
        'screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();

        $result1 = (array) json_decode($list1,true);
        $result=[];
        $result2 = [];

        if (count($result1) > 0) {
            foreach ($result1 as $key => $val) {
                if(empty($result[$val['module_id']])){
                    $result[$val['module_id']]=[];
                    $result[$val['module_id']]['module_id']=$val['module_id'] ??  'NA';
                    $result[$val['module_id']]['screen_route'] = $val['screen_route'] ??  'NA';
                    $result[$val['module_id']]['screen_name'] = $val['module_name'] ??  'NA';
                    $result[$val['module_id']]['icon'] = $val['icon'] ??  'NA';
                    $result[$val['module_id']]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$val['module_id']]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$val['module_id']]['screen_acess_role_id'] = $val['id'] ??  'NA';

                    $result[$val['module_id']]['sub_module_id']=[];
                }

                if($val['sub_module_id']){
                $ab = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->select('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screens.sub_module_id','=',$val['sub_module_id'])
        ->where('screen_access_roles.staff_id','=', $request->staff_id)
        ->groupBy('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();
                   $result_tmp = (array) json_decode($ab,true);
                   foreach ($result_tmp as $key => $val_) {
                    $result[$val['module_id']]['sub_module_id'][] = $val_;
                    }
                }
            }
            foreach ($result as $key => $value) {
                $result2[] = $value;
            }
        }
        if($result2){
            return response()->json(["message" => "User Access List2", 'list' => $result2, "code" => 200]);
        }else{
            return response()->json(["message" => "User Access List2", 'list' => $result2, "code" => 400]);
        }

    }


    }

    public function getAccessScreenByUserIdForReport(Request $request)
    {
        $validator = Validator::make($request->all(), ['staff_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);

    if($request->type=="User Administrator"){
        $list1 = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->join('screen_modules', 'screen_modules.id', '=', 'screen_access_roles.module_id')
        ->select('screen_access_roles.module_id','screen_access_roles.sub_module_id',
        'screens.screen_route','screens.icon','screen_access_roles.screen_id',
        'screen_modules.module_name','screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screen_access_roles.user_type','=', $request->type)
        ->where(function ($query) {
            $query->orWhere('screen_modules.id','=', '26')//Workload Report
                  ->orWhere('screen_modules.id','=', '27')//Activities Report
                  ->orWhere('screen_modules.id','=', '28')//National KPI
                  ->orWhere('screen_modules.id','=', '29');//SHHARP
        })
        ->where('screen_access_roles.staff_id','=', $request->staff_id)
        ->groupBy('screen_access_roles.id','screen_modules.id','screen_access_roles.module_id','screen_access_roles.sub_module_id','screens.screen_name','screens.screen_route','screen_modules.module_name','screens.icon','screens.index_val','screen_access_roles.screen_id',
        'screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();

        $result1 = (array) json_decode($list1,true);
        $result=[];
        $result2 = [];

        if (count($result1) > 0) {
            foreach ($result1 as $key => $val) {
                if(empty($result[$val['module_id']])){
                    $result[$val['module_id']]=[];
                    $result[$val['module_id']]['module_id']=$val['module_id'] ??  'NA';
                    $result[$val['module_id']]['screen_route'] = $val['screen_route'] ??  'NA';
                    $result[$val['module_id']]['screen_name'] = $val['module_name'] ??  'NA';
                    $result[$val['module_id']]['icon'] = $val['icon'] ??  'NA';
                    $result[$val['module_id']]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$val['module_id']]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$val['module_id']]['screen_acess_role_id'] = $val['id'] ??  'NA';

                    $result[$val['module_id']]['sub_module_id']=[];
                }

                if($val['sub_module_id']){
                    $ab = DB::table('screen_access_roles')
                        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
                        ->select('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
                        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
                        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
                        ->where('screen_access_roles.status','=', '1')
                        ->where('screens.screen_status','=', '1')
                        ->where('screens.sub_module_id','=',$val['sub_module_id'])
                        ->where(function ($query) {
                            $query->orWhere('screen_modules.id','=', '26')//Workload Report
                                  ->orWhere('screen_modules.id','=', '27')//Activities Report
                                  ->orWhere('screen_modules.id','=', '28')//National KPI
                                  ->orWhere('screen_modules.id','=', '29');//SHHARP
                        })
                        ->where('screen_access_roles.user_type','=', $request->type)
                        ->groupBy('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
                        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
                        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
                        ->get();
                    $result_tmp = (array) json_decode($ab,true);
                    foreach ($result_tmp as $key => $val_) {
                        $result[$val['module_id']]['sub_module_id'][] = $val_;
                        }
                }
            }
            foreach ($result as $key => $value) {
                $result2[] = $value;
            }
        }
        if($result2){
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 200]);
        }else{
            return response()->json(["message" => "User Access List", 'list' => $result2, "code" => 400]);
        }
    }else{

        $list1 = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->join('screen_modules', 'screen_modules.id', '=', 'screen_access_roles.module_id')
        ->select('screen_access_roles.module_id','screen_access_roles.sub_module_id',
        'screens.screen_route','screens.icon','screen_access_roles.screen_id',
        'screen_modules.module_name','screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screen_access_roles.staff_id','=', $request->staff_id) //possibleconditionforappearingreport[all]
        ->where(function ($query) {
            $query->orWhere('screen_modules.id','=', '26')//Workload Report
                  ->orWhere('screen_modules.id','=', '27')//Activities Report
                  ->orWhere('screen_modules.id','=', '28')//National KPI
                  ->orWhere('screen_modules.id','=', '29');//SHHARP
        })
        ->groupBy('screen_access_roles.id','screen_modules.id','screen_access_roles.module_id','screen_access_roles.sub_module_id','screens.screen_name','screens.screen_route','screen_modules.module_name','screens.icon','screens.index_val','screen_access_roles.screen_id',
        'screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();

        $result1 = (array) json_decode($list1,true);
        $result=[];
        $result2 = [];

        if (count($result1) > 0) {
            foreach ($result1 as $key => $val) {
                if(empty($result[$val['module_id']])){
                    $result[$val['module_id']]=[];
                    $result[$val['module_id']]['module_id']=$val['module_id'] ??  'NA';
                    $result[$val['module_id']]['screen_route'] = $val['screen_route'] ??  'NA';
                    $result[$val['module_id']]['screen_name'] = $val['module_name'] ??  'NA';
                    $result[$val['module_id']]['icon'] = $val['icon'] ??  'NA';
                    $result[$val['module_id']]['read_writes'] = $val['read_writes'] ??  'NA';
                    $result[$val['module_id']]['read_only'] = $val['read_only'] ??  'NA';
                    $result[$val['module_id']]['screen_acess_role_id'] = $val['id'] ??  'NA';

                    $result[$val['module_id']]['sub_module_id']=[];
                }

                if($val['sub_module_id']){
                $ab = DB::table('screen_access_roles')
        ->join('screens', 'screen_access_roles.screen_id', '=', 'screens.id')
        ->select('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->where('screen_access_roles.status','=', '1')
        ->where('screens.screen_status','=', '1')
        ->where('screens.sub_module_id','=',$val['sub_module_id'])
        ->where('screen_access_roles.staff_id','=', $request->staff_id)
        ->groupBy('screens.id','screens.added_by','screens.module_id','screens.module_name','screens.sub_module_id',
        'screens.sub_module_name','screens.screen_name','screens.screen_route','screens.icon',
        'screen_access_roles.id','screen_access_roles.read_writes','screen_access_roles.read_only')
        ->get();
                   $result_tmp = (array) json_decode($ab,true);
                   foreach ($result_tmp as $key => $val_) {
                    $result[$val['module_id']]['sub_module_id'][] = $val_;
                    }
                }
            }
            foreach ($result as $key => $value) {
                $result2[] = $value;
            }
        }
        if($result2){
            return response()->json(["message" => "User Access List2", 'list' => $result2, "code" => 200]);
        }else{
            return response()->json(["message" => "User Access List2", 'list' => $result2, "code" => 400]);
        }

    }


    }




    public function UpdateScreenRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'screen_idss' => 'required',
            'userid'=>'required',

        ]);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        foreach ($request->screen_idss as $k => $v) {
            if($v['access_screen']==true){
                $true ='1';
            }else{
                $true ='0';
            }
            ScreenAccessRoles::where(
                ['staff_id' => $request->userid,
                'screen_id' => $v['screen_ids']]
            )->update([
                'status' => $true,
                'access_screen' => $v['access_screen'],
                'read_writes' =>$v['read_writes'],
                'read_only' => $v['read_only'],
            ]);
        }
        return response()->json(["message" => "Roles has been Updated successfully!", "code" => 200]);
    }

    public function getScreenByModuleId(Request $request)
    {
        $list = DB::table('screens')
            ->join('screen_modules', 'screens.module_id', '=', 'screen_modules.id')
            ->join('screen_sub_modules', 'screens.sub_module_id', '=', 'screen_sub_modules.id')
            ->select('screens.sub_module_id as submodule_id','screens.id as screen_id','screens.screen_name','screens.screen_route','screens.screen_description','screen_modules.module_name','screen_sub_modules.sub_module_name')
            ->where('screens.module_id','=', $request->module_id)
            ->where('screens.screen_status','=', '1')
            ->get();
        return response()->json(["message" => "Screen Page Module List", 'list' => $list, "code" => 200]);
    }




}
