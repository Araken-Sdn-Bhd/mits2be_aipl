<?php

namespace App\Http\Controllers;

use App\Models\ConsultationDischargeNote;
use App\Models\CounsellingProgressNote;
use App\Models\CpsDischargeNote;
use App\Models\CpsHomevisitConsentForm;
use App\Models\CpsHomevisitWithdrawalForm;
use App\Models\CpsPoliceReferralForm;
use App\Models\CpsProgressNote;
use App\Models\CPSReferralForm;
use App\Models\EtpConsentForm;
use App\Models\EtpProgressNote;
use App\Models\ExternalReferralForm;
use Illuminate\Http\Request;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientRegistration;
use App\Models\HospitalBranchTeamManagement;
use App\Models\InternalReferralForm;
use App\Models\JobClubConsentForm;
use App\Models\JobClubProgressNote;
use App\Models\JobDescription;
use App\Models\JobEndReport;
use App\Models\JobInterestChecklist;
use App\Models\JobInterestList;
use App\Models\JobSearchList;
use App\Models\JobStartForm;
use App\Models\JobTransitionReport;
use App\Models\LASERAssesmenForm;
use App\Models\ListJobClub;
use App\Models\ListOfETP;
use App\Models\ListOfJobSearch;
use App\Models\ListPreviousCurrentJob;
use App\Models\LogMeetingWithEmployer;
use App\Models\Occt_Referral_Form;
use App\Models\PatientCarePaln;
use App\Models\PsychiatryClerkingNote;
use App\Models\PatientCounsellorClerkingNotes;
use App\Models\PatientIndexForm;
use App\Models\PhotographyConsentForm;
use App\Models\PreviousOrCurrentJobRecord;
use App\Models\PsychiatricProgressNote;
use App\Models\PsychologyReferral;
use App\Models\RehabDischargeNote;
use App\Models\RehabReferralAndClinicalForm;
use App\Models\SEConsentForm;
use App\Models\SeProgressNote;
use App\Models\TriageForm;
use App\Models\WorkAnalysisForm;
use App\Models\WorkAnalysisJobSpecification;
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

        if (count($getmnr_id) == 0) {
            return response()->json(["message" => "This user is not registered", "code" => 401]);
        } else {
            $booking_date = $request->booking_date;
            $booking_time = $request->booking_time;
            $assign_team = $request->assign_team;
            // $startTime = date("H:i", strtotime('0 minutes', $booking_time));
            $endTime = date("H:i", strtotime('+30 minutes', strtotime($booking_time)));

            $chkPoint =  PatientAppointmentDetails::where(function ($query) use ($booking_date, $booking_time, $assign_team, $endTime) {
                $query->where('booking_date', '=', $booking_date)->whereBetween('booking_time', [$booking_time, $endTime])->where('assign_team', '=', $assign_team);
            })->where('status', '1')->get();
            //dd($chkPoint);
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
                PatientAppointmentDetails::create($service);
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
            ->with('service:service_name,id')
            ->where('status', '1')
            ->get()
            ->toArray();
        $result = [];
        $list123 = HospitalBranchTeamManagement::select('id', 'hospital_branch_name', 'team_name', 'hospital_code')->where('status', '=', '1')->get();
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient = [];
                $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id')
                    ->where('id', $val['patient_mrn_id'])
                    ->with('salutation:section_value,id')
                    ->get()
                    ->toArray();
                    // dd($patient);
                if (($patient)) {
                    $resultChunk = [];
                    $resultChunk['patient_id'] = $patient[0]['id'] ?: 'NA';
                    $resultChunk['patient_mrn'] = $patient[0]['patient_mrn'] ?: 'NA';
                    $resultChunk['name_asin_nric'] = $patient[0]['name_asin_nric'];
                    $resultChunk['nric_no'] = $patient[0]['nric_no'] ?: 'NA';
                    $resultChunk['passport_no'] = $patient[0]['passport_no'] ?: 'NA';
                    // $resultChunk['salutation'] = $patient[0]['salutation'][0]['section_value'] ?: 'NA';

                    if ($patient[0]['salutation']  != null) {
                        $resultChunk['salutation'] = $patient[0]['salutation'][0]['section_value'] ?: 'NA';
                    } else {
                        $resultChunk['salutation'] = 'NA';
                    }

                    if ($val['service'] != null) {
                        $resultChunk['service'] = $val['service']['service_name'];
                    } else {
                        $resultChunk['service'] = 'NA';
                    }
                    $resultChunk['appointment_id'] = $val['id'] ?: 'NA';
                    $resultChunk['appointment_date'] = $val['booking_date'] ?: 'NA';
                    $resultChunk['appointment_time'] = date('H:i', strtotime($val['booking_time'])) ?: 'NA';
                    $resultChunk['appointment_status'] = $val['appointment_status'] ?: 'NA';
                    $team_id = $val['assign_team'] ?: 'NA';
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get()->pluck('team_name');
                    //  print_r($teamName);
                    $resultChunk['team_name'] = (count($teamName) > 0) ? $teamName[0] : 'NA';
                    $result[] = $resultChunk;
                }
            }
        }

        return response()->json(["message" => "Appointment List.", 'list' => $result, 'list123' => $list123, "code" => 200]);
    }

    public function getPatientAppointmentDetailsTodayList()
    {
        $resultSet = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'appointment_status')
            ->with('service:service_name,id')
            ->where('status', '1')
            ->where('booking_date', date('Y-m-d'))
            ->get()
            ->toArray();
        $result = [];
        $list123 = HospitalBranchTeamManagement::select('id', 'hospital_branch_name', 'team_name', 'hospital_code')->where('status', '=', '1')->get();
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient = [];
                $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id')
                    ->where('id', $val['patient_mrn_id'])
                    ->with('salutation:section_value,id')
                    ->get()
                    ->toArray();
                if (($patient)) {
                    $resultChunk = [];
                    $resultChunk['patient_id'] = $patient[0]['id'] ?: 'NA';
                    $resultChunk['patient_mrn'] = $patient[0]['patient_mrn'] ?: 'NA';
                    $resultChunk['name_asin_nric'] = $patient[0]['name_asin_nric'];
                    $resultChunk['nric_no'] = $patient[0]['nric_no'] ?: 'NA';
                    $resultChunk['passport_no'] = $patient[0]['passport_no'] ?: 'NA';
                    // $resultChunk['salutation'] = $patient[0]['salutation'][0]['section_value'] ?: 'NA';

                    if ($patient[0]['salutation']  != null) {
                        $resultChunk['salutation'] = $patient[0]['salutation'][0]['section_value'] ?: 'NA';
                    } else {
                        $resultChunk['salutation'] = 'NA';
                    }
                    if ($val['service'] != null) {
                        $resultChunk['service'] = $val['service']['service_name'];
                    } else {
                        $resultChunk['service'] = 'NA';
                    }
                    $resultChunk['appointment_id'] = $val['id'] ?: 'NA';
                    $resultChunk['appointment_date'] = $val['booking_date'] ?: 'NA';
                    $resultChunk['appointment_time'] = date('H:i', strtotime($val['booking_time'])) ?: 'NA';
                    $resultChunk['appointment_status'] = $val['appointment_status'] ?: 'NA';
                    $team_id = $val['assign_team'] ?: 'NA';
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get()->pluck('team_name');
                    //  print_r($teamName);
                    $resultChunk['team_name'] = (count($teamName) > 0) ? $teamName[0] : 'NA';
                    $result[] = $resultChunk;
                }
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
                ->with('service:service_name,id')
                ->where('status', '1')
                ->get()->toArray();
        }
        $resultSet = [];
        $sql = PatientAppointmentDetails::select('id', 'nric_or_passportno', 'patient_mrn_id', 'booking_date', 'booking_time', 'duration', 'appointment_type', 'type_visit', 'patient_category', 'assign_team', 'appointment_status')
            ->with('service:service_name,id')
            ->where('status', '1');
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
        }
        $resultSet = $sql->get()->toArray();
        // dd($resultSet);
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id')->where('id', $val['patient_mrn_id'])
                    ->with('salutation:section_value,id')
                    ->get();
                // dd($patient[0]['patient_mrn']);
                $result[$key]['patient_id'] = $patient[0]['id'] ??  'NA';
                $result[$key]['patient_mrn'] = $patient[0]['patient_mrn'] ??  'NA';
                $result[$key]['name_asin_nric'] = $patient[0]['name_asin_nric'] ??  'NA';
                $result[$key]['nric_no'] = $patient[0]['nric_no'] ??  'NA';
                $result[$key]['passport_no'] = $patient[0]['passport_no'] ??  'NA';
                $result[$key]['salutation'] = $patient[0]['salutation'][0]['section_value'] ??  'NA';
                if ($val['service'] != null) {
                    $result[$key]['service'] = $val['service']['service_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                }
                $result[$key]['appointment_id'] = $val['id'] ??  'NA';
                $result[$key]['appointment_date'] = $val['booking_date'] ??  'NA';
                $result[$key]['appointment_time'] = date('H:i', strtotime($val['booking_time'])) ??  'NA';
                $result[$key]['appointment_status'] = $val['appointment_status'] ??  'NA';
                $team_id = $val['assign_team'] ??  'NA';
                $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get()->pluck('team_name');
                $result[$key]['team_name'] = (count($teamName) > 0) ? $teamName[0] : 'NA';
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
        $list = [];
        $Psychiatry_Clerking_Note = [];
        $Counsellor_Clerking_Note = [];
        $Psychiatry_Clerking_Note = DB::table('psychiatry_clerking_note')
            // ->join('staff_management', 'psychiatry_clerking_note.added_by', '=', 'staff_management.id')
            ->join('users', 'psychiatry_clerking_note.added_by', '=', 'users.id')
            ->select(DB::raw("(CASE WHEN TIME(psychiatry_clerking_note.created_at) BETWEEN '00:00:00' AND '11:59:59' THEN DATE_FORMAT(psychiatry_clerking_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(psychiatry_clerking_note.created_at, '%h:%i PM')
       END) as time"), DB::raw("DATE_FORMAT(psychiatry_clerking_note.created_at, '%d-%m-%Y') as date"), 'psychiatry_clerking_note.status', 'psychiatry_clerking_note.id', 'users.name', DB::raw("'PsychiatryClerkingNote' as type"), DB::raw("'Psychiatry Clerking Note' as section_name"),"psychiatry_clerking_note.created_at")
            ->where('psychiatry_clerking_note.patient_mrn_id', $request->patient_id)
            ->orderBy('psychiatry_clerking_note.created_at', 'asc')
            ->get();

        $Counsellor_Clerking_Note = DB::table('patient_counsellor_clerking_notes')
            //  ->join('staff_management', 'patient_counsellor_clerking_notes.added_by', '=', 'staff_management.id')
            ->join('users', 'patient_counsellor_clerking_notes.added_by', '=', 'users.id')

            ->select(DB::raw("(CASE WHEN TIME(patient_counsellor_clerking_notes.created_at) BETWEEN '00:00:00' AND '11:59:59' THEN DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%h:%i PM')
       END)  as time1"), DB::raw("DATE_FORMAT(patient_counsellor_clerking_notes.created_at, '%d-%m-%Y') as date"), 'patient_counsellor_clerking_notes.status', 'patient_counsellor_clerking_notes.id', 'users.name', DB::raw("'CounsellorClerkingNote' as type"), DB::raw("'Counsellor Clerking Note' as section_name"),
       "patient_counsellor_clerking_notes.created_at")
            ->where('patient_counsellor_clerking_notes.patient_mrn_id', $request->patient_id)
            ->orderBy('patient_counsellor_clerking_notes.created_at', 'asc')
            ->get();

        $patient_index_form = DB::table('patient_index_form')
            ->join('users', 'patient_index_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(patient_index_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(patient_index_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(patient_index_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(patient_index_form.created_at, '%d-%m-%Y') as date"),
                'patient_index_form.status',
                'patient_index_form.id',
                'users.name',
                DB::raw("'PatientIndexForm' as type"),
                DB::raw("1 as editstatus"),
                DB::raw("'Patient Index Form' as section_name"),
                "patient_index_form.created_at"
            )
            ->where('patient_index_form.patient_mrn_id', $request->patient_id)
            ->orderBy('patient_index_form.created_at', 'asc')
            ->get();

        $psychiatric_progress_note = DB::table('psychiatric_progress_note')
            ->join('users', 'psychiatric_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(psychiatric_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(psychiatric_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(psychiatric_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(psychiatric_progress_note.created_at, '%d-%m-%Y') as date"),
                'psychiatric_progress_note.status',
                'psychiatric_progress_note.id',
                'users.name',
                DB::raw("'PsychiatricProgressNote' as type"),
                DB::raw("'Psychiatric Progress Note' as section_name"),
                "psychiatric_progress_note.created_at"
            )
            ->where('psychiatric_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('psychiatric_progress_note.created_at', 'asc')
            ->get();

        $cps_progress_note = DB::table('cps_progress_note')
            ->join('users', 'cps_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_progress_note.created_at, '%d-%m-%Y') as date"),
                'cps_progress_note.status',
                'cps_progress_note.id',
                'users.name',
                DB::raw("'CPSProgressNote' as type"),
                DB::raw("'CPS Progress Note' as section_name"),
                "cps_progress_note.created_at"
            )
            ->where('cps_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('cps_progress_note.created_at', 'asc')
            ->get();

        $se_progress_note = DB::table('se_progress_note')
            ->join('users', 'se_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(se_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(se_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(se_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(se_progress_note.created_at, '%d-%m-%Y') as date"),
                'se_progress_note.status',
                'se_progress_note.id',
                'users.name',
                DB::raw("'SEProgressNote' as type"),
                DB::raw("'SE Progress Note' as section_name"),
                "se_progress_note.created_at"
            )
            ->where('se_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('se_progress_note.created_at', 'asc')
            ->get();

        $counselling_progress_note = DB::table('counselling_progress_note')
            ->join('users', 'counselling_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(counselling_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(counselling_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(counselling_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(counselling_progress_note.created_at, '%d-%m-%Y') as date"),
                'counselling_progress_note.status',
                'counselling_progress_note.id',
                'users.name',
                DB::raw("'CounsellingProgressNote' as type"),
                DB::raw("'Counselling Progress Note' as section_name"),
                "counselling_progress_note.created_at"
            )
            ->where('counselling_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('counselling_progress_note.created_at', 'asc')
            ->get();

        $etp_progress_note = DB::table('etp_progress_note')
            ->join('users', 'etp_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(etp_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(etp_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(etp_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(etp_progress_note.created_at, '%d-%m-%Y') as date"),
                'etp_progress_note.status',
                'etp_progress_note.id',
                'users.name',
                DB::raw("'EtpProgressNote' as type"),
                DB::raw("'Etp Progress Note' as section_name"),
                "etp_progress_note.created_at"
            )
            ->where('etp_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('etp_progress_note.created_at', 'asc')
            ->get();

        $job_club_progress_note = DB::table('job_club_progress_note')
            ->join('users', 'job_club_progress_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_club_progress_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_club_progress_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_club_progress_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_club_progress_note.created_at, '%d-%m-%Y') as date"),
                'job_club_progress_note.status',
                'job_club_progress_note.id',
                'users.name',
                DB::raw("'JobClubProgressNote' as type"),
                DB::raw("'Job Club Progress Note' as section_name"),
                "job_club_progress_note.created_at"
            )
            ->where('job_club_progress_note.patient_mrn_id', $request->patient_id)
            ->orderBy('job_club_progress_note.created_at', 'asc')
            ->get();

        $consultation_discharge_note = DB::table('consultation_discharge_note')
            ->join('users', 'consultation_discharge_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(consultation_discharge_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(consultation_discharge_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(consultation_discharge_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(consultation_discharge_note.created_at, '%d-%m-%Y') as date"),
                'consultation_discharge_note.status',
                'consultation_discharge_note.id',
                'users.name',
                DB::raw("'ConsultationDischargeNote' as type"),
                DB::raw("'Consultation Discharges Note' as section_name"),
                "consultation_discharge_note.created_at"
            )
            ->where('consultation_discharge_note.patient_id', $request->patient_id)
            ->orderBy('consultation_discharge_note.created_at', 'asc')
            ->get();

        $rehab_discharge_note = DB::table('rehab_discharge_note')
            ->join('users', 'rehab_discharge_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(rehab_discharge_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(rehab_discharge_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(rehab_discharge_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(rehab_discharge_note.created_at, '%d-%m-%Y') as date"),
                'rehab_discharge_note.status',
                'rehab_discharge_note.id',
                'users.name',
                DB::raw("'RehabDischargeNote' as type"),
                DB::raw("'Rehab Discharges Note' as section_name"),
                "rehab_discharge_note.created_at"
            )
            ->where('rehab_discharge_note.patient_mrn_id', $request->patient_id)
            ->orderBy('rehab_discharge_note.created_at', 'asc')
            ->get();

        $cps_discharge_note = DB::table('cps_discharge_note')
            ->join('users', 'cps_discharge_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_discharge_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_discharge_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_discharge_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_discharge_note.created_at, '%d-%m-%Y') as date"),
                'cps_discharge_note.status',
                'cps_discharge_note.id',
                'users.name',
                DB::raw("'CpsDischargeNote' as type"),
                DB::raw("'Cps Discharges Note' as section_name"),
                "cps_discharge_note.created_at"
            )
            ->where('cps_discharge_note.patient_mrn_id', $request->patient_id)
            ->orderBy('cps_discharge_note.created_at', 'asc')
            ->get();

        $cps_discharge_note = DB::table('cps_discharge_note')
            ->join('users', 'cps_discharge_note.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_discharge_note.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_discharge_note.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_discharge_note.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_discharge_note.created_at, '%d-%m-%Y') as date"),
                'cps_discharge_note.status',
                'cps_discharge_note.id',
                'users.name',
                DB::raw("'CpsDischargeNote' as type"),
                DB::raw("'Cps Discharges Note' as section_name"),
                "cps_discharge_note.created_at"
            )
            ->where('cps_discharge_note.patient_mrn_id', $request->patient_id)
            ->orderBy('cps_discharge_note.created_at', 'asc')
            ->get();

        $cps_homevisit_consent_form = DB::table('cps_homevisit_consent_form')
            ->join('users', 'cps_homevisit_consent_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_homevisit_consent_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_homevisit_consent_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_homevisit_consent_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_homevisit_consent_form.created_at, '%d-%m-%Y') as date"),
                'cps_homevisit_consent_form.consent_for_homevisit',
                'cps_homevisit_consent_form.id',
                'users.name',
                DB::raw("'CpsHomeVisitConsentForm' as type"),
                DB::raw("'1' as status"),
                DB::raw("'Cps Home Visit Consent Form' as section_name"),
                "cps_homevisit_consent_form.created_at"
            )
            ->where('cps_homevisit_consent_form.patient_id', $request->patient_id)
            ->orderBy('cps_homevisit_consent_form.created_at', 'asc')
            ->get();

            $cps_homevisit_withdrawal_form = DB::table('cps_homevisit_withdrawal_form')
            ->join('users', 'cps_homevisit_withdrawal_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_homevisit_withdrawal_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_homevisit_withdrawal_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_homevisit_withdrawal_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_homevisit_withdrawal_form.created_at, '%d-%m-%Y') as date"),
                'cps_homevisit_withdrawal_form.community_psychiatry_services',
                'cps_homevisit_withdrawal_form.id',
                'users.name',
                DB::raw("'CpsHomeVisitWithdrawalForm' as type"),
                DB::raw("'1' as status"),
                DB::raw("'Cps Home Visit Withdraw Form' as section_name"),
                "cps_homevisit_withdrawal_form.created_at"
            )
            ->where('cps_homevisit_withdrawal_form.patient_id', $request->patient_id)
            ->orderBy('cps_homevisit_withdrawal_form.created_at', 'asc')
            ->get();
           
        $cps_police_referral_form = DB::table('cps_police_referral_form')
            ->join('users', 'cps_police_referral_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_police_referral_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_police_referral_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_police_referral_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_police_referral_form.created_at, '%d-%m-%Y') as date"),
                'cps_police_referral_form.status',
                'cps_police_referral_form.id',
                'users.name',
                DB::raw("'CpsPoliceReferralForm' as type"),
                DB::raw("'Cps Police Referral Form' as section_name"),
                "cps_police_referral_form.created_at"
            )
            ->where('cps_police_referral_form.patient_id', $request->patient_id)
            ->orderBy('cps_police_referral_form.created_at', 'asc')
            ->get();

        $photography_consent_form = DB::table('photography_consent_form')
            ->join('users', 'photography_consent_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(photography_consent_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(photography_consent_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(photography_consent_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(photography_consent_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'photography_consent_form.id',
                'users.name',
                DB::raw("'PhotographyConsentForm' as type"),
                DB::raw("'Photography Consent Form' as section_name"),
                "photography_consent_form.created_at"
            )
            ->where('photography_consent_form.patient_id', $request->patient_id)
            ->orderBy('photography_consent_form.created_at', 'asc')
            ->get();

        $se_consent_form = DB::table('se_consent_form')
            ->join('users', 'se_consent_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(se_consent_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(se_consent_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(se_consent_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(se_consent_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'se_consent_form.id',
                'users.name',
                DB::raw("'SEConsentForm' as type"),
                DB::raw("'SE Consent Form' as section_name"),
                "se_consent_form.created_at"
            )
            ->where('se_consent_form.patient_id', $request->patient_id)
            ->orderBy('se_consent_form.created_at', 'asc')
            ->get();

        $etp_consent_form = DB::table('etp_consent_form')
            ->join('users', 'etp_consent_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(etp_consent_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(etp_consent_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(etp_consent_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(etp_consent_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'etp_consent_form.id',
                'users.name',
                DB::raw("'ETPConsentForm' as type"),
                DB::raw("'ETP Consent Form' as section_name"),
                "etp_consent_form.created_at"
            )
            ->where('etp_consent_form.patient_id', $request->patient_id)
            ->orderBy('etp_consent_form.created_at', 'asc')
            ->get();

        $job_club_consent_form = DB::table('job_club_consent_form')
            ->join('users', 'job_club_consent_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_club_consent_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_club_consent_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_club_consent_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_club_consent_form.created_at, '%d-%m-%Y') as date"),
                'job_club_consent_form.consent_for_participation as status',
                'job_club_consent_form.id',
                'users.name',
                DB::raw("'JobClubConsentForm' as type"),
                DB::raw("'Job Club Consent Form' as section_name"),
                "job_club_consent_form.created_at"
            )
            ->where('job_club_consent_form.patient_id', $request->patient_id)
            ->orderBy('job_club_consent_form.created_at', 'asc')
            ->get();

        //         $job_club_consent_form = DB::table('job_club_consent_form')
        //         ->join('users', 'job_club_consent_form.added_by', '=', 'users.id')
        //         ->select(DB::raw("(CASE WHEN TIME(job_club_consent_form.created_at) BETWEEN '00:00:00' AND 
        //         '11:59:59' THEN DATE_FORMAT(job_club_consent_form.created_at, '%h:%i AM')
        //         ELSE DATE_FORMAT(job_club_consent_form.created_at, '%h:%i PM')
        //    END)  as time"), DB::raw("DATE_FORMAT(job_club_consent_form.created_at, '%d-%m-%Y') as date"), 
        //    'job_club_consent_form.consent_for_participation as status', 'job_club_consent_form.id', 'users.name', 
        //    DB::raw("'JobClubConsentForm' as type"), DB::raw("'Job Club Consent Form' as section_name"))
        //         ->where('job_club_consent_form.patient_id', $request->patient_id)
        //         ->orderBy('job_club_consent_form.created_at', 'asc')
        //         ->get();

        $patient_care_paln = DB::table('patient_care_paln')
            ->join('users', 'patient_care_paln.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(patient_care_paln.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(patient_care_paln.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(patient_care_paln.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(patient_care_paln.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'patient_care_paln.id',
                'users.name',
                DB::raw("'PatientCarePlanAndCaseReviewForm' as type"),
                DB::raw("'Patient Care Plan And Case Review Form' as section_name"),
                "patient_care_paln.created_at"
            )
            ->where('patient_care_paln.patient_id', $request->patient_id)
            ->orderBy('patient_care_paln.created_at', 'asc')
            ->get();

        $job_start_form = DB::table('job_start_form')
            ->join('users', 'job_start_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_start_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_start_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_start_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_start_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'job_start_form.id',
                'users.name',
                DB::raw("'JobStartReport' as type"),
                DB::raw("'Job Start Report' as section_name"),
                "job_start_form.created_at"
            )
            ->where('job_start_form.patient_id', $request->patient_id)
            ->orderBy('job_start_form.created_at', 'asc')
            ->get();

        $job_end_report = DB::table('job_end_report')
            ->join('users', 'job_end_report.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_end_report.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_end_report.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_end_report.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_end_report.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'job_end_report.id',
                'users.name',
                DB::raw("'JobEndReport' as type"),
                DB::raw("'Job End Report' as section_name"),
                "job_end_report.created_at"
            )
            ->where('job_end_report.patient_id', $request->patient_id)
            ->orderBy('job_end_report.created_at', 'asc')
            ->get();

        $job_transition_report = DB::table('job_transition_report')
            ->join('users', 'job_transition_report.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_transition_report.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_transition_report.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_transition_report.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_transition_report.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'job_transition_report.id',
                'users.name',
                DB::raw("'JobTransitionReport' as type"),
                DB::raw("'Job Transition Report' as section_name"),
                "job_transition_report.created_at"
            )
            ->where('job_transition_report.patient_id', $request->patient_id)
            ->orderBy('job_transition_report.created_at', 'asc')
            ->get();

        $laser_assesmen_form = DB::table('laser_assesmen_form')
            ->join('users', 'laser_assesmen_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(laser_assesmen_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(laser_assesmen_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(laser_assesmen_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(laser_assesmen_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'laser_assesmen_form.id',
                'users.name',
                DB::raw("'LaserAssessment' as type"),
                DB::raw("'Laser Assessment Form' as section_name"),
                "laser_assesmen_form.created_at"
            )
            ->where('laser_assesmen_form.patient_id', $request->patient_id)
            ->orderBy('laser_assesmen_form.created_at', 'asc')
            ->get();

        $triage_form = DB::table('triage_form')
            ->join('users', 'triage_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(triage_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(triage_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(triage_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(triage_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'triage_form.id',
                'users.name',
                DB::raw("'TriageForm' as type"),
                DB::raw("'Triage Form' as section_name"),
                "triage_form.created_at"
            )
            ->where('triage_form.patient_mrn_id', $request->patient_id)
            ->orderBy('triage_form.created_at', 'asc')
            ->get();

        $job_interest_checklist = DB::table('job_interest_checklist')
            ->join('users', 'job_interest_checklist.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(job_interest_checklist.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(job_interest_checklist.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(job_interest_checklist.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(job_interest_checklist.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'job_interest_checklist.id',
                'users.name',
                DB::raw("'JobInterestCheckList' as type"),
                DB::raw("'Job Interest Check List' as section_name"),
                "job_interest_checklist.created_at"
            )
            ->where('job_interest_checklist.patient_id', $request->patient_id)
            ->orderBy('job_interest_checklist.created_at', 'asc')
            ->get();

        $work_analysis_form = DB::table('work_analysis_forms')
            ->join('users', 'work_analysis_forms.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(work_analysis_forms.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(work_analysis_forms.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(work_analysis_forms.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(work_analysis_forms.created_at, '%d-%m-%Y') as date"),
                DB::raw("work_analysis_forms.status"),
                'work_analysis_forms.id',
                'users.name',
                DB::raw("'WorkAnalysisForm' as type"),
                DB::raw("'Work Analysis Form' as section_name"),
                "work_analysis_forms.created_at"
            )
            ->where('work_analysis_forms.patient_id', $request->patient_id)
            ->orderBy('work_analysis_forms.created_at', 'asc')
            ->get();

        $list_job_club = DB::table('list_job_club')
            ->join('users', 'list_job_club.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(list_job_club.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(list_job_club.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(list_job_club.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(list_job_club.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'list_job_club.id',
                'users.name',
                DB::raw("'ListofJobClub' as type"),
                DB::raw("'List of Job Club' as section_name"),
                "list_job_club.created_at"
            )
            ->where('list_job_club.patient_id', $request->patient_id)
            ->orderBy('list_job_club.created_at', 'asc')
            ->get();

        $list_of_etp = DB::table('list_of_etp')
            ->join('users', 'list_of_etp.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME( list_of_etp.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(list_of_etp.created_at, '%h:%i AM')
            ELSE DATE_FORMAT( list_of_etp.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(list_of_etp.created_at, '%d-%m-%Y') as date"),
                DB::raw("list_of_etp.status"),
                'list_of_etp.id',
                'users.name',
                DB::raw("'ListofEtp' as type"),
                DB::raw("'List of Etp' as section_name"),
                "list_of_etp.created_at"
            )
            ->where('list_of_etp.patient_id', $request->patient_id)
            ->orderBy('list_of_etp.created_at', 'asc')
            ->get();

        $list_of_job_search = DB::table('list_of_job_search')
            ->join('users', 'list_of_job_search.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(list_of_job_search.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(list_of_job_search.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(list_of_job_search.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(list_of_job_search.created_at, '%d-%m-%Y') as date"),
                DB::raw("list_of_job_search.status"),
                'list_of_job_search.id',
                'users.name',
                DB::raw("'ListofJobSearch' as type"),
                DB::raw("'List of Job Search' as section_name"),
                "list_of_job_search.created_at"
            )
            ->where('list_of_job_search.patient_id', $request->patient_id)
            ->orderBy('list_of_job_search.created_at', 'asc')
            ->get();

        $log_meeting_with_employer = DB::table('log_meeting_with_employer')
            ->join('users', 'log_meeting_with_employer.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(log_meeting_with_employer.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(log_meeting_with_employer.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(log_meeting_with_employer.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(log_meeting_with_employer.created_at, '%d-%m-%Y') as date"),
                DB::raw("log_meeting_with_employer.status"),
                'log_meeting_with_employer.id',
                'users.name',
                DB::raw("'LogMeetingWithEmployer' as type"),
                DB::raw("'Log Meeting With Employer' as section_name"),
                "log_meeting_with_employer.created_at"
            )
            ->where('log_meeting_with_employer.patient_id', $request->patient_id)
            ->orderBy('log_meeting_with_employer.created_at', 'asc')
            ->get();

        $list_previous_current_job = DB::table('list_previous_current_job')
            ->join('users', 'list_previous_current_job.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(list_previous_current_job.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(list_previous_current_job.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(list_previous_current_job.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(list_previous_current_job.created_at, '%d-%m-%Y') as date"),
                DB::raw("list_previous_current_job.status"),
                'list_previous_current_job.id',
                'users.name',
                DB::raw("'ListofPreviousCurrentJob' as type"),
                DB::raw("'List of Previous Current Job' as section_name"),
                "list_previous_current_job.created_at"
            )
            ->where('list_previous_current_job.patient_id', $request->patient_id)
            ->orderBy('list_previous_current_job.created_at', 'asc')
            ->get();

        $internal_referral_form = DB::table('internal_referral_form')
            ->join('users', 'internal_referral_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(internal_referral_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(internal_referral_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(internal_referral_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(internal_referral_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("internal_referral_form.status"),
                'internal_referral_form.id',
                'users.name',
                DB::raw("'InternalRefferalForm' as type"),
                DB::raw("'Internal Refferal Form' as section_name"),
                "internal_referral_form.created_at"
            )
            ->where('internal_referral_form.patient_mrn_id', $request->patient_id)
            ->orderBy('internal_referral_form.created_at', 'asc')
            ->get();

        $external_referral_form = DB::table('external_referral_form')
            ->join('users', 'external_referral_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(external_referral_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(external_referral_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(external_referral_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(external_referral_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("external_referral_form.status"),
                'external_referral_form.id',
                'users.name',
                DB::raw("'ExternalRefferalForm' as type"),
                DB::raw("'External Refferal Form' as section_name"),
                "external_referral_form.created_at"
            )
            ->where('external_referral_form.patient_mrn_id', $request->patient_id)
            ->orderBy('external_referral_form.created_at', 'asc')
            ->get();

        $cps_referral_form = DB::table('cps_referral_form')
            ->join('users', 'cps_referral_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(cps_referral_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(cps_referral_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(cps_referral_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(cps_referral_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'cps_referral_form.id',
                'users.name',
                DB::raw("'CpsRefferalForm' as type"),
                DB::raw("'Cps Refferal Form' as section_name"),
                "cps_referral_form.created_at"
            )
            ->where('cps_referral_form.patient_id', $request->patient_id)
            ->orderBy('cps_referral_form.created_at', 'asc')
            ->get();

        $occt_referral_form = DB::table('occt_referral_form')
            ->join('users', 'occt_referral_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(occt_referral_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(occt_referral_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(occt_referral_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(occt_referral_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("occt_referral_form.status"),
                'occt_referral_form.id',
                'users.name',
                DB::raw("'OcctRefferalForm' as type"),
                DB::raw("'Occt Refferal Form' as section_name"),
                "occt_referral_form.created_at"
            )
            ->where('occt_referral_form.patient_mrn_id', $request->patient_id)
            ->orderBy('occt_referral_form.created_at', 'asc')
            ->get();

        $psychology_referral = DB::table('psychology_referral')
            ->join('users', 'psychology_referral.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(psychology_referral.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(psychology_referral.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(psychology_referral.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(psychology_referral.created_at, '%d-%m-%Y') as date"),
                DB::raw("'1' as status"),
                'psychology_referral.id',
                'users.name',
                DB::raw("'PsychologyRefferalForm' as type"),
                DB::raw("'Psychology Refferal Form' as section_name"),
                'psychology_referral.created_at'
            )
            ->where('psychology_referral.patient_id', $request->patient_id)
            ->orderBy('psychology_referral.created_at', 'asc')
            ->get();

        $rehab_referral_and_clinical_form = DB::table('rehab_referral_and_clinical_form')
            ->join('users', 'rehab_referral_and_clinical_form.added_by', '=', 'users.id')
            ->select(
                DB::raw("(CASE WHEN TIME(rehab_referral_and_clinical_form.created_at) BETWEEN '00:00:00' AND 
            '11:59:59' THEN DATE_FORMAT(rehab_referral_and_clinical_form.created_at, '%h:%i AM')
            ELSE DATE_FORMAT(rehab_referral_and_clinical_form.created_at, '%h:%i PM')
       END)  as time"),
                DB::raw("DATE_FORMAT(rehab_referral_and_clinical_form.created_at, '%d-%m-%Y') as date"),
                DB::raw("rehab_referral_and_clinical_form.status"),
                'rehab_referral_and_clinical_form.id',
                'users.name',
                DB::raw("'RehabRefferalAndClinicalForm' as type"),
                DB::raw("'Rehab Refferal And Clinical Form' as section_name"),
                "rehab_referral_and_clinical_form.created_at"
            )
            ->where('rehab_referral_and_clinical_form.patient_mrn_id', $request->patient_id)
            ->orderBy('rehab_referral_and_clinical_form.created_at', 'asc')
            ->get();

        foreach ($Psychiatry_Clerking_Note as $key => $val) {
            $list[] = $val;
        }
        foreach ($Counsellor_Clerking_Note as $key => $val) {
            $list[] = $val;
        }
        foreach ($patient_index_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($psychiatric_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($se_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($counselling_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($etp_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_club_progress_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($consultation_discharge_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($rehab_discharge_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_discharge_note as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_homevisit_consent_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_homevisit_withdrawal_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_police_referral_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($photography_consent_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($se_consent_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($etp_consent_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_club_consent_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($patient_care_paln as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_start_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_end_report as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_transition_report as $key => $val) {
            $list[] = $val;
        }
        foreach ($laser_assesmen_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($triage_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($job_interest_checklist as $key => $val) {
            $list[] = $val;
        }
        foreach ($work_analysis_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($list_job_club as $key => $val) {
            $list[] = $val;
        }
        foreach ($list_of_etp as $key => $val) {
            $list[] = $val;
        }
        foreach ($list_of_job_search as $key => $val) {
            $list[] = $val;
        }
        foreach ($log_meeting_with_employer as $key => $val) {
            $list[] = $val;
        }
        foreach ($list_previous_current_job as $key => $val) {
            $list[] = $val;
        }
        foreach ($internal_referral_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($external_referral_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($cps_referral_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($occt_referral_form as $key => $val) {
            $list[] = $val;
        }
        foreach ($psychology_referral as $key => $val) {
            $list[] = $val;
        }
        foreach ($rehab_referral_and_clinical_form as $key => $val) {
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
        if ($request->type == "PsychiatryClerkingNote") {
            $list = PsychiatryClerkingNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CounsellorClerkingNote") {
            $list = PatientCounsellorClerkingNotes::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "PatientIndexForm") {
            $list = PatientIndexForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        // if ($request->type == "PatientIndexForm") {
        //     $list = PatientIndexForm::select('*')
        //         ->where('id', '=', $request->id)
        //         // ->where('status', '1')
        //         ->get();
        // }
        if ($request->type == "PsychiatricProgressNote") {
            $list = PsychiatricProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CPSProgressNote") {
            $list = CpsProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "SEProgressNote") {
            $list = SeProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CounsellingProgressNote") {
            $list = CounsellingProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "EtpProgressNote") {
            $list = EtpProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobClubProgressNote") {
            $list = JobClubProgressNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ConsultationDischargeNote") {
            $list = ConsultationDischargeNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "RehabDischargeNote") {
            $list = RehabDischargeNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CpsDischargeNote") {
            $list = CpsDischargeNote::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CpsHomeVisitConsentForm") {
            $list = CpsHomevisitConsentForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CpsHomeVisitWithdrawalForm") {
            $list = CpsHomevisitWithdrawalForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CpsPoliceReferralForm") {
            $list = CpsPoliceReferralForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "PhotographyConsentForm") {
            $list = PhotographyConsentForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "SEConsentForm") {
            $list = SEConsentForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ETPConsentForm") {
            $list = EtpConsentForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobClubConsentForm") {
            $list = JobClubConsentForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "PatientCarePlanAndCaseReviewForm") {
            $list = PatientCarePaln::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobStartReport") {
            $list = JobStartForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobEndReport") {
            $list = JobEndReport::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobTransitionReport") {
            $list = JobTransitionReport::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "LaserAssessment") {
            $list = LASERAssesmenForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "TriageForm") {
            $list = TriageForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "JobInterestCheckList") {

            // $list = DB::table('job_interest_checklist')
            // ->join('job_interest_list', 'job_interest_checklist.id', '=', 'job_interest_list.job_interest_checklist_id')
            // ->select('job_interest_checklist.*', 'job_interest_list.*')
            // ->where('job_interest_checklist.status', '=', '1')
            // ->where('job_interest_checklist.id', '=', $request->id)
            // ->get();
            // dd($list);
        // return response()->json(["message" => "CountryState List", 'list' => $users, "code" => 200]);

            $list1 = JobInterestChecklist::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
                $result = (array) json_decode($list1,true);
                $list=[];
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        if($val['id']){
                            $ab=JobInterestList:: select('*')->where('job_interest_checklist_id',$val['id'])
                            ->get();
                         }
                         $list[$key]['patient_id'] = $val['patient_id'] ??  'NA';
                         $list[$key]['interest_to_work'] = $val['interest_to_work'] ??  'NA';
                         $list[$key]['agree_if_mentari_find_job_for_you'] = $val['agree_if_mentari_find_job_for_you'] ??  'NA';
                         $list[$key]['clerk_job_interester'] = $val['clerk_job_interester'] ??  'NA';
                         $list[$key]['clerk_job_notes'] = $val['clerk_job_notes'] ??  'NA';
                         $list[$key]['id'] = $val['id'] ??  'NA';
                         $list[$key]['factory_worker_job_interested'] = $val['factory_worker_job_interested'] ??  'NA';
                         $list[$key]['factory_worker_notes'] = $val['factory_worker_notes'] ??  'NA';
                         $list[$key]['cleaner_job_interested'] = $val['cleaner_job_interested'] ??  'NA';
                         $list[$key]['cleaner_job_notes'] = $val['cleaner_job_notes'] ??  'NA';
                         $list[$key]['security_guard_job_interested'] = $val['security_guard_job_interested'] ?? 'NA';
                         $list[$key]['security_guard_notes'] = $val['security_guard_notes'] ??  'NA';
                         $list[$key]['laundry_worker_job_interested'] = $val['laundry_worker_job_interested'] ??  'NA';
                         $list[$key]['laundry_worker_notes'] = $val['laundry_worker_notes'] ??  'NA';
                         $list[$key]['car_wash_worker_job'] = $val['car_wash_worker_job'] ??  'NA';	
                         $list[$key]['car_wash_worker_notes'] = $val['car_wash_worker_notes'] ??  'NA';
                         $list[$key]['kitchen_helper_job'] = $val['kitchen_helper_job'] ??  'NA';
                         $list[$key]['kitchen_helper_notes'] = $val['kitchen_helper_notes'] ??  'NA';
                         $list[$key]['waiter_job_interested'] = $val['waiter_job_interested'] ??  'NA';
                         $list[$key]['waiter_job_notes'] = $val['waiter_job_notes'] ??  'NA';
                         $list[$key]['chef_job_interested'] = $val['chef_job_interested'] ??  'NA';
                         $list[$key]['chef_job_notes'] = $val['chef_job_notes'] ??  'NA';
                         $list[$key]['others_job_specify'] = $val['others_job_specify'] ??  'NA';
                         $list[$key]['others_job_notes'] = $val['others_job_notes'] ??  'NA';
                         $list[$key]['type_of_job'] = $val['type_of_job'] ??  'NA';
                         $list[$key]['duration'] = $val['duration'] ??  'NA';
                         $list[$key]['termination_reason'] = $val['termination_reason'] ??  'NA';
                         $list[$key]['note'] = $val['note'] ??  'NA';
                         $list[$key]['planning'] = $val['planning'] ??  'NA';
                         $list[$key]['patient_consent_interested'] = $val['patient_consent_interested'] ??  'NA';
                         $list[$key]['location_services'] = $val['location_services'] ??  'NA';
                         $list[$key]['type_diagnosis_id'] = $val['type_diagnosis_id'] ??  'NA';
                         $list[$key]['category_services'] = $val['category_services'] ??  'NA';
                         $list[$key]['services_id'] = $val['services_id'] ??  'NA';
                         $list[$key]['code_id'] = $val['code_id'] ??  'NA';
                         $list[$key]['sub_code_id'] = $val['sub_code_id'] ??  'NA';
                         $list[$key]['complexity_services'] = $val['complexity_services'] ??  'NA';
                         $list[$key]['outcome'] = $val['outcome'] ??  'NA';
                         $list[$key]['medication_des'] = $val['medication_des'] ??  'NA';
                         $list[$key]['status'] = $val['status'] ??  'NA';

                         $list[$key]['jobs'] = $ab ??  'NA';
                    }

                }
        }
        if ($request->type == "WorkAnalysisForm") {
            $list1 = WorkAnalysisForm::select('*')
                ->where('id', '=', $request->id)
                ->get();
                $result = (array) json_decode($list1,true);
                $list=[];
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        if($val['id']){
                            $ab=WorkAnalysisJobSpecification:: select('*')->where('work_analysis_form_id',$val['id'])->where('patient_id',$val['patient_id'])
                            ->get();
                            $jobdes=JobDescription:: select('*')->where('work_analysis_form_id',$val['id'])->where('patient_id',$val['patient_id'])
                            ->get();
                         }
                         
                         $list[$key]['patient_id'] = $val['patient_id'] ??  'NA';
                         $list[$key]['interest_to_work'] = $val['interest_to_work'] ??  'NA';
                         $list[$key]['company_name'] = $val['company_name'] ??  'NA';
                         $list[$key]['company_address1'] = $val['company_address1'] ??  'NA';
                         $list[$key]['company_address2'] = $val['company_address2'] ??  'NA';
                         $list[$key]['id'] = $val['id'] ??  'NA';
                         $list[$key]['company_address3'] = $val['company_address3'] ??  'NA';
                         $list[$key]['state_id'] = $val['state_id'] ??  'NA';
                         $list[$key]['city_id'] = $val['city_id'] ??  'NA';
                         $list[$key]['postcode_id'] = $val['postcode_id'] ??  'NA';
                         $list[$key]['supervisor_name'] = $val['supervisor_name'] ?? 'NA';
                         $list[$key]['email'] = $val['email'] ??  'NA';
                         $list[$key]['position'] = $val['position'] ??  'NA';
                         $list[$key]['job_position'] = $val['job_position'] ??  'NA';
                         $list[$key]['client_name'] = $val['client_name'] ??  'NA';	
                         $list[$key]['current_wage'] = $val['current_wage'] ??  'NA';
                         $list[$key]['wage_specify'] = $val['wage_specify'] ??  'NA';
                         $list[$key]['wage_change_occur'] = $val['wage_change_occur'] ??  'NA';
                         $list[$key]['change_in_rate'] = $val['change_in_rate'] ??  'NA';
                         $list[$key]['from'] = $val['from'] ??  'NA';
                         $list[$key]['to'] = $val['to'] ??  'NA';
                         $list[$key]['on_date'] = $val['on_date'] ??  'NA';
                         $list[$key]['works_hour_week'] = $val['works_hour_week'] ??  'NA';
                         $list[$key]['work_schedule'] = $val['work_schedule'] ??  'NA';
                         $list[$key]['no_of_current_employee'] = $val['no_of_current_employee'] ??  'NA';
                         $list[$key]['no_of_other_employee'] = $val['no_of_other_employee'] ??  'NA';
                         $list[$key]['during_same_shift'] = $val['during_same_shift'] ??  'NA';
                         $list[$key]['education_level'] = $val['education_level'] ??  'NA';
                         $list[$key]['grade'] = $val['grade'] ??  'NA';
                         $list[$key]['job_experience_year'] = $val['job_experience_year'] ??  'NA';

                         $list[$key]['job_experience_months'] = $val['job_experience_months'] ??  'NA';
                         $list[$key]['others'] = $val['others'] ??  'NA';
                        
                         $list[$key]['location_services'] = $val['location_services'] ??  'NA';
                         $list[$key]['type_diagnosis_id'] = $val['type_diagnosis_id'] ??  'NA';
                         $list[$key]['category_services'] = $val['category_services'] ??  'NA';
                         $list[$key]['services_id'] = $val['services_id'] ??  'NA';
                         $list[$key]['code_id'] = $val['code_id'] ??  'NA';
                         $list[$key]['sub_code_id'] = $val['sub_code_id'] ??  'NA';
                         $list[$key]['complexity_services'] = $val['complexity_services'] ??  'NA';
                         $list[$key]['outcome'] = $val['outcome'] ??  'NA';
                         $list[$key]['medication_des'] = $val['medication_des'] ??  'NA';
                         $list[$key]['status'] = $val['status'] ??  'NA';

                         $list[$key]['jobs'] = $ab ??  'NA';
                         $list[$key]['jobs_des'] = $jobdes ??  'NA';
                    }

                }
        }
        if ($request->type == "ListofJobClub") {
            $list = ListJobClub::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ListofEtp") {
            $list = ListOfETP::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ListofJobSearch") {
            $list1 = ListOfJobSearch::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();

                $result = (array) json_decode($list1,true);
                $list=[];
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        if($val['id']){
                            $ab=JobSearchList:: select('*')->where('list_of_job_search_id',$val['id'])->where('patient_id',$val['patient_id'])
                            ->get();
                         }
                         $list[$key]['patient_id'] = $val['patient_id'] ??  'NA';
                         $list[$key]['location_services'] = $val['location_services'] ??  'NA';
                         $list[$key]['type_diagnosis_id'] = $val['type_diagnosis_id'] ??  'NA';
                         $list[$key]['category_services'] = $val['category_services'] ??  'NA';
                         $list[$key]['services_id'] = $val['services_id'] ??  'NA';
                         $list[$key]['code_id'] = $val['code_id'] ??  'NA';
                         $list[$key]['sub_code_id'] = $val['sub_code_id'] ??  'NA';
                         $list[$key]['complexity_services'] = $val['complexity_services'] ??  'NA';
                         $list[$key]['outcome'] = $val['outcome'] ??  'NA';
                         $list[$key]['medication_des'] = $val['medication_des'] ??  'NA';
                         $list[$key]['status'] = $val['status'] ??  'NA';

                         $list[$key]['jobs'] = $ab ??  'NA';
                    }

                }
        }
        if ($request->type == "LogMeetingWithEmployer") {
            $list = LogMeetingWithEmployer::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ListofPreviousCurrentJob") {
            $list1 = ListPreviousCurrentJob::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
                $result = (array) json_decode($list1,true);
                $list=[];
                if (count($result) > 0) {
                    foreach ($result as $key => $val) {
                        if($val['id']){
                            $ab=PreviousOrCurrentJobRecord:: select('*')->where('list_previous_current_job_id',$val['id'])->where('patient_id',$val['patient_id'])
                            ->get();
                         }
                         $list[$key]['patient_id'] = $val['patient_id'] ??  'NA';
                         $list[$key]['location_services'] = $val['location_services'] ??  'NA';
                         $list[$key]['type_diagnosis_id'] = $val['type_diagnosis_id'] ??  'NA';
                         $list[$key]['category_services'] = $val['category_services'] ??  'NA';
                         $list[$key]['services_id'] = $val['services_id'] ??  'NA';
                         $list[$key]['code_id'] = $val['code_id'] ??  'NA';
                         $list[$key]['sub_code_id'] = $val['sub_code_id'] ??  'NA';
                         $list[$key]['complexity_services'] = $val['complexity_services'] ??  'NA';
                         $list[$key]['outcome'] = $val['outcome'] ??  'NA';
                         $list[$key]['medication_des'] = $val['medication_des'] ??  'NA';
                         $list[$key]['status'] = $val['status'] ??  'NA';

                         $list[$key]['jobs'] = $ab ??  'NA';
                    }

                }
        }
        if ($request->type == "InternalRefferalForm") {
            $list = InternalReferralForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "ExternalRefferalForm") {
            $list = ExternalReferralForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "CpsRefferalForm") {
            $list = CPSReferralForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "OcctRefferalForm") {
            $list = Occt_Referral_Form::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "PsychologyRefferalForm") {
            $list = PsychologyReferral::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        if ($request->type == "RehabRefferalAndClinicalForm") {
            $list = RehabReferralAndClinicalForm::select('*')
                ->where('id', '=', $request->id)
                // ->where('status', '1')
                ->get();
        }
        return response()->json(["message" => "List", 'Data' => $list, "code" => 200]);
    }
}
