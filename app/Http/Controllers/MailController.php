<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ForgotPasswordEmail;
use App\Mail\VerifyAccountEmail;
use App\Models\EmployeeRegistration;
use Exception;
use Validator;
use Illuminate\Support\Facades\Crypt;

class MailController extends Controller
{
    public function sendForgotPasswordEmail(Request $request)
    {
        $chkUser = User::where('email', $request->emailAddress)->get()->toArray();
        if ($chkUser) {
            $toEmail    =   $request->emailAddress;
            $data       =   ['id' => Crypt::encryptString($chkUser[0]['id']), 'name' => $chkUser[0]['name'], 'frontEndUrl' => env('FRONTEND_URL')];
            try {
                Mail::to($toEmail)->send(new ForgotPasswordEmail($data));
                return response()->json(["message" => 'Email Sent', "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 500]);
            }
        } else {
            return response()->json(["message" => 'Email-Address Doesn\'t Exists In Our Records.', "code" => 404]);
        }
    }

    public function registerEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:6',
            'contact_number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
            
            try {
                $check = User::where('email', $request->email)->count();
                
                if ($check == 0) {
                    $id=User::create(
                        ['name' => $request->company_name, 'email' => $request->email, 'role' => "Von", 'password' => bcrypt($request->password)]
                    );
                    $userid= $id->id;
                    EmployeeRegistration::create([
                        'company_name' =>  $request->company_name,
                        'contact_number' =>  $request->contact_number,
                        'email' =>  $request->email,
                        'password' => bcrypt($request->password),
                        'user_id' =>  $userid,
                    ]);
                    //dd($role[0]['section_value']);
                    $toEmail    =   $request->email;
                    $data       =   ['user_id' => Crypt::encryptString($userid), 'company_name' =>  $request->company_name, 'frontEndUrl' => env('FRONTEND_URL')];
                    try {
                        Mail::to($toEmail)->send(new VerifyAccountEmail($data));
                        // return response()->json(["message" => 'Email Sent', "code" => 200]);
                        return response()->json(["message" => "User Created Successfully!", "code" => 200]);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), "code" => 500]);
                    }
                    
                    // return response()->json(["message" => "User Created Successfully!", "code" => 200]);
                }
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(),  "code" => 200]);
            }
            return response()->json(["message" => "Employee already exists!", "code" => 200]);






        // $chkUser = User::where('email', $request->emailAddress)->get()->toArray();
        // if ($chkUser) {
           
       
        // else {
        //     return response()->json(["message" => 'Email-Address Doesn\'t Exists In Our Records.', "code" => 404]);
        // }
    }
}
