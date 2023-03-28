<?php

namespace App\Http\Controllers;

use App\Models\PatientAppointmentDetails;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SeProgressNote;
use App\Models\PatientRegistration;
use App\Models\StaffManagement;
use App\Models\Notifications;
use App\Models\AppointmentRequest;
use App\Models\HospitalBranchManagement;
use DateTime;
use App\Models\TransactionLog;
use DateTimeZone;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentRequestMail as AppointmentRequestMail;

class SeProgressNoteController extends Controller
{

    public function GetActivityList()
    {
        $list = SeProgressNote::select('id', 'activity_type')
            ->get();
        return response()->json(["message" => "Se Progress Activity List", 'list' => $list, "code" => 200]);
    }

    public function GetSENamelist()
    {
        $list = SeProgressNote::select('id', 'staff_name', 'employment_status')
            ->get();
        return response()->json(["message" => "Se Progress Note Stafflist", 'list' => $list, "code" => 200]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->status == 1) {
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $seprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($seprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                }
            } else {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $seprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($seprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "1",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                }
            }
        } else if ($request->status == 0) {
            if ($request->id) {
                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {
                    $validator = Validator::make($request->all(), [
                        'services_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $seprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($seprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required|integer'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else {

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SEProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                }
            } else {
                if ($request->appId == null || $request->appId == '') {
                    $checkTodayAppointment = PatientAppointmentDetails::where('patient_mrn_id', $request->patient_id)->whereDate("created_at",'=',date('Y-m-d'))->first();
                    if ($checkTodayAppointment) {
                        $request->appId = $checkTodayAppointment->id;
                    } else {
                        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
                        $duration_set = 30;
                        $booking_date_set = $date->format('Y-m-d H:i:s');
                        $booking_time_set = $date->format('Y-m-d H:i:s');
                        $assign_team_set = 3;
                        $appointment_type = 3;
                        $patient_category = 150;
                        $type_visit = 153;

                        $PatientDetails = PatientRegistration::where('id', $request->patient_id)->where('patient_mrn', 'like', '%'.$request->patient_mrn_id.'%')->first();
                        if ($PatientDetails->nric_no != null || $PatientDetails->nric_no != '') {
                            $nric_or_passportno = $PatientDetails->nric_no;
                        } else if ($PatientDetails->passport_no != null && $PatientDetails->nric_no == null && $PatientDetails->nric_no != '') {
                            $nric_or_passportno = $PatientDetails->passport_no;
                        }
                        $userDetails = StaffManagement::where('id', $request->added_by)->first();
                        $nric_or_passportno = $PatientDetails->nric_no;
                        $getmnr_id = PatientRegistration::select('id')
                            ->where('nric_no', $nric_or_passportno)
                            ->orWhere('passport_no', $nric_or_passportno)
                            ->pluck('id');

                        if (count($getmnr_id) == 0) {
                            return response()->json(["message" => "This user is not registered", "code" => 401]);
                        } else {
                            $booking_date = $booking_date_set;
                            $booking_time = $booking_time_set;
                            $assign_team = $assign_team_set;
                            $branch_id = $userDetails->branch_id;
                            $duration = "+" . $duration_set . " minutes";
                            $endTime = date("H:i", strtotime($duration, strtotime($booking_time)));

                            $chkPoint =  PatientRegistration::join('patient_appointment_details', 'patient_appointment_details.patient_mrn_id', '=', 'patient_registration.id')
                                ->where('patient_registration.branch_id', '=', $branch_id)
                                ->where('patient_appointment_details.booking_date', '=', $booking_date)
                                ->whereBetween('patient_appointment_details.booking_time', [$booking_time, $endTime])
                                ->where('patient_appointment_details.status', '=', '1')
                                ->where('patient_appointment_details.assign_team', '=', $assign_team)
                                ->get();

                            if ($chkPoint->count() == 0) {
                                $service = [
                                    'added_by' => $request->added_by,
                                    'nric_or_passportno' => $nric_or_passportno,
                                    'booking_date' => $booking_date_set,
                                    'booking_time' => $booking_time_set,
                                    'patient_mrn_id' => $getmnr_id[0],
                                    'duration' => $duration_set,
                                    'appointment_type' => $appointment_type,
                                    'type_visit' => $type_visit,
                                    'patient_category' => $patient_category,
                                    'assign_team' => $assign_team_set
                                ];
                                $patient = PatientAppointmentDetails::create($service);
                                $request->appId = $patient->id;
                                // $notifi = [
                                //     'added_by' => $request->added_by,
                                //     'branch_id' => $userDetails->branch_id,
                                //     'role' => 'Admin/Clerk',
                                //     'patient_mrn' =>   $getmnr_id[0],
                                //     'url_route' => "/Modules/Patient/list-of-appointment",
                                //     'created_at' => $date->format('Y-m-d H:i:s'),
                                //     'message' =>  'Request for appointment(s)',
                                // ];
                                // $HOD = Notifications::insert($notifi);

                                // EMAIL
                                $app_request = AppointmentRequest::where('nric_or_passportno', $nric_or_passportno)
                                    ->select('name', 'email')->get();

                                $hospital_branch = HospitalBranchManagement::where('id', $userDetails->branch_id)
                                    ->select('hospital_branch_name')->get();
                                if ($app_request->count() != 0) {
                                    $bookingDate = date('d M Y', strtotime($booking_date_set));
                                    $bookingTime = date("h:i A", strtotime($booking_time_set));
                                    $data = array(
                                        'name' => $app_request[0]['name'],
                                        'branch' => ucwords(strtolower($hospital_branch[0]['hospital_branch_name'])),
                                        'email' => $app_request[0]['email'],
                                        'date' => $bookingDate,
                                        'time' => $bookingTime,
                                    );

                                    try {
                                        Mail::to($data['email'])->send(new AppointmentRequestMail($data));
                                    } catch (\Exception $err) {
                                        var_dump($err);

                                        return response([
                                            'message' => 'Error In Email Configuration: ' . $err,
                                            'code' => 500
                                        ]);
                                    }
                                };
                            } else {
                                return response()->json(["message" => "Another Appointment already booked for this date and time!", "code" => 400]);
                            }
                        }
                    }
                }

                if ($request->service_category == 'assisstance' || $request->service_category == 'external') {

                    $seprogressnote = [
                        'services_id' =>  $request->services_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($seprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "SE Progress Form Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else if ($request->service_category == 'clinical') {

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'progress_note' =>  $request->progress_note,
                        'employment_status' =>  $request->employment_status,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'service_category' =>  $request->service_category,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                } else {

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $request->sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart_program' => $request->restart_program,
                        'employment_status' =>  $request->employment_status,
                        'progress_note' =>  $request->progress_note,
                        'management_plan' =>  $request->management_plan,
                        'location_service' =>  $request->location_service,
                        'diagnosis_type' =>  $request->diagnosis_type,
                        'complexity_service' =>  $request->complexity_service,
                        'outcome' =>  $request->outcome,
                        'medication' =>  $request->medication,
                        'status' => "0",
                        'appointment_details_id' => $request->appId,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SEProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully", "code" => 200]);
                }
            }
        }
    }
}
