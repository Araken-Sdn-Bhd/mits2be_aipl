<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use DB;
use Validator;

class RolesController extends Controller
{
    public function index()
    {
        $list= Roles::select('id', 'role_name', 'status')->where('status','=','0')->orderBy('role_name','asc')->get();
        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function branch_view_list()
    {
        $list= Roles::select('id', 'role_name', 'status')->where('role_name','!=','System Admin')->where('status','=','0')->orderBy('role_name','asc')->get();
        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function system_admin_role()
    {
        $list= Roles::select('id', 'role_name', 'status')->where('role_name','=','System Admin')->orderBy('role_name','asc')->get();
        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function check_point($role_name, $role_id)
    {
        if ($role_id == 0)
            return Roles::where(['role_name' => $role_name])->get()->count();
        else
            return Roles::where(['role_name' => $role_name])->where('id', '!=', $role_id)->get()->count();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $roles = $this->check_point($request->role_name, 0);
        if ($roles > 0) {
            return response()->json(["message" => "Role is Existed.", "code" => 200]);
        }
        $role = new Roles;
        $role->requested_by =  $request->requested_by;
        $role->role_name = $request->role_name;
        $role->status = $request->status;
        $role->save();
        return response()->json(["message" => "A new role has been added into the system", "code" => 200]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'requested_by' => 'required',
            'role_id' => 'required|integer',
            'role_name' => 'required',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $roles = $this->check_point($request->role_name, $request->role_id);
        if ($roles > 0) {
            return response()->json(["message" => "Role Already Exists.", "code" => 200]);
        }
        $role = Roles::find($request->role_id);
        $role->requested_by =  $request->requested_by;
        $role->role_name = $request->role_name;
        $role->status = $request->status;
        $role->save();
        return response()->json(["message" => "Role has been updated", "code" => 200]);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $role = Roles::find($request->role_id);
        $role->requested_by = $request->user_id;
        $role->status = 0;
        $role->save();
        return response()->json(["message" => "A role has been deleted", "code" => 200]);
    }

    public function set_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
            'assigned_by' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        DB::table('role_user')->updateOrInsert(['user_id' => $request->input('user_id')], ['role_id' => $request->input('role_id'), 'role_assigned_by' => $request->input('assigned_by')]);
        return response()->json("Role has been assigned to user", 200);
    }
    public function role_byId(Request $request)
    {
        $list = Roles::where('id', $request->id)->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }
}
