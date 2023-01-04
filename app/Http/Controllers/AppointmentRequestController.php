<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentRequest;
use DateTime;
use DateTimeZone;
use Exception;
use App\Models\Notifications;
use App\Models\ScreenPageModule;
use Validator;
use Illuminate\Support\Facades\DB;

class AppointmentRequestController extends Controller
{
    public function addRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer',
            'name' => 'required|string', 
            'contact_no' => 'required|string',
            'email' => 'required|string',
            'patient_mrn_id' => '',
            'ip_address' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $appointmentrequest = [
            'branch_id' =>  $request->branch_id,
            'name' =>  $request->name,
            'contact_no' =>  $request->contact_no,
            'address' =>  $request->address,
            'email' =>  $request->email,
            'patient_mrn_id' =>  '',
            'nric_or_passportno' =>  $request->nric_or_passportno,
            'address1' =>  $request->address1,
            'ip_address' =>  $request->ip_address,
        ];
        try {
            $HOD = AppointmentRequest::firstOrCreate($appointmentrequest);

            $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
            $notifi_code='RA';
            $screen_id=ScreenPageModule::select('id','screen_route_alt')->where('notifi_code',$notifi_code)->first();
            $notifi=[
                'added_by' => $request->added_by,
                'patient_mrn' =>   '',
                'branch_id' => $request->branch_id,
                'screen_id' => $screen_id['id'],
                'staff_id'=> '',
                'url_route' => $screen_id['screen_route_alt'],
                'message' =>  'Request for appointment(s)',
                'created_at' => $date->format('Y-m-d H:i:s'),
            ];
            $HOD2 = Notifications::insert($notifi);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'AppointmentRequest' => $appointmentrequest, "code" => 200]);
        }
        return response()->json(["message" => "Appointment Requested", "code" => 200]);
    }

    public function getRequestList(Request $request)
    {

        $list =AppointmentRequest::select('id', 'added_by','branch_id','name',
        'nric_or_passportno', 'contact_no', 'address', 'address1', 'email','ip_address',
        DB::raw("DATE_FORMAT(created_at, '%d-%M-%y') as created"))
        ->where('status', '1')
        ->where('branch_id',$request->branch_id)
        ->get();
     
       return response()->json(["message" => "Appointment Requested List", 'list' => $list, "code" => 200]);
    }

    public function getAllRequestList(Request $request)
    {
        $list=[];
        $list =AppointmentRequest::select('id', 'added_by','branch_id','name','nric_or_passportno', 'contact_no', 'address', 'address1', 'email','ip_address')
        ->where('id',$request->id)
        ->where('status', '1')
        ->get();
        
       return response()->json(["message" => "Appointment Requested List", 'list' => $list, "code" => 200]);
    }
}
