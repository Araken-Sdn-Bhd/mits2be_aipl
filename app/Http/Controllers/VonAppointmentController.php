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

        $record= DB::table('von_appointment')
        ->select('von_appointment.*','staff_management.name as dr_name','areas_of_involvement.name as aoi')

        ->leftJoin('von_org_representative_background', 'von_org_representative_background.id', '=', 'von_appointment.parent_section_id')
        ->leftJoin('staff_management','von_appointment.interviewer_id','=','staff_management.id')
        ->leftJoin('areas_of_involvement','areas_of_involvement.id','=','von_appointment.area_of_involvement')
        ->where('von_org_representative_background.branch_id',$request->branch_id)
        ->where('von_appointment.status','0');
        
        if($request->name != "" || $request->name != null) {
           $record->where('von_appointment.name', 'like', '%'.$request->name.'%');
          
        }
        if ($request->date != null || $request->date != ""){
            $record->where('von_appointment.booking_date','=', $request->date);
        }
        if ($request->service != null || $request->service != ""){
            $record->where('von_appointment.services_type','=', $request->service);
        }

        $list = $record->get();

        foreach ($list as $item) { 
                    $item->app_date = date('d/m/Y', strtotime($item->booking_date));
                    $item->app_time = date('H:i a', strtotime($item->booking_time));    
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
