<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use App\Models\PsychiatryClerkingNote;
use App\Models\PatientCounsellorClerkingNotes;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientAppointmentDetailsController extends Controller
{
    public function store(Request $request)
    {
        // patient_mrn_id is treated as patient_id
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'nric_or_passportno' => 'required|string',
            'booking_date' => 'required',
            // 'patient_mrn_id' => 'required', 
            'booking_time' => 'required',
            'duration' => 'required|integer',
            'appointment_type' => 'required|integer',
            'type_visit' => 'required|integer',
            'patient_category' => 'required|integer',
            'assign_team' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $nric_or_passportno = $request->nric_or_passportno;
        $getmnr_id = PatientRegistration::select('id')
            ->where('nric_no', $nric_or_passportno)
            ->orWhere('passport_no', $nric_or_passportno)
            ->pluck('id');
        // dd($getmnr_id);
        // $chkPoint1 =  PatientRegistration::where(function ($query) use ($nric_or_passportno) {
        // $query->where('nric_no','=', $nric_or_passportno);
        // })->where('status', '1')->get();

        if (count($getmnr_id) == 0) {
            return response()->json(["message" => "This user is not registered", "code" => 401]);
        } else {
            $booking_date = $request->booking_date;
            $booking_time = $request->booking_time;
            $assign_team = $request->assign_team;
            $chkPoint =  PatientAppointmentDetails::where(function ($query) use ($booking_date, $booking_time, $assign_team) {
                $query->where('booking_date', '=', $booking_date)->where('booking_time', '=', $booking_time)->where('assign_team', '=', $assign_team);
            })->where('status', '1')->get();

            if ($chkPoint->count() == 0) {
                $service = [
                    'added_by' => $request->added_by,
                    'nric_or_passportno' => $request->nric_or_passportno,
                    'booking_date' => $request->booking_date,
                    'booking_time' => $request->booking_time,
                    'patient_mrn_id' => $getmnr_id[0],
                    'duration' => $request->duration,
                    'appointment_type' => $request->appointment_type,
                    'type_visit' => $request->type_visit,
                    'patient_category' => $request->patient_category,
                    'assign_team' => $request->assign_team
                ];
                PatientAppointmentDetails::firstOrCreate($service);
                return response()->json(["message" => "Patient Appointment Created Successfully!", "code" => 200]);
            } else {
                return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
            }
        }
    }

    public function checkNricNoORPassport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nric_or_passportno' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $nric_or_passportno = $request->nric_or_passportno;
        $chkPoint1 =  PatientRegistration::where(function ($query) use ($nric_or_passportno) {
            $query->select('id')->where('nric_no', '=', $nric_or_passportno);
        })->where('status', '1')->get();
        if ($chkPoint1->count() == 0) {
            return response()->json(["message" => "This user is not registered", "code" => 401]);
        } else {
            return response()->json(["message" => "This user is registered", 'list' => $chkPoint1, "code" => 200]);
        }
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'nric_or_passportno' => 'required|string',
            'booking_date' => 'required',
            'patient_mrn_id' => 'required',
            'booking_time' => 'required',
            'duration' => 'required|integer',
            'appointment_type' => 'required|integer',
            'type_visit' => 'required|integer',
            'patient_category' => 'required|integer',
            'assign_team' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $booking_date = $request->booking_date;
        $booking_time = $request->booking_time;
        $assign_team = $request->assign_team;
        $chkPoint =  PatientAppointmentDetails::where(function ($query) use ($booking_date, $booking_time, $assign_team) {
            $query->where('booking_date', '=', $booking_date)->where('booking_time', '=', $booking_time)->where('assign_team', '=', $assign_team);
        })->where('id', '!=', $request->id)->where('status', '1')->get();
        if ($chkPoint->count() == 0) {
            PatientAppointmentDetails::where(
                ['id' => $request->id]
            )->update([
                'added_by' => $request->added_by,
                'nric_or_passportno' => $request->nric_or_passportno,
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'patient_mrn_id' => $request->patient_mrn_id,
                'duration' => $request->duration,
                'appointment_type' => $request->appointment_type,
                'type_visit' => $request->type_visit,
                'patient_category' => $request->patient_category,
                'assign_team' => $request->assign_team
            ]);
            return response()->json(["message" => "Appointment Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
        }
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'appointment_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        PatientAppointmentDetails::where(
            ['id' => $request->appointment_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Appointment Deleted Successfully!", "code" => 200]);
    }

    public function getPatientAppointmentDetailsList()
    {
        $resultSet = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'appointment_status')
            ->where('status', '1')
            ->get()->toArray();
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id')->where('id', $val['patient_mrn_id'])->with('salutation:section_value,id')->with('service:service_name,id')
                    ->get();

                $result[$key]['patient_id'] = $patient[0]['id'];
                $result[$key]['patient_mrn'] = $patient[0]['patient_mrn'];
                $result[$key]['name_asin_nric'] = $patient[0]['name_asin_nric'];
                $result[$key]['nric_no'] = $patient[0]['nric_no'];
                $result[$key]['passport_no'] = $patient[0]['passport_no'];
                $result[$key]['salutation'] = $patient[0]['salutation'][0]['section_value'];
                //  dd($patient[0]['appointments']);
                if ($patient[0]['appointments'] != null) {
                    if ($patient[0]['service'] != null) {
                        $result[$key]['service'] = $patient[0]['service']['service_name'];
                    } else {
                        $result[$key]['service'] = 'NA';
                    }
                    $result[$key]['appointment_id'] = $val['id'];
                    $result[$key]['appointment_date'] = $val['booking_date'];
                    $result[$key]['appointment_time'] = date('H:i', strtotime($val['booking_time']));
                    $result[$key]['appointment_status'] = $val['appointment_status'];
                    $team_id = $val['assign_team'];
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                    $result[$key]['team_name'] = $teamName[0]['team_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                    $result[$key]['appointments'] = 'NA';
                    $result[$key]['team_name'] = 'NA';
                }
                //  dd($result);
            }
        }

        return response()->json(["message" => "Appointment List.", 'list' => $result, "code" => 200]);
    }

    public function getPatientAppointmentDetailsListById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'appointment_status', 'assign_team')
            ->where('id', '=', $request->appointment_id)
            ->where('status', '1')->get();
        return response()->json(["message" => "Appointment List.", 'list' => $list, "code" => 200]);
    }

    public function getPatientAppointmentDetailsOfPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'appointment_status', 'type_visit', 'patient_category', 'assign_team')
            ->where('patient_mrn_id', '=', $request->patient_id)
            ->where('status', '1')->get();
        return response()->json(["message" => "Appointment Patient List.", 'list' => $list, "code" => 200]);
    }

    public function searchPatientListByBranchIdOrServiceIdOrByName(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'service_id' => 'required|integer',
            'keyword' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->keyword == 'no-keyword' && $request->date == 'yyyy-mm-dd' && $request->service_id == '0') {
            $resultSet = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'appointment_status')
                ->where('status', '1')
                ->get()->toArray();
            //return response()->json(["message" => "Appointment List.", 'list' => $list, "code" => 200]);
        }
        $resultSet=[];
        $sql = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'appointment_status')->where('status', '1');
        if ($request->service_id != '0') {
            $sql = $sql->where('appointment_type', '=', $request->service_id);
        }
        if ($request->date != 'yyyy-mm-dd') {
            $sql = $sql->where('booking_date', '=', $request->date);
        }
        if ($request->keyword != 'no-keyword') {
            $searchWord = $request->keyword;
            $ids =  PatientRegistration::select('id')->where(function ($query) use ($searchWord) {
                $query->where('patient_mrn', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('name_asin_nric', 'LIKE', '%' . $searchWord . '%');
            })->get();
            $resultSet = $sql->where(function ($query) use ($searchWord, $ids) {
                $query->where('nric_or_passportno', 'LIKE', '%' . $searchWord . '%')
                    ->orWhereIn('patient_mrn_id',  $ids);
            });
            // dd($sql);
            $resultSet = $sql->toArray();
        }
        //dd($resultSet);
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id')->where('id', $val['patient_mrn_id'])->with('salutation:section_value,id')->with('service:service_name,id')
                    ->get();
                $result[$key]['patient_id'] = $patient[0]['id'];
                $result[$key]['patient_mrn'] = $patient[0]['patient_mrn'];
                $result[$key]['name_asin_nric'] = $patient[0]['name_asin_nric'];
                $result[$key]['nric_no'] = $patient[0]['nric_no'];
                $result[$key]['passport_no'] = $patient[0]['passport_no'];
                $result[$key]['salutation'] = $patient[0]['salutation'][0]['section_value'];
                //  dd($patient[0]['appointments']);
                if ($patient[0]['appointments'] != null) {
                    if ($patient[0]['service'] != null) {
                        $result[$key]['service'] = $patient[0]['service']['service_name'];
                    } else {
                        $result[$key]['service'] = 'NA';
                    }
                    $result[$key]['appointment_id'] = $val['id'];
                    $result[$key]['appointment_date'] = $val['booking_date'];
                    $result[$key]['appointment_time'] = date('H:i', strtotime($val['booking_time']));
                    $result[$key]['appointment_status'] = $val['appointment_status'];
                    $team_id = $val['assign_team'];
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                    $result[$key]['team_name'] = $teamName[0]['team_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                    $result[$key]['appointments'] = 'NA';
                    $result[$key]['team_name'] = 'NA';
                }
                //  dd($result);
            }
        }

        return response()->json(["message" => "Appointment List.", 'list' => $result, "code" => 200]);
    }

    public function updateappointmentstatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|integer',
            'appointment_status' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        PatientAppointmentDetails::where(
            ['id' => $request->appointment_id]
        )->update([
            'appointment_status' =>  $request->appointment_status
        ]);

        return response()->json(["message" => "Appointment Status Updated Successfully!", "code" => 200]);
    }

    public function getNextPrev(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $next = PatientAppointmentDetails::where('appointment_status', '0')->where('patient_mrn_id', $request->patient_id)->orderBy('booking_date', 'asc')
            ->get()->pluck('booking_date')->toArray();
        $prev = PatientAppointmentDetails::where('appointment_status', '1')->where('patient_mrn_id', $request->patient_id)->orderBy('booking_date', 'desc')
            ->get()->pluck('booking_date')->toArray();

        $result = ['next' => ((empty($next))  ? 'NA' : date('d/m/Y', strtotime($next[0]))), 'prev' => ((empty($prev)) ? 'NA' : date('d/m/Y', strtotime($prev[0])))];
        return response()->json(["message" => "Appointment List!", 'result' => $result, "code" => 200]);
    }

    public function updateTeamDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'appointment_id' => 'required|integer',
            'assign_team' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        PatientAppointmentDetails::where(
            ['id' => $request->appointment_id]
        )->update([
            'appointment_status' => '1',
            'added_by' => $request->added_by,
            'assign_team' => $request->assign_team
        ]);

        return response()->json(["message" => "Assigned Team has been update Successfully!", "code" => 200]);
    }
    public function fetchViewHistoryList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list=[];
        $Psychiatry_Clerking_Note=[];
        $Counsellor_Clerking_Note=[];
        $Psychiatry_Clerking_Note = DB::table('psychiatry_clerking_note')
            ->join('staff_management', 'psychiatry_clerking_note.added_by', '=', 'staff_management.id')
            ->select(DB::raw("(CASE WHEN TIME(psychiatry_clerking_note.created_at) BETWEEN '00:00:00' AND '11:59:59' THEN DATE_FORMAT(psychiatry_clerking_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(psychiatry_clerking_note.created_at, '%h:%i PM')
       END) as time"),DB::raw("DATE_FORMAT(psychiatry_clerking_note.created_at, '%d-%m-%Y') as date"),'psychiatry_clerking_note.status','psychiatry_clerking_note.id','staff_management.name',DB::raw("'PsychiatryClerkingNote' as type"),DB::raw("'Psychiatry Clerking Note' as section_name"))
            ->where('psychiatry_clerking_note.patient_mrn_id', $request->patient_id)
             ->orderBy('psychiatry_clerking_note.created_at', 'asc')
            ->get();

            $Counsellor_Clerking_Note = DB::table('patient_counsellor_clerking_notes')
            ->join('staff_management', 'patient_counsellor_clerking_notes.added_by', '=', 'staff_management.id')
            
            ->select(DB::raw("(CASE WHEN TIME(patient_counsellor_clerking_notes.created_at) BETWEEN '00:00:00' AND '11:59:59' THEN DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%h:%i PM')
       END)  as time"),DB::raw("DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%d-%m-%Y') as date"),'patient_counsellor_clerking_notes.status','patient_counsellor_clerking_notes.id','staff_management.name',DB::raw("'CounsellorClerkingNote' as type"),DB::raw("'Counsellor Clerking Note' as section_name"))
            ->where('patient_counsellor_clerking_notes.patient_mrn_id', $request->patient_id)
             ->orderBy('patient_counsellor_clerking_notes.created_at', 'asc')
            ->get();

            foreach($Psychiatry_Clerking_Note as $key =>$val){
                $list[] = $val;
            }
            foreach($Counsellor_Clerking_Note as $key =>$val){
                $list[] = $val;
            }
          // $list["Psychiatry_Clerking_Note"]=$Psychiatry_Clerking_Note;
          // $list["Counsellor_Clerking_Note"]=$Counsellor_Clerking_Note;

       
        return response()->json(["message" => "List", 'Data' => $list, "code" => 200]);
    }

    public function fetchViewHistoryListDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if($request->type=="PsychiatryClerkingNote"){
        $list = PsychiatryClerkingNote::select('*')
            ->where('id', '=', $request->id)
            // ->where('status', '1')
            ->get();
    }
            if($request->type=="CounsellorClerkingNote"){
            $list = PatientCounsellorClerkingNotes::select('*')
            ->where('id', '=', $request->id)
            // ->where('status', '1')
            ->get();
    }
        return response()->json(["message" => "List", 'Data' => $list, "code" => 200]);
    }
}
