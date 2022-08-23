<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserBlock;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
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
            // dd($id[0]);
            // $a=date('Y-m-d H:i:s');

            // dd($currentdatetime);
            $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
            $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
            $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
            $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');

            $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
            $newDate = $date->format('Y-m-d H:i:s');
            // dd($systemattempt[0].''.$no_of_attempts[0]);
            if (!empty($systemattempt[0]) == !empty($no_of_attempts[0])) {
                // dd('if');
                $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . $blocktime[0] . 'hours'));
            } else {
                // dd('else');
                $currentdatetime = $date->format('Y-m-d H:i:s');
            }
            // $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . $blocktime[0] . 'hours'));
            // dd($currentdatetime);
            // dd(count($id_block_user));
            if (count($id_block_user) == 0) {
                // dd('if');
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
                // dd($currentdatetime);
                // dd($no_of_attempts[0]);
                if ($systemattempt[0] <= $no_of_attempts[0]) {
                    return response()->json(['message' => 'Account has been blocked for next ' . $blocktime[0] . ' hour', 'code' => 201], 201);
                } else {
                    $count = number_format($no_of_attempts[0]) + 1;
                    // dd($count);
                    UserBlock::where(
                        ['id' => $id_block_user]
                    )->update([
                        // 'user_id' => $id[0],
                        'no_of_attempts' => $count,
                        'block_untill' => $currentdatetime
                    ]);
                    return response()->json(['message' => 'Incorrect password.', "code" => 401], 401);
                }
            }
            return response()->json(['message' => 'Unauthorized', "code" => 401], 401);
        }
        $id = User::select('id')->where('email', $request->email)->pluck('id');
        $id_block_user = UserBlock::select('id')->where('user_id', $id)->pluck('id');

        $systemattempt = SystemSetting::select('variable_value')->where('section', 'login-attempt')->pluck('variable_value');
        $blocktime = SystemSetting::select('variable_value')->where('section', 'system-block-duration')->pluck('variable_value');
        $no_of_attempts = UserBlock::select('no_of_attempts')->where('user_id', $id)->pluck('no_of_attempts');
        $block_untill = UserBlock::select('block_untill')->where('user_id', $id)->pluck('block_untill');
        // dd($systemattempt.$no_of_attempts);
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $newDate = $date->format('Y-m-d H:i:s');
        $currentdatetime = date('Y-m-d H:i:s', strtotime($newDate . '+' . 'hours'));
        // dd($currentdatetime);
        if (!empty($no_of_attempts[0])) {
        } else {
            $no_of_attempts = "0";
        }
        if (($systemattempt[0]) <= ($no_of_attempts[0])) {
            // dd($block_untill[0] . $newDate);
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
                        ->select(DB::raw('screens.screen_route'))
                        ->join('screens', function ($join) {
                            $join->on('screens.module_id', '=', 'screen_access_roles.module_id');
                        })
                        ->where('screens.screen_route', 'like', '%Dash%')
                        ->where('screen_access_roles.staff_id', '=', $id)
                        ->get();
                    if (!empty($screenroute[0])) {
                        $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                        return $this->createNewToken($token, $tmp);
                    } else {
                        $tmp = "";
                        return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
                    }
                }else{
                    $tmp = "";
            return $this->createNewToken($token, $tmp);
                }
            } else {
                return response()->json(['message' => '1Account has been blocked for next ' . $blocktime[0] . ' hour'], 401);
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
                //throw $th;
            }
            if(!$request->type=="Von"){
            $screenroute = DB::table('screen_access_roles')
                ->select(DB::raw('screens.screen_route'))
                ->join('screens', function ($join) {
                    $join->on('screens.module_id', '=', 'screen_access_roles.module_id');
                })
                ->where('screens.screen_route', 'like', '%Mod%')
                ->where('screen_access_roles.staff_id', '=', $id)
                ->get();
                // dd($screenroute);
            if (!empty($screenroute[0])) {
                $tmp = json_decode(json_encode($screenroute[0]), true)['screen_route'];
                return $this->createNewToken($token, $tmp);
            } else {
                $tmp = "";
                return response()->json(['message' => 'User has not right to access any form. Please contact to Admin', 'code' => '201'], 201);
            }
        }else{
            $tmp = "";
            return $this->createNewToken($token, $tmp);
        }
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
    protected function createNewToken($token, $tmp)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 14400,
            'user' => auth()->user(),
            'route' => $tmp,
            'code' => '200'
        ]);
    }
}
