<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VonAppointment;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use App\Models\JobCompanies;
use App\Models\AreasOfInvolvement;
use App\Models\EtpRegister;
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
            'appointment_type' => 'required|integer',
            'interviewer_id' => 'required|integer',
            'area_of_involvement' => 'required|integer',
            'services_type' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        // $nric_or_passportno = $request->nric_or_passportno;
        // $getmnr_id = PatientRegistration::select('id')
        //     ->where('nric_no', $nric_or_passportno)
        //     ->orWhere('passport_no', $nric_or_passportno)
        //     ->pluck('id');
        // dd($getmnr_id);
        // $chkPoint1 =  PatientRegistration::where(function ($query) use ($nric_or_passportno) {
        // $query->where('nric_no','=', $nric_or_passportno);
        // })->where('status', '1')->get();

        // if (count($getmnr_id) == 0) {
        //     return response()->json(["message" => "This user is not registered", "code" => 401]);
        // } else {
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
                // 'patient_mrn_id' => $getmnr_id[0],
                'duration' => $request->duration,
                'appointment_type' => $request->appointment_type,
                'interviewer_id' => $request->interviewer_id,
                'area_of_involvement' => $request->area_of_involvement,
                'services_type' => $request->services_type
            ];
            VonAppointment::firstOrCreate($service);
            return response()->json(["message" => "Von Appointment Created Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
        }
        // }
    }

    public function geyVonAppointmentById(Request $request)
    {
        return VonAppointment::select('*', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as start_date"))->where('added_by', $request->added_by)->where('parent_section_id', $request->parent_section_id)->get();
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
            'appointment_type' => 'required|integer',
            'interviewer_id' => 'required|integer',
            'area_of_involvement' => 'required|integer',
            'services_type' => 'required|integer',
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
                'appointment_type' => $request->appointment_type,
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
        $search = [];
        if ($request->date) {
            $search['booking_date'] = $request->date;
        }
        if ($request->service) {
            $search['services_type'] = $request->service;
        }
        if ($request->name) {
            $search['name'] = $request->name;
        }
        $list = [];
        if ($search) {
            $records = VonAppointment::where($search)->get();
        } else {
            $records = VonAppointment::all();
        }
        if ($records) {
            foreach ($records as $key => $val) {
                $list[$key]['id'] = $val['id'];
                $list[$key]['name'] = $val['name'];
                $list[$key]['app_date'] = date('d/m/Y', strtotime($val['booking_date']));
                $list[$key]['app_time'] = date('H:i', strtotime($val['booking_time']));
                $dr = JobCompanies::where('id', $val['interviewer_id'])->get()->pluck('contact_name')->toArray();
                $list[$key]['dr_name'] = $dr[0];
                $aoi = AreasOfInvolvement::where('id', $val['area_of_involvement'])->get()->pluck('name')->toArray();
                $list[$key]['aoi'] = $aoi[0];
                $service = EtpRegister::where('id', $val['services_type'])->get()->pluck('etp_name')->toArray();
                $list[$key]['service'] = $service[0];
            }
        }
        return response()->json(["message" => "Von Updated Successfully!", "list" => $list, "code" => 200]);
        dd($list);
    }
}
