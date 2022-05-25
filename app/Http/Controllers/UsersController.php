<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Roles;
use App\Models\Modules;

class UsersController extends Controller
{
    public function get_user_role(Request $request)
    {
        $user_id = $request->userid;
        $user = User::where('id', $user_id)->first();
        $user_info = [];
        if (count($user->roles) > 0) {
            $roles = Roles::where('id', $user->roles->first()->id)->with('modules')->get()->toArray();
            if ($roles) {
                $user_info = ['id' => $user->id, 'Name' => $user->name, 'Email' => $user->email, "Role" => $user->roles->first()->role_name];
                $user_modules = [];
                $modules = Modules::with("children")->where(["module_parent_id" => 0])->get()->toArray();
                foreach ($roles[0]['modules'] as $k => $v) {
                    if ($v['child_module_ids']) {
                        foreach ($modules as $key => $value) {
                            if ($value['id'] == $v['parent_module_id']) {
                                $user_modules['main_module'][$k] = $this->module_array($value);
                                $sub_module_string = $v['child_module_ids'];

                                $sub_modules =  explode('&', $sub_module_string);

                                foreach ($sub_modules as $smk => $smv) {
                                    $array = explode('@', $smv);

                                    $sub_module_array = $this->traverse_array($value['children'], $array[0]);
                                    $user_modules['main_module'][$k]['sub_module'][$smk] = $this->module_array($sub_module_array);
                                    if (count($array) > 1) {
                                        $subModules = explode(',', $array[1]);
                                        foreach ($subModules as $k1 => $val) {
                                            $levelOne = explode('^', $val);

                                            $level_one_array = $this->traverse_array($sub_module_array['children'], $levelOne[0]);

                                            $user_modules['main_module'][$k]['sub_module'][$smk]['sub_module_level'][$k1] = $this->module_array($level_one_array);

                                            $levelTwo = explode('-', $levelOne[1]);

                                            foreach ($levelTwo as $kk => $vv) {
                                                $level_two_array = $this->traverse_array($level_one_array['children'], $vv);
                                                $user_modules['main_module'][$k]['sub_module'][$smk]['sub_module_level'][$k1]['sub_module_level_1'][$kk] =  $this->module_array($level_two_array);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $user_info['modules'] = $user_modules;
            } else {
                return response()->json(['status' => 404, 'user' =>  []]);
            }
        }
        return response()->json(['status' => 200, 'user' =>  $user_info]);
    }

    public function traverse_array($array, $id)
    {
        foreach ($array as $key => $value) {
            if ($value['id'] == $id) {
                return $value;
            }
        }
    }
    public function module_array($array)
    {
        return  [
            'id' => $array['id'],
            'name' => $array['module_name'],
            'module_code' => $array['module_code'],
            'parent_id' => $array['module_parent_id']
        ];
    }

    public function user_list($from, $to)
    {
        if (is_numeric($from) && is_numeric($to)) {
            $arr = [$from, $to];
            $to  = ($arr[0] < $arr[1]) ? $to : $from;
            $from = ($arr[0] > $arr[1]) ? $arr[1] : $arr[0];
        } else {
            return response()->json(['status' => 500, 'message' => 'Record limit should be Integer']);
        }
        $users = User::with(['roles' => function ($query) {
            $query->select('role_name');
        }])->select('id', 'name')->skip($from)->take($to - $from)->get();

        return response()->json(['code' => 200, 'user' =>  $users, 'to' => $to]);
    }
}
