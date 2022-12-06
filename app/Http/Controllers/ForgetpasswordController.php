<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class ForgetpasswordController extends Controller
{
    public function forgetpass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $ss = SystemSetting::select('variable_name','variable_value')->where('section','email-setting')->get();
            $configuration = SystemSetting::select('variable_name','variable_value')->
            where("section", "email-setting")->get();
            dd($configuration);
            if ($configuration['variable_name'][0]=="send-email-from") {
                $a = ($configuration['variable_value']);
                dd($a);
            }
            else if ($configuration['variable_name'][1]=="outgoing-smtp-server") {
                $b = ($configuration['variable_value']);
            }
            else if ($configuration['variable_name'][2]=="login-user-id") {
                $c = ($configuration['variable_value']);
            }
            else if($configuration['variable_name']=="verify-password") {
                $d = ($configuration['variable_value']);
            }
            else if ($configuration['variable_name']=="smtp-port-number") {
                $e = ($configuration['variable_value']);
            }
            else if ($configuration['variable_name']=="security") {
                $f = $configuration['variable_value'];
                dd($f);
            }
            return response()->json(["message" => "Email Successfully Sent","code" => 200]);
        }else{
            return response()->json(["message" => "User does not exist", "code" => 401]);
        }
    }

    public function html_email() {
        $data = array('name'=>"Virat Gandhi");
        Mail::send('mail', $data, function($message) {
           $message->to('guptamanish055@gmail.com', 'Tutorials Point')->subject
              ('Laravel HTML Testing Mail');
           $message->from('guptamanish055@gmail.com','Manish');
        });
     }
}
