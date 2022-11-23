<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultRoleAccess;
use App\Models\ScreenPageModule;

use Validator;
use DB;

class DefaultRoleAccessController extends Controller
{
    public function store(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'module_id' => 'required',
        ]);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);

        if($request->screen_ids){
           
            $screen_ids = explode(',', $request->screen_ids);
            foreach ($screen_ids as $k => $v) {
               
                $subId = DB::table('screens')
                ->select('sub_module_id')
                ->where('id',$v)
                ->first();
                $screen = [
                    'module_id' => $request->module_id,
                    'screen_id' => $v,
                    'sub_module_id' => $subId->sub_module_id,
                    'role_id' => $request->roles_id,
                ];
    
                if (DefaultRoleAccess::where($screen)->count() == 0) {
                    DefaultRoleAccess::Create($screen);
                }
            }
            
            return response()->json(["message" => "Default access successfully assigned!", "code" => 200]);
        }else{
           
            if($request->module_id){
                $list = ScreenPageModule::select('id')->where(['module_id' => $request->module_id])->get();
                
                    $screen = [
                        'module_id' => $request->module_id,
                        'screen_id' => $list[0]->id,
                        'role_id' => $request->roles_id,
           
                    ];
                
                
                DefaultRoleAccess::Create($screen);
                return response()->json(["message" => "Roles has been assigned successfully!", "code" => 200]);
                
            }
           
        }
       
    }
}
