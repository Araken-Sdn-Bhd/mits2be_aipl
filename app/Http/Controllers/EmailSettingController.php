<?php

namespace App\Http\Controllers;

use App\Mail\EmailTest;
use App\Mail\TestMail as testEmail;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Validator;

class EmailSettingController extends Controller
{
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'send_email_from' => 'required',
            'outgoing_smtp_server' => 'required',
            'login_user_id' => 'required|string',
            'login_password' => 'required|string',
            'verify_password' => 'required|string',
            'smtp_port_number' => 'required|string',
            'security' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $checkpatientid = EmailSetting::select('id')
            ->pluck('id');
        if (count($checkpatientid) == 0) {
            $alert = [
                'send_email_from' =>  $request->send_email_from,
                'outgoing_smtp_server' =>  $request->outgoing_smtp_server,
                'login_user_id' =>  $request->login_user_id,
                'login_password' =>  $request->login_password,
                'verify_password' =>  $request->verify_password,
                'smtp_port_number' =>  $request->smtp_port_number,
                'security' =>  $request->security,
            ];
            try {
                $HOD = EmailSetting::updateOrCreate($alert);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Email Created' => $alert, "code" => 200]);
            }
            return response()->json(["message" => "Email Created", "code" => 200]);
        } else {
            $alertupdate = [
                'send_email_from' =>  $request->send_email_from,
                'outgoing_smtp_server' =>  $request->outgoing_smtp_server,
                'login_user_id' =>  $request->login_user_id,
                'login_password' =>  $request->login_password,
                'verify_password' =>  $request->verify_password,
                'smtp_port_number' =>  $request->smtp_port_number,
                'security' =>  $request->security,
            ];

            $sd = EmailSetting::where('id', $checkpatientid[0])->update($alertupdate);
            if ($sd)
                return response()->json(["message" => "Email Setting Updated Successfully!", "code" => 200]);
        }
    }
    public function getEmail()
    {
        $email = EmailSetting::select('*')->get();
        return response()->json(["message" => "Email Setting!", 'list' => $email, "code" => 200]);
    }

    public function testEmail(Request $request)
    {
        $toEmail    =   $request->send_email_from;
        $target = $request->outgoing_smtp_server;
        $port = $request->smtp_port_number;
        $error_number = "";
        $error_string = "";
        $timeout = 9;
        $newline = "\n\r";
        $log = [];

        $data = array(
            'email' => $request->target_email,
            'name' => 'Test MITS 2.0 CONFIG'
        );
        if ($request->target_email == null || $request->target_email == ""){
            return response([
                'message' => 'Target Email in empty, Please insert a target email in the test send email input box.',
                'code' => 500
            ]);
        } else {
        try {
                //test send mail
                Mail::to($data['email'])->send(new TestEmail($data));
                /// Server Connection

                return response([
                    'message' => 'Email setting successfully connected.',
                    'code' => 200
                ]);
            } catch (\Exception $err) {
                var_dump($err);

                return response([
                    'message' => 'Error In Email Configuration: ' . $err,
                    'code' => 500
                ]);
            }
        }
    }
}
