<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use Illuminate\Http\Request;
Use Exception;
use Validator;

class EmailSettingController extends Controller
{
    public function store(Request $request){

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
            ->where('id', $request->id)
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
                       $HOD = EmailSetting::create($alert);
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
        
                $sd = EmailSetting::where('id', $request->id)->update($alertupdate);
                if ($sd)
                    return response()->json(["message" => "Email Setting Updated Successfully!", "code" => 200]);
            }
       
    }
}
