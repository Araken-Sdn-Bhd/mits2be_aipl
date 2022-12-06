<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VonAppointment;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use App\Models\JobCompanies;
use App\Models\AreasOfInvolvement;
use App\Models\EtpRegister;
use App\Models\StaffManagement;
use App\Models\VonOrgRepresentativeBackground;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class VonAppointmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'parent_section_id' => 'required',
            'name' => 'required|string',
            'booking_date' => 'required',
            'booking_time' => 'required',
            'duration' => 'required|integer',
            'interviewer_id' => 'required|integer',
            'area_of_involvement' => 'required|integer',
            'services_type' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $interviewer_id = $request->interviewer_id;
        $chkPoint =  VonAppointment::where(function ($query) use ($booking_date, $booking_time, $interviewer_id) {
            $query->where('booking_date', '=', $booking_date)->where('booking_time', '=', $booking_time)->where('interviewer_id', '=', $interviewer_id);
        })->where('status', '1')->get();

        if ($chkPoint->count() == 0) {
            $service = [
                'added_by' => $request->added_by,
                'name' => $request->name,
                'parent_section_id' => $request->parent_section_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'duration' => $request->duration,
                'interviewer_id' => $request->interviewer_id,
                'area_of_involvement' => $request->area_of_involvement,
                'services_type' => $request->services_type,
                'status' => '0'
            ];
            VonAppointment::create($service);
            return response()->json(["message" => "Von Appointment Created Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
        }
    }

    public function geyVonAppointmentById(Request $request)
    {
        return VonAppointment::select('*', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as start_date"))->where('added_by', $request->added_by)->where('id', $request->id)->get();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'parent_section_id' => 'required',
            'name' => 'required|string',
            'booking_date' => 'required',
            'booking_time' => 'required',
            'duration' => 'required|integer',
            'interviewer_id' => 'required|integer',
            'area_of_involvement' => 'required|integer',
            'services_type' => 'required',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $interviewer_id = $request->interviewer_id;
        $chkPoint =  VonAppointment::where(function ($query) use ($booking_date, $booking_time, $interviewer_id) {
            $query->where('booking_date', '=', $booking_date)->where('booking_time', '=', $booking_time)->where('interviewer_id', '=', $interviewer_id);
        })->where('id', '!=', $request->id)->where('status', '1')->get();
        if ($chkPoint->count() == 0) {
            VonAppointment::where(
                ['id' => $request->id]
            )->update([
                'added_by' => $request->added_by,
                'name' => $request->name,
                'parent_section_id' => $request->parent_section_id,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'duration' => $request->duration,
                'interviewer_id' => $request->interviewer_id,
                'area_of_involvement' => $request->area_of_involvement,
                'services_type' => $request->services_type
            ]);
            return response()->json(["message" => "Von Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
        }
    }

    public function listAppointment(Request $request)
    {

        $role = DB::table('staff_management')
        ->select('roles.code')
        ->join('roles', 'staff_management.role_id', '=', 'roles.id')
        ->where('staff_management.email', '=', $request->email)
        ->first();

        $search = [];
        if ($request->date) {
            $search['booking_date'] = $request->date;
        }
        if ($request->service) {
            $search['services_type'] = $request->service;
        }
        $list = [];
        if($request->name != "" || $request->name != null) {
            $sql = VonAppointment::query();
            $sql = $sql->where('name', 'LIKE', '%' . $request->name. '%');
            if ($request->date != null || $request->date != ""){
                $sql = $sql->where('booking_date','=', $request->date);
            }
            if ($request->service != null || $request->service != ""){
                $sql = $sql->where('services_type','=', $request->service);
            }
            $records = $sql->get();
           
        }else{
            $sql = VonAppointment::query();
            $sql = $sql->where('status','=','0');
            if ($request->date != null || $request->date != ""){
                $sql = $sql->where('booking_date','=', $request->date);
            }
            if ($request->service != null || $request->service != ""){
                $sql = $sql->where('services_type','=', $request->service);
            }
            $records = $sql->get();
        }
        if ($records) {
            foreach ($records as $key => $val) {
                $list[$key]['id'] = $val['id'];
                $list[$key]['name'] = $val['name'];
                $list[$key]['app_date'] = date('d/m/Y', strtotime($val['booking_date']));
                $list[$key]['app_time'] = date('H:i a', strtotime($val['booking_time']));
                $dr = StaffManagement::where('id', $val['interviewer_id'])->get()->pluck('name')->toArray();
                if (!$dr){
                    $list[$key]['dr_name'] = 'NA';
                } else {

                    $list[$key]['dr_name'] = $dr[0];
                }
                $aoi = AreasOfInvolvement::where('id', $val['area_of_involvement'])->get()->pluck('name')->toArray();
                $list[$key]['aoi'] = $aoi[0];
                $list[$key]['service'] = $val['services_type'];
                
            }
        }

        return response()->json(["message" => "Von List", "list" => $list, "code" => 200]);
    }

    public function setStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        VonOrgRepresentativeBackground::where('id', $request->id)->update(['status' => $request->status]);
        VonAppointment::where('id', $request->id)->update(['status' => $request->status]);
        return response()->json(["message" => "Appointment status updated!", "code" => 200]);
    }

    public function setStatusgroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        VonAppointment::where('id', $request->id)->update(['status' => $request->status]);
        return response()->json(["message" => "Appointment status updated!", "code" => 200]);
    }
}
