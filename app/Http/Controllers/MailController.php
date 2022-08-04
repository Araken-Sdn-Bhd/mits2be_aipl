<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ForgotPasswordEmail;
use Exception;
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
}
