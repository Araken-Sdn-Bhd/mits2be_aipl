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

        $additional_diagnosis=str_replace('"',"",$request->additional_diagnosis);
        $additional_subcode=str_replace('"',"",$request->additional_subcode);
        $sub_code_id=str_replace('"',"",$request->sub_code_id);

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
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($seprogressnote);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SeProgress' => $seprogressnote, "code" => 200]);
                    }
                    return response()->json(["message" => "SE Progress Form Successfully1", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = SeProgressNote::where(
                            ['id' => $request->id]
                        )->update($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully2", "code" => 200]);
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
                        'restart' => $request->restart_program,
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
                    return response()->json(["message" => "Se Progress Note Successfully3", "code" => 200]);
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
                        'restart' => $request->restart_program,
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
                    return response()->json(["message" => "SE Progress Form Successfully4", "code" => 200]);
                } else if ($request->service_category == 'clinical-work') {
                    $validator = Validator::make($request->all(), [
                        'code_id' => 'required|integer',
                        'sub_code_id' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    }

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully5", "code" => 200]);
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
                        'restart' => $request->restart_program,
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
                    return response()->json(["message" => "Se Progress Note Successfully6", "code" => 200]);
                }
            }
        } else if ($request->status == 0) {
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
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                    ];

                    if($request->id) {
                        SeProgressNote::where(
                                    ['id' => $request->id]
                                )->update($seprogressnote);
                                return response()->json(["message" => "SE progress note updated1", "code" => 200]);
                    } else {
                        SeProgressNote::create($seprogressnote);
                        return response()->json(["message" => "SE progress note created2", "code" => 200]);
                    }
                } else if ($request->service_category == 'clinical-work') {

                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    if($request->id) {
                        SeProgressNote::where(
                                    ['id' => $request->id]
                                )->update($SeProgress);
                                return response()->json(["message" => "SE progress note updated3", "code" => 200]);
                    } else {
                        SeProgressNote::create($SeProgress);
                        return response()->json(["message" => "SE progress note created4", "code" => 200]);
                }
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
                        'restart' => $request->restart_program,
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
                    return response()->json(["message" => "Se Progress Note Successfully13", "code" => 200]);
                }
                else {
                    $SeProgress = [
                        'services_id' =>  $request->services_id,
                        'code_id' =>  $request->code_id,
                        'sub_code_id' =>  $sub_code_id,
                        'added_by' =>  $request->added_by,
                        'patient_mrn_id' =>  $request->patient_mrn_id,
                        'patient_id' =>  $request->patient_id,
                        'name' =>  $request->name,
                        'mrn' =>  $request->mrn,
                        'date' =>  $request->date,
                        'time' =>  $request->time,
                        'staff_name' =>  $request->staff_name,
                        'activity_type' =>  $request->activity_type,
                        'restart' => $request->restart_program,
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
                        'additional_diagnosis' => $additional_diagnosis,
                        'additional_code_id' => $request->additional_code_id,
                        'additional_subcode' => $additional_subcode,
                    ];

                    try {
                        $HOD = SeProgressNote::create($SeProgress);
                    } catch (Exception $e) {
                        return response()->json(["message" => $e->getMessage(), 'SEProgress' => $SeProgress, "code" => 200]);
                    }
                    return response()->json(["message" => "Se Progress Note Successfully14", "code" => 200]);
                }
        }
    }

    public function storeMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ( $request->add_diagnosis_type1 != null && $request->add_diagnosis_type1 != ''){
            $additional_diagnosis = $request->add_diagnosis_type1;
            if ($request->add_diagnosis_type2 != null && $request->add_diagnosis_type2 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type2;
                if ($request->add_diagnosis_type3 != null && $request->add_diagnosis_type3 != ''){
                    $additional_diagnosis .= ','.$request->add_diagnosis_type3;
                    if ($request->add_diagnosis_type4 != null && $request->add_diagnosis_type4 != ''){
                        $additional_diagnosis .= ','.$request->add_diagnosis_type4;
                        if ($request->add_diagnosis_type5 != null && $request->add_diagnosis_type5 != ''){
                            $additional_diagnosis .= ','.$request->add_diagnosis_type5;
                        }
                    }
                }
            }
        }

        if ( $request->additional_sub_code_id != null && $request->additional_sub_code_id != ''){
            $additional_subcode = $request->additional_sub_code_id;
            if ( $request->additional_sub_code_id1 != null && $request->additional_sub_code_id1 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id1;
                if ( $request->additional_sub_code_id2 != null && $request->additional_sub_code_id2 != ''){
                    $additional_subcode .= ','.$request->additional_sub_code_id2;
                    if ( $request->additional_sub_code_id3 != null && $request->additional_sub_code_id3 != ''){
                        $additional_subcode .= ','.$request->additional_sub_code_id3;
                        if ( $request->additional_sub_code_id4 != null && $request->additional_sub_code_id4 != ''){
                            $additional_subcode .= ','.$request->additional_sub_code_id4;
                            if ( $request->additional_sub_code_id5 != null && $request->additional_sub_code_id5 != ''){
                                $additional_subcode .= ','.$request->additional_sub_code_id5;
                            }
                        }
                    }
                }
            }
        }

        if ( $request->sub_code_id != null && $request->sub_code_id != ''){
            $sub_code_id = $request->sub_code_id;
            if ( $request->sub_code_id1 != null && $request->sub_code_id1 != ''){
                $sub_code_id .= ','.$request->sub_code_id1;
                if ( $request->sub_code_id2 != null && $request->sub_code_id2 != ''){
                    $sub_code_id .= ','.$request->additional_sub_code_id2;
                    if ( $request->sub_code_id3 != null && $request->sub_code_id3 != ''){
                        $sub_code_id .= ','.$request->sub_code_id3;
                        if ( $request->sub_code_id4 != null && $request->sub_code_id4 != ''){
                            $sub_code_id .= ','.$request->additional_sub_code_id4;
                            if ( $request->sub_code_id5 != null && $request->sub_code_id5 != ''){
                                $sub_code_id .= ','.$request->sub_code_id5;
                            }
                        }
                    }
                }
            }
        }

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
                    'restart' => $request->restart_program,
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
                    'additional_diagnosis' => $additional_diagnosis,
                ];
                if($request->id) {
                    SeProgressNote::where(
                                ['id' => $request->id]
                            )->update($seprogressnote);
                            return response()->json(["message" => "SE progress note updated1", "code" => 200]);
                } else {
                    SeProgressNote::create($seprogressnote);
                    return response()->json(["message" => "SE progress note created2", "code" => 200]);
                }
            } else if ($request->service_category == 'clinical-work') {

                $SeProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'restart' => $request->restart_program,
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
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_code_id' => $request->additional_code_id,
                    'additional_subcode' => $additional_subcode,
                ];

                if($request->id) {
                    SeProgressNote::where(
                                ['id' => $request->id]
                            )->update($SeProgress);
                            return response()->json(["message" => "SE progress note updated3", "code" => 200]);
                } else {
                    SeProgressNote::create($SeProgress);
                    return response()->json(["message" => "SE progress note created4", "code" => 200]);
                }
            }
            else {
                $SeProgress = [
                    'services_id' =>  $request->services_id,
                    'code_id' =>  $request->code_id,
                    'sub_code_id' =>  $sub_code_id,
                    'added_by' =>  $request->added_by,
                    'patient_mrn_id' =>  $request->patient_mrn_id,
                    'patient_id' =>  $request->patient_id,
                    'name' =>  $request->name,
                    'mrn' =>  $request->mrn,
                    'date' =>  $request->date,
                    'time' =>  $request->time,
                    'staff_name' =>  $request->staff_name,
                    'activity_type' =>  $request->activity_type,
                    'restart' => $request->restart_program,
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
                    'additional_diagnosis' => $additional_diagnosis,
                    'additional_code_id' => $request->additional_code_id,
                    'additional_subcode' => $additional_subcode,
                ];

                try {
                    $HOD = SeProgressNote::create($SeProgress);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'SEProgress' => $SeProgress, "code" => 200]);
                }
                return response()->json(["message" => "Se Progress Note Successfully14", "code" => 200]);
            }
        }
}
