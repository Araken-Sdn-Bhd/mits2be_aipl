<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StaffManagement;
use App\Models\UserBlock;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\UserActivity;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginEmployer']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        DB::enableQueryLog();
        app('log')->debug($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid Credential', 'code' => '400'], 401);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            $id = User::select('id')->where('email', $request->email)->pluck('id');
            $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
            $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
            $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
            $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');

            $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
            $newDate = $date->format('Y-m-d H:i:s');
            if (!empty($systemattempt[0]) == !empty($no_of_attempts[0])) {
                $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . $blocktime[0] . 'hours'));
            } else {
                $currentdatetime = $date->format('Y-m-d H:i:s');
            }
            if (count($id_block_user) == 0) {
                $data = [
                    'user_id' => $id[0],
                    'no_of_attempts' => "1",
                    'block_untill' => $currentdatetime,
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'updated_at' => $date->format('Y-m-d H:i:s'),
                ];
                UserBlock::insert($data);
                return response()->json(["message" => "Incorrect password", "code" => 401]);
            } else {
                if ($systemattempt[0] <= $no_of_attempts[0]) {
                    return response()->json(['message' => 'Account has been blocked for next ' . $blocktime[0] . ' hour', 'code' => 201], 201);
                } else {
                    $count = number_format($no_of_attempts[0]) + 1;
                    UserBlock::where(
                        ['id' => $id_block_user]
                    )->update([
                        'no_of_attempts' => $count,
                        'block_untill' => $currentdatetime
                    ]);
                    return response()->json(['message' => 'Incorrect password.', "code" => 401], 401);
                }
            }
            return response()->json(['message' => 'Unauthorized', "code" => 401], 401);
        }
        $useradmin = User::select('role')->where('email', $request->email)->pluck('role');
        $id = User::select('id')->where('email', $request->email)->pluck('id');
        $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');
        $branch = DB::table('staff_management as s')
            ->select('s.branch_id', 'b.hospital_branch_name', 'b.hospital_id', 'h.hospital_name')
            ->join('hospital_branch__details as b', function ($join) {
                $join->on('b.id', '=', 's.branch_id');
            })
            ->join('hospital_management as h', function ($join) {
                $join->on('h.id', '=', 'b.hospital_id');
            })
            ->where('s.email', $request->email)->first();

        $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
        $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
        $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
        $block_untill = UserBlock::select('block_untill')->where('user_id', $id)->pluck('block_untill');
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $newDate = $date->format('Y-m-d H:i:s');
        $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . 'hours'));
        $userStatus = DB::table('staff_management as s')
            ->select('s.status')
            ->where('s.email', $request->email)->first();
        $designation = DB::table('staff_management as a')->select('b.section_value')->join('general_setting as b', function ($join) {
            $join->on('b.id', '=', 'a.designation_id');
        })->where('a.email', $request->email)->first();
        if ($userStatus->status != 1) {
            return response()->json(['message' => 'User is Inactive', "code" => 202], 202);
        }
        if (!empty($no_of_attempts[0])) {
        } else {
            $no_of_attempts = "0";
        }
        if (($systemattempt[0]) <= ($no_of_attempts[0])) {
            if ($block_untill[0] < $newDate) {
                UserBlock::where(
                    ['id' => $id_block_user]
                )->update([
                    'user_id' => $id[0],
                    'no_of_attempts' => "0",
                    'block_untill' => $currentdatetime
                ]);
                if (!$request->type == "Von") {
                    $screenroute = DB::table('screen_access_roles')
                        ->select(DB::raw('screens.screen_route', 'screens.screen_route_alt'))
                        ->join('screens', function ($join) {
                            $join->on('screens.id', '=', 'screen_access_roles.screen_id');
                        })
                        ->where('screens.screen_route', 'like', '%Dash%')
                        ->where('screen_access_roles.staff_id', '=', $id)
                        ->where('screen_access_roles.status', '=', '1')
                        ->get();

                    if (!empty($screenroute[0])) {
                        $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                        $tmp_alt = json_decode(json_encode($screenroute[0]), true)['screen_route_alt'];

                        return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
                    } else {
                        $tmp = "";
                        return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
                    }
                } else {
                    $screenroute = DB::table('screen_access_roles')
                        ->select(DB::raw('screens.screen_route', 'screens,screen_route_alt'))
                        ->join('screens', function ($join) {
                            $join->on('screens.id', '=', 'screen_access_roles.screen_id');
                        })
                        ->where('screens.screen_route', 'like', '%Mod%')
                        ->where('screen_access_roles.staff_id', '=', $id)
                        ->where('screen_access_roles.status', '=', '1')
                        ->get();
                    if (!empty($screenroute[0])) {
                        $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                        $tmp_alt = json_decode(json_encode($screenroute[0]), true)['screen_route_alt'];
                        return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
                    } else {
                        $tmp = "";
                        return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
                    }
                }
            } else {
                return response()->json(['message' => 'Account has been blocked for next ' . $blocktime[0] . ' hour'], 401);
            }
        } else {
            try {
                UserBlock::where(
                    ['id' => $id_block_user]
                )->update([
                    'user_id' => $id[0],
                    'no_of_attempts' => "0",
                    'block_untill' => $currentdatetime
                ]);
            } catch (\Throwable $th) {
            }
            if (!$request->type == "Von") {
                $screenroute = DB::table('screen_access_roles')
                    ->select(DB::raw('screens.screen_route'))
                    ->join('screens', function ($join) {
                        $join->on('screens.id', '=', 'screen_access_roles.screen_id');
                    })
                    ->where('screens.screen_route', 'like', '%Mod%')
                    ->where('screen_access_roles.staff_id', '=', $id)
                    ->where('screen_access_roles.status', '=', '1')
                    ->get();

                $screenroutealt = DB::table('screen_access_roles')
                    ->select(DB::raw('screens.screen_route_alt'))
                    ->join('screens', function ($join) {
                        $join->on('screens.id', '=', 'screen_access_roles.screen_id');
                    })
                    ->where('screens.screen_route_alt', 'like', '%Mod%')
                    ->where('screen_access_roles.staff_id', '=', $id)
                    ->where('screen_access_roles.status', '=', '1')
                    ->get();

                if (!empty($screenroute[0])) {
                    $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                    $tmp_alt = json_decode(json_encode($screenroutealt[0]), true)['screen_route_alt'];

                    $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                    $userid = User::select('id','name')->where('email', $request->email)->first();
                $check_user=UserActivity::select('id','login_count', 'first_login')->where('user_email',$request->email)->first();
                if($check_user != NULL) {
                    $login_count = $check_user['login_count'] +1;
                    $last_login = $date->format('Y-m-d H:i:s');
                    $activity=[
                        'login_count' => $login_count,
                        'last_login' => $last_login,
                    ];

                    DB::table('user_activity')
                    ->where('id', $check_user['id'])
                    ->update($activity);
                } else {

                    $login_count = 1;
                    $first_login = $date->format('Y-m-d H:i:s');
                    $last_login = $date->format('Y-m-d H:i:s');
                    $activity=[
                        'user_email' => $request->email,
                        'user_name' =>   $userid->name,
                        'branch_name' => $branch->hospital_branch_name,
                        'login_count' => $login_count,
                        'first_login'=> $first_login,
                        'last_login' => $last_login,
                    ];
                    $user_activity = UserActivity::insert($activity);

                }

                    return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
                } else {
                    $tmp = "";
                    return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
                }
            } else {
                $screenroute = DB::table('screen_access_roles')
                    ->select(DB::raw('screens.screen_route', 'screens,screen_route_alt'))
                    ->join('screens', function ($join) {
                        $join->on('screens.id', '=', 'screen_access_roles.screen_id');
                    })
                    ->where('screens.screen_route', 'like', '%Mod%')
                    ->where('screen_access_roles.staff_id', '=', $id)
                    ->where('screen_access_roles.status', '=', '1')
                    ->get();

                if (!empty($screenroute[0])) {
                    $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                    $tmp_alt = json_decode(json_encode($screenroute[0]), true)['screen_route_alt'];
                    return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
                } else {
                    $tmp = "";
                    return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
                }
            }
        }
    }

    public function loginEmployer(Request $request)
    {
        app('log')->debug($request->all());
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid Credential', 'code' => '400'], 401);
        }

        if (!$token = auth()->attempt($validator->validated())) {

            $id = User::select('id')->where('email', $request->email)->pluck('id');

            $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
            $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
            $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
            $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');

            $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
            $newDate = $date->format('Y-m-d H:i:s');
            if (!empty($systemattempt[0]) == !empty($no_of_attempts[0])) {

                $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . $blocktime[0] . 'hours'));
            } else {

                $currentdatetime = $date->format('Y-m-d H:i:s');
            }

            if (count($id_block_user) == 0) {

                $data = [
                    'user_id' => $id[0],
                    'no_of_attempts' => "1",
                    'block_untill' => $currentdatetime,
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'updated_at' => $date->format('Y-m-d H:i:s'),
                ];
                UserBlock::insert($data);
                return response()->json(["message" => "Incorrect password", "code" => 401]);
            } else {

                if ($systemattempt[0] <= $no_of_attempts[0]) {
                    return response()->json(['message' => 'Account has been blocked for next ' . $blocktime[0] . ' hour', 'code' => 201], 201);
                } else {
                    $count = number_format($no_of_attempts[0]) + 1;

                    UserBlock::where(
                        ['id' => $id_block_user]
                    )->update([

                        'no_of_attempts' => $count,
                        'block_untill' => $currentdatetime
                    ]);
                    return response()->json(['message' => 'Incorrect password.', "code" => 401], 401);
                }
            }
            return response()->json(['message' => 'Unauthorized', "code" => 401], 401);
        }

        $id = User::select('id')->where('email', $request->email)->where('role', 'employer')->pluck('id');
        $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');
        $branch = "";
        $tmp = "/app/Modules/Dashboard/high-level-employer";
        $tmp_alt = "/Modules/Dashboard/high-level-employer";
        $designation = "";
        $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
        $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
        $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
        $block_untill = UserBlock::select('block_untill')->where('user_id', $id)->pluck('block_untill');

        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $newDate = $date->format('Y-m-d H:i:s');
        $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . 'hours'));

        if (!empty($no_of_attempts[0])) {
        } else {
            $no_of_attempts = "0";
        }
        if (($systemattempt[0]) <= ($no_of_attempts[0])) {

            if ($block_untill[0] < $newDate) {
                UserBlock::where(
                    ['id' => $id_block_user]
                )->update([
                    'user_id' => $id[0],
                    'no_of_attempts' => "0",
                    'block_untill' => $currentdatetime
                ]);

                return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
            } else {
                return response()->json(['message' => 'Account has been blocked for next ' . $blocktime[0] . ' hour'], 401);
            }
        } else {
            try {
                UserBlock::where(
                    ['id' => $id_block_user]
                )->update([
                    'user_id' => $id[0],
                    'no_of_attempts' => "0",
                    'block_untill' => $currentdatetime
                ]);
            } catch (\Throwable $th) {
            }

            return $this->createNewToken($token, $tmp, $tmp_alt, $branch, $designation);
        }
    }



    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out'], 200);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $tmp, $tmp_alt, $branch, $designation)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 14400,
            'user' => auth()->user(),
            'branch' => $branch,
            'route' => $tmp,
            'route_alt' => $tmp_alt,
            'designation' => $designation,
            'code' => '200'
        ]);
    }
}
