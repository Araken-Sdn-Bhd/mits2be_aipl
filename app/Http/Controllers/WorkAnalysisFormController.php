<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkAnalysisForm;
use App\Models\JobDescription;
use App\Models\JobSpecification;
use App\Models\WorkAnalysisJobSpecification;

use App\Models\PatientAppointmentDetails;
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
use App\Models\UserDiagnosis;
use CreateWorkAnalysisJobSpecificationTable;
use Validator;
use DB;
use Exception;

class WorkAnalysisFormController extends Controller
{
    public function store(Request $request)
    {
        $additional_diagnosis = str_replace('"', "", $request->additional_diagnosis);
        $additional_subcode = str_replace('"', "", $request->additional_sub_code_id);
        $sub_code_id = str_replace('"', "", $request->sub_code_id);
        if ($request->status == '0') {

            if ($request->appId == null || $request->appId == '') {
                $checkTodayAppointment = PatientAppointmentDetails::where('patient_mrn_id', $request->patient_id)->whereDate("created_at", '=', date('Y-m-d'))->first();
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

                    $PatientDetails = PatientRegistration::where('id', $request->patient_id)->where('patient_mrn', 'like', '%' . $request->patient_mrn_id . '%')->first();
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
            $WorkAnalysisForm = [
                'appointment_details_id' => $request->appId,
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'company_name' => $request->company_name,
                'company_address1' => $request->company_address1,
                'company_address2' => $request->company_address2,
                'company_address3' => $request->company_address3,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'postcode_id' => $request->postcode_id,

                'supervisor_name' => $request->supervisor_name,
                'email' => $request->email,
                'position' => $request->position,
                'job_position' => $request->job_position,
                'client_name' => $request->client_name,
                'current_wage' => $request->current_wage,
                'wage_specify' => $request->wage_specify,
                'wage_change_occur' => $request->wage_change_occur,
                'education_level' => $request->education_level,
                'grade' => $request->grade,
                'job_experience_year' => $request->job_experience_year,
                'job_experience_months' => $request->job_experience_months,
                'others' => $request->others,

                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
                'additional_diagnosis' => $additional_diagnosis,
                'location_services' => $request->location_services,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "0"
            ];

            $validateWorkAnalysisForm = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateWorkAnalysisForm['services_id'] = 'required';
                $WorkAnalysisForm['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateWorkAnalysisForm['code_id'] = 'required';
                $WorkAnalysisForm['code_id'] =  $request->code_id;
                $validateWorkAnalysisForm['sub_code_id'] = 'required';
                $WorkAnalysisForm['sub_code_id'] =  $sub_code_id;
            } else if ($request->category_services == 'clinical') {
                $validateWorkAnalysisForm['code_id'] = 'required';
                $WorkAnalysisForm['code_id'] =  $request->code_id;
                $validateWorkAnalysisForm['sub_code_id'] = 'required';
                $WorkAnalysisForm['sub_code_id'] =  $sub_code_id;
            }

            if ($request->wage_change_occur == 'yes') {
                $validateWorkAnalysisForm['change_in_rate'] = 'required';
                $WorkAnalysisForm['change_in_rate'] =  $request->change_in_rate;
                $validateWorkAnalysisForm['from'] = 'required';
                $WorkAnalysisForm['from'] =  $request->from;
                $validateWorkAnalysisForm['to'] = 'required';
                $WorkAnalysisForm['to'] =  $request->to;
                $validateWorkAnalysisForm['on_date'] = 'required';
                $WorkAnalysisForm['on_date'] =  $request->on_date;
                $validateWorkAnalysisForm['works_hour_week'] = 'required';
                $WorkAnalysisForm['works_hour_week'] =  $request->works_hour_week;
                $validateWorkAnalysisForm['work_schedule'] = 'required';
                $WorkAnalysisForm['work_schedule'] =  $request->work_schedule;
                $validateWorkAnalysisForm['no_of_current_employee'] = 'required';
                $WorkAnalysisForm['no_of_current_employee'] =  $request->no_of_current_employee;
                $validateWorkAnalysisForm['no_of_other_employee'] = 'required';
                $WorkAnalysisForm['no_of_other_employee'] =  $request->no_of_other_employee;
                $validateWorkAnalysisForm['during_same_shift'] = 'required';
                $WorkAnalysisForm['during_same_shift'] =  $request->during_same_shift;
            }

            $validator = Validator::make($request->all(), $validateWorkAnalysisForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            // dd($request);
            WorkAnalysisForm::where(['id' => $request->id])->update($WorkAnalysisForm);
            $WorkAnalysisFormid = ($request->id);

            if (!empty($request->jobs)) {
                foreach ($request->jobs as $key) {
                    if ($key['task_description']) {
                        $data = array('task_description' => $key['task_description'], 'patient_id' => $request->patient_id, 'objectives' => $key['objectives'], 'procedure' => $key['procedure'], 'rate_of_time' => $key['rate_of_time'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                        JobDescription::insert($data);
                    }
                }
            }
            if (!empty($request->job_specification)) {
                foreach ($request->job_specification as $key) {
                    if ($key['questions']) {
                        $data = array('question_name' => $key['questions'], 'patient_id' => $request->patient_id, 'answer' => $key['answer'], 'comment' => $key['comments'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                        WorkAnalysisJobSpecification::insert($data);
                    }
                }
            }

            return response()->json(["message" => "Work Analysis Form Updated Successfully!", "code" => 200]);
        } else if ($request->status == '1') {
            $validator = Validator::make($request->all(), [
                'added_by' => 'required|integer',
                'patient_id' => 'required|integer',
                'company_name' => 'required|string',
                'company_address1' => 'required|string',
                'company_address2' => '',
                'company_address3' => '',
                'state_id' => 'required',
                'city_id' => 'required',
                'postcode_id' => 'required',

                'supervisor_name' => 'required|string',
                'email' => 'required|string',
                'position' => 'required|string',
                'job_position' => 'required|string',
                'client_name' => 'required|string',
                'current_wage' => 'required|string',
                'wage_specify' => '',
                'wage_change_occur' => '',
                'change_in_rate' => '',
                'from' => '',
                'to' => '',
                'on_date' => '',
                'works_hour_week' => '',
                'work_schedule' => '',
                'no_of_current_employee' => '',
                'no_of_other_employee' => '',
                'during_same_shift' => '',

                'education_level' => 'required|string',
                'grade' => 'required|string',
                'job_experience_year' => 'required|string',
                'job_experience_months' => 'required|string',
                'others' => 'required|string',

                'location_services' => 'required',
                'services_id' => '',
                'code_id' => '',
                'sub_code_id' => '',
                'type_diagnosis_id' => 'required',
                'category_services' => 'required',
                'complexity_services' => '',
                'outcome' => '',
                'medication_des' => '',
                'jobs' => '',
                'job_specification' => '',
                'appointment_details_id' => '',
            ]);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $WorkAnalysisForm = [
                'appointment_details_id' => $request->appId,
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'company_name' => $request->company_name,
                'company_address1' => $request->company_address1,
                'company_address2' => $request->company_address2,
                'company_address3' => $request->company_address3,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'postcode_id' => $request->postcode_id,

                'supervisor_name' => $request->supervisor_name,
                'email' => $request->email,
                'position' => $request->position,
                'job_position' => $request->job_position,
                'client_name' => $request->client_name,
                'current_wage' => $request->current_wage,
                'wage_specify' => $request->wage_specify,
                'wage_change_occur' => $request->wage_change_occur,
                'education_level' => $request->education_level,
                'grade' => $request->grade,
                'job_experience_year' => $request->job_experience_year,
                'job_experience_months' => $request->job_experience_months,
                'others' => $request->others,

                'additional_code_id' => $request->additional_code_id,
                'additional_subcode' => $additional_subcode,
                'additional_diagnosis' => $additional_diagnosis,
                'location_services' => $request->location_services,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "1"
            ];

            $validateWorkAnalysisForm = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateWorkAnalysisForm['services_id'] = 'required';
                $WorkAnalysisForm['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateWorkAnalysisForm['code_id'] = 'required';
                $WorkAnalysisForm['code_id'] =  $request->code_id;
                $validateWorkAnalysisForm['sub_code_id'] = 'required';
                $WorkAnalysisForm['sub_code_id'] =  $sub_code_id;
            } else if ($request->category_services == 'clinical') {
                $validateWorkAnalysisForm['code_id'] = 'required';
                $WorkAnalysisForm['code_id'] =  $request->code_id;
                $validateWorkAnalysisForm['sub_code_id'] = 'required';
                $WorkAnalysisForm['sub_code_id'] =  $sub_code_id;
            }

            if ($request->wage_change_occur == 'yes') {
                $validateWorkAnalysisForm['change_in_rate'] = 'required';
                $WorkAnalysisForm['change_in_rate'] =  $request->change_in_rate;
                $validateWorkAnalysisForm['from'] = 'required';
                $WorkAnalysisForm['from'] =  $request->from;
                $validateWorkAnalysisForm['to'] = 'required';
                $WorkAnalysisForm['to'] =  $request->to;
                $validateWorkAnalysisForm['on_date'] = 'required';
                $WorkAnalysisForm['on_date'] =  $request->on_date;
                $validateWorkAnalysisForm['works_hour_week'] = 'required';
                $WorkAnalysisForm['works_hour_week'] =  $request->on_date;
                $validateWorkAnalysisForm['work_schedule'] = 'required';
                $WorkAnalysisForm['work_schedule'] =  $request->work_schedule;
                $validateWorkAnalysisForm['no_of_current_employee'] = 'required';
                $WorkAnalysisForm['no_of_current_employee'] =  $request->no_of_current_employee;
                $validateWorkAnalysisForm['no_of_other_employee'] = 'required';
                $WorkAnalysisForm['no_of_other_employee'] =  $request->no_of_other_employee;
                $validateWorkAnalysisForm['during_same_shift'] = 'required';
                $WorkAnalysisForm['during_same_shift'] =  $request->during_same_shift;
            }

            $validator = Validator::make($request->all(), $validateWorkAnalysisForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            // dd($request);
            $user_diagnosis = [
                'app_id' => $request->appId,
                'patient_id' =>  $request->patient_id,
                'diagnosis_id' =>  $request->type_diagnosis_id,
                'add_diagnosis_id' => $additional_diagnosis,
                'code_id' =>  $request->code_id,
                'sub_code_id' =>  $sub_code_id,
                'add_code_id'=> $request->additional_code_id,
                'add_sub_code_id' => $additional_subcode,
                'outcome_id' =>  $request->outcome,
                'category_services' =>  $request->category_services,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            UserDiagnosis::create($user_diagnosis);
            if ($request->id) {
                WorkAnalysisForm::where(['id' => $request->id])->update($WorkAnalysisForm);
                $WorkAnalysisFormid = ($request->id);
                if (!empty($request->jobs)) {
                    foreach ($request->jobs as $key) {
                        if ($key['task_description']) {
                            $data = array('task_description' => $key['task_description'], 'patient_id' => $request->patient_id, 'objectives' => $key['objectives'], 'procedure' => $key['procedure'], 'rate_of_time' => $key['rate_of_time'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                            JobDescription::insert($data);
                        }
                    }
                }
                if (!empty($request->job_specification)) {
                    foreach ($request->job_specification as $key) {
                        if ($key['questions']) {
                            $data = array('question_name' => $key['questions'], 'patient_id' => $request->patient_id, 'answer' => $key['answer'], 'comment' => $key['comments'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                            WorkAnalysisJobSpecification::insert($data);
                        }
                    }
                }
                return response()->json(["message" => "Work Analysis Form Created Successfully!", "code" => 200]);
            } else {
                $ab = WorkAnalysisForm::firstOrCreate($WorkAnalysisForm);
                $WorkAnalysisFormid = ($ab->id);
                if (!empty($request->jobs)) {
                    foreach ($request->jobs as $key) {
                        if ($key['task_description']) {
                            $data = array('task_description' => $key['task_description'], 'patient_id' => $request->patient_id, 'objectives' => $key['objectives'], 'procedure' => $key['procedure'], 'rate_of_time' => $key['rate_of_time'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                            JobDescription::insert($data);
                        }
                    }
                }
                if (!empty($request->job_specification)) {
                    foreach ($request->job_specification as $key) {
                        if ($key['questions']) {
                            $data = array('question_name' => $key['questions'], 'patient_id' => $request->patient_id, 'answer' => $key['answer'], 'comment' => $key['comments'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                            WorkAnalysisJobSpecification::insert($data);
                        }
                    }
                }
                return response()->json(["message" => "Work Analysis Form Created Successfully!", "code" => 200]);
            }
        }
    }

    public function storeMobile(Request $request)
    {
        if ( $request->add_diagnosis_type1 != null && $request->add_diagnosis_type1 != ''){
            $additional_diagnosis = $request->add_diagnosis_type1;
            if ($request->add_diagnosis_type2 != null && $request->add_diagnosis_type2 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type2;
            }
            if ($request->add_diagnosis_type3 != null && $request->add_diagnosis_type3 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type3;
            }
            if ($request->add_diagnosis_type4 != null && $request->add_diagnosis_type4 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type4;
            }
            if ($request->add_diagnosis_type5 != null && $request->add_diagnosis_type5 != ''){
                $additional_diagnosis .= ','.$request->add_diagnosis_type5;
            }
        }

        if ( $request->additional_sub_code_id != null && $request->additional_sub_code_id != ''){
            $additional_subcode = $request->additional_sub_code_id;
            if ( $request->additional_sub_code_id1 != null && $request->additional_sub_code_id1 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id1;
            }
            if ( $request->additional_sub_code_id2 != null && $request->additional_sub_code_id2 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id2;
            }
            if ( $request->additional_sub_code_id3 != null && $request->additional_sub_code_id3 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id3;
            }
            if ( $request->additional_sub_code_id4 != null && $request->additional_sub_code_id4 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id4;
            }
            if ( $request->additional_sub_code_id5 != null && $request->additional_sub_code_id5 != ''){
                $additional_subcode .= ','.$request->additional_sub_code_id5;
            }
        }

        if ( $request->sub_code_id != null && $request->sub_code_id != ''){
            $sub_code_id = $request->sub_code_id;
            if ( $request->sub_code_id1 != null && $request->sub_code_id1 != ''){
                $sub_code_id .= ','.$request->sub_code_id1;
            }
            if ( $request->sub_code_id2 != null && $request->sub_code_id2 != ''){
                $sub_code_id .= ','.$request->additional_sub_code_id2;
            }
            if ( $request->sub_code_id3 != null && $request->sub_code_id3 != ''){
                $sub_code_id .= ','.$request->sub_code_id3;
            }
            if ( $request->sub_code_id4 != null && $request->sub_code_id4 != ''){
                $sub_code_id .= ','.$request->additional_sub_code_id4;
            }
            if ( $request->sub_code_id5 != null && $request->sub_code_id5 != ''){
                $sub_code_id .= ','.$request->sub_code_id5;
            }
        }

            if ($request->appId == null || $request->appId == '') {
                $checkTodayAppointment = PatientAppointmentDetails::where('patient_mrn_id', $request->patient_id)->whereDate("created_at", '=', date('Y-m-d'))->first();
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

                    $PatientDetails = PatientRegistration::where('id', $request->patient_id)->where('patient_mrn', 'like', '%' . $request->patient_mrn_id . '%')->first();
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
            $WorkAnalysisForm = [
                'appointment_details_id' => $request->appId,
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'company_name' => $request->company_name,
                'company_address1' => $request->company_address1,
                'company_address2' => $request->company_address2,
                'company_address3' => $request->company_address3,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'postcode_id' => $request->postcode_id,

                'supervisor_name' => $request->supervisor_name,
                'email' => $request->email,
                'position' => $request->position,
                'job_position' => $request->job_position,
                'client_name' => $request->client_name,
                'current_wage' => $request->current_wage,
                'wage_specify' => $request->wage_specify,
                'wage_change_occur' => $request->wage_change_occur,
                'education_level' => $request->education_level,
                'grade' => $request->grade,
                'job_experience_year' => $request->job_experience_year,
                'job_experience_months' => $request->job_experience_months,
                'others' => $request->others,

                'location_services' => $request->location_services,
                'type_diagnosis_id' => $request->type_diagnosis_id,
                'category_services' => $request->category_services,
                'complexity_services' => $request->complexity_services,
                'outcome' => $request->outcome,
                'medication_des' => $request->medication_des,
                'status' => "0"
            ];

            $validateWorkAnalysisForm = [];

            if ($request->category_services == 'assisstance' || $request->category_services == 'external') {
                $validateWorkAnalysisForm['services_id'] = 'required';
                $WorkAnalysisForm['services_id'] =  $request->services_id;
            } else if ($request->category_services == 'clinical-work') {
                $validateWorkAnalysisForm['code_id'] = 'required';
                $WorkAnalysisForm['code_id'] =  $request->code_id;
                $validateWorkAnalysisForm['sub_code_id'] = 'required';
                $WorkAnalysisForm['sub_code_id'] =  $sub_code_id;
                $WorkAnalysisForm['additional_diagnosis'] = $additional_diagnosis;
                $WorkAnalysisForm['additional_code_id'] = $request->additional_code_id;
                $WorkAnalysisForm['additional_subcode'] = $additional_subcode;
            }

            if ($request->wage_change_occur == 'yes') {
                $validateWorkAnalysisForm['change_in_rate'] = 'required';
                $WorkAnalysisForm['change_in_rate'] =  $request->change_in_rate;
                $validateWorkAnalysisForm['from'] = 'required';
                $WorkAnalysisForm['from'] =  $request->from;
                $validateWorkAnalysisForm['to'] = 'required';
                $WorkAnalysisForm['to'] =  $request->to;
                $validateWorkAnalysisForm['on_date'] = 'required';
                $WorkAnalysisForm['on_date'] =  $request->on_date;
                $validateWorkAnalysisForm['works_hour_week'] = 'required';
                $WorkAnalysisForm['works_hour_week'] =  $request->on_date;
                $validateWorkAnalysisForm['work_schedule'] = 'required';
                $WorkAnalysisForm['work_schedule'] =  $request->work_schedule;
                $validateWorkAnalysisForm['no_of_current_employee'] = 'required';
                $WorkAnalysisForm['no_of_current_employee'] =  $request->no_of_current_employee;
                $validateWorkAnalysisForm['no_of_other_employee'] = 'required';
                $WorkAnalysisForm['no_of_other_employee'] =  $request->no_of_other_employee;
                $validateWorkAnalysisForm['during_same_shift'] = 'required';
                $WorkAnalysisForm['during_same_shift'] =  $request->during_same_shift;
            }

            $validator = Validator::make($request->all(), $validateWorkAnalysisForm);
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors(), "code" => 422]);
            }
            $WAF=WorkAnalysisForm::create($WorkAnalysisForm);
            $WorkAnalysisFormid = ($WAF->id);

            if (!empty($request->jobs)) {
                foreach ($request->jobs as $key) {
                    if ($key['task_description']) {
                        $data = array('task_description' => $key['task_description'], 'patient_id' => $request->patient_id, 'objectives' => $key['objectives'], 'procedure' => $key['procedure'], 'rate_of_time' => $key['rate_of_time'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                        JobDescription::insert($data);
                    }
                }
            }
            if (!empty($request->job_specification)) {
                foreach ($request->job_specification as $key) {
                    if ($key['questions']) {
                        $data = array('question_name' => $key['questions'], 'patient_id' => $request->patient_id, 'answer' => $key['answer'], 'comment' => $key['comments'], 'work_analysis_form_id' => $WorkAnalysisFormid);
                        WorkAnalysisJobSpecification::insert($data);
                    }
                }
            }

            return response()->json(["message" => "Work Analysis Form Updated Successfully!", "code" => 200]);
        }
}
