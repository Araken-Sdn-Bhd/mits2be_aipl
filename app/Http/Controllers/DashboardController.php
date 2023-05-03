<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRequest;
use App\Models\ConsultationDischargeNote;
use App\Models\CounsellingProgressNote;
use App\Models\CpsHomevisitConsentForm;
use App\Models\CpsHomevisitWithdrawalForm;
use App\Models\CpsPoliceReferralForm;
use App\Models\CpsProgressNote;
use App\Models\CPSReferralForm;
use App\Models\EtpProgressNote;
use App\Models\GeneralSetting;
use App\Models\HospitalBranchManagement;
use App\Models\HospitalManagement;
use App\Models\IcdCode;
use App\Models\IcdType;
use App\Models\JobClubConsentForm;
use App\Models\JobClubProgressNote;
use App\Models\JobInterestList;
use App\Models\JobStartForm;
use App\Models\JobTransitionReport;
use App\Models\LASERAssesmenForm;
use App\Models\Notifications;
use App\Models\PatientAppointmentDetails;
use App\Models\PatientCarePaln;
use App\Models\PatientCounsellorClerkingNotes;
use App\Models\PatientIndexForm;
use App\Models\PatientRegistration;
use App\Models\PsychiatricProgressNote;
use App\Models\PsychiatryClerkingNote;
use App\Models\RehabDischargeNote;
use App\Models\SeProgressNote;
use App\Models\ServiceRegister;
use App\Models\ShharpReportGenerateHistory;
use App\Models\StaffManagement;
use App\Models\VonnAppointment;
use App\Models\Announcement;
use App\Models\User;
use App\Models\TriageForm;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\CpsDischargeNote;
use App\Models\JobEndReport;
use App\Models\JobInterestChecklist;
use App\Models\WorkAnalysisForm;
use App\Models\LogMeetingWithEmployer;
use App\Models\InternalReferralForm;
use App\Models\ExternalReferralForm;
use App\Models\Occt_Referral_Form;
use App\Models\PsychologyReferral;
use App\Models\RehabReferralAndClinicalForm;


use App\Models\Year;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ScreenPageModule;

class DashboardController extends Controller
{
    //
    public function getsystemadmin(Request $request)
    {
        $users = DB::table('patient_appointment_details')
            ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
            ->select(DB::raw('count(appointment_status) as TeamTask'))
            ->where('patient_appointment_details.appointment_status', '=', '0')
            ->groupBy('patient_appointment_details.appointment_status')
            ->get();

        $list2 = StaffManagement::select(DB::raw('count(*) as TotalMITS2User'))
            ->get();

        $list = StaffManagement::select(DB::raw('count(*) as TotalMentari'))
            ->get();

        $users2 = DB::table('state')->where('state_name', '=', $request->state_name)
            ->join('hospital_management', 'hospital_management.id', '=', 'state.country_id')
            ->select('state_name', DB::raw('count(state_name) as TotalState'))
            ->where('state.state_status', '=', '1')
            ->groupBy('state.state_name')
            ->get();

        $task = [];
        foreach ($users as $key => $value) {
            $task[] = $value;
        }
        foreach ($list as $key => $value) {
            $task[] = $value;
        }

        foreach ($list2 as $key => $value) {
            $task[] = $value;
        }

        foreach ($users2 as $key => $value) {
            $task[] = $value;
        }

        return response()->json(["message" => "System Admin", 'list' => $task, "code" => 200]);
    }


    public function getallmentaristaff(Request $request)
    {

        $today_appointment = 0;
        $query = DB::table('patient_appointment_details as p')
            ->select('p.id')
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'p.added_by');
            })
            ->leftjoin('staff_management as s', function ($join) {
                $join->on('u.email', '=', 's.email');
            })
            ->Where("booking_date", '=', date('Y-m-d'))
            ->Where("branch_id", '=', $request->branch)->get();
        $today_appointment = $query->count();

        $list = StaffManagement::select("team_id", 'id')->Where("email", '=', $request->email)->get();

        $personal_task = 0;
        $query2 = DB::table('von_appointment as v')
            ->select('v.id')
            ->leftjoin('staff_management as s', function ($join) {
                $join->on('v.interviewer_id', '=', 's.id');
            })
            ->where('v.interviewer_id', '=', $list[0]['id'])
            ->Where("booking_date", '=', date('Y-m-d'))->get();
        $personal_task = $query2->count();

        ////team task////
        $team_task = 0;

        if($list[0]['team_id']==1){

            $query = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 1)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();
            $team_task = $query->count();

        }else if($list[0]['team_id']==2 || $list[0]['team_id']==3 || $list[0]['team_id']==4 || $list[0]['team_id']==5){

            $query1 = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 2)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();

            $query2 = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 3)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();

            $query3 = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 5)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();

            $query4 = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 3)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();

            $team_task1 = $query1->count();
            $team_task2 = $query2->count();
            $team_task3 = $query3->count();
            $team_task4 = $query4->count();

            $team_task = $team_task1 + $team_task2 + $team_task3 + $team_task4;

        }else if($list[0]['team_id']==6){

            $query = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 6)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();
            $team_task = $query->count();

        }else if($list[0]['team_id']==20){

            $query = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', 20)
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();
            $team_task = $query->count();

        }

////Announcement////
$screen_id_announcement=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','AM')->first();
$announcment_route=$screen_id_announcement->dashboard_route;


        $date = date('Y-m-d');
        $staff_check = DB::table('staff_management')
                        ->leftjoin('roles', function ($join) {
                                        $join->on('roles.id', '=', 'staff_management.role_id');
                                    })
                        ->Where("staff_management.email", '=', $request->email)
                        ->pluck("roles.role_name");

        if($staff_check){
            if($staff_check[0] == "Psychiatrist"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 1, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Medical Officer"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 3, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Counsellor"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 5, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Occupational Therapy"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 7, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Staff Nurse"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 9, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Healthcare Assistant"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 11, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else{
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
        }

            ////Review Patient////
            $dateReview = Carbon::now()->subDays(7)->toDateString();
            $team = StaffManagement::select("id","team_id")->Where("email", '=', $request->email)->get();
            $screen_id_review=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','APV')->first();
            $review_route=$screen_id_review->dashboard_route;

            $review_patient = DB::table('patient_care_paln as p')
           ->select('p.id','r.name_asin_nric','p.next_review_date')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', $team[0]['team_id'])
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '>=', $dateReview)->get()->toArray();


            ////All Clinical Documentation Draft////
            $team_id= User::select('id')->Where("email", '=', $request->email)->get();
        $dateDraft = Carbon::now()->subDays(2)->toDateString();
        $screen_id=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','RPC')->first();
        $route=$screen_id->dashboard_route;
        $draft_array=[];
        $index=0;
        ///// patient care plan /////
        $draft1 = DB::table('patient_care_paln as p')
            ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
             ->leftjoin('patient_registration as r', function ($join) {
                 $join->on('p.patient_id', '=', 'r.id');
             })
             ->Where("p.added_by", '=', $team_id [0]['id'])
             ->Where("r.branch_id", '=', $request->branch)
             ->Where("p.status", '=', '0')
             ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

            $cd  = json_decode(json_encode($draft1), true);



        if($cd){
            foreach($cd as $dr => $d) {
                $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                $draft_array[$index]['name']=$d['name_asin_nric'];
                $draft_array[$index]['updated_at']=$d['updated_at'];
                $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                $index++;
            }
        }

        ///// psychiatry clerking note /////

        $draft2 = DB::table('psychiatry_clerking_note as p')
        ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
         ->leftjoin('patient_registration as r', function ($join) {
             $join->on('p.patient_mrn_id', '=', 'r.id');
         })
         ->Where("p.added_by", '=', $team_id [0]['id'])
         ->Where("r.branch_id", '=', $request->branch)
         ->Where("p.status", '=', '0')
         ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

        $cd2  = json_decode(json_encode($draft2), true);

    if($cd2){
        foreach($cd2 as $dr => $d) {
            $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
            $draft_array[$index]['name']=$d['name_asin_nric'];
            $draft_array[$index]['updated_at']=$d['updated_at'];
            $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
            $index++;

        }
    }

            ///// patient counsellor clerking notes /////

            $draft3 = DB::table('patient_counsellor_clerking_notes as p')
            ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
             ->leftjoin('patient_registration as r', function ($join) {
                 $join->on('p.patient_mrn_id', '=', 'r.id');
             })
             ->Where("p.added_by", '=', $team_id [0]['id'])
             ->Where("r.branch_id", '=', $request->branch)
             ->Where("p.status", '=', '0')
             ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

            $cd3  = json_decode(json_encode($draft3), true);


        if($cd3){
            foreach($cd3 as $dr => $d) {
                $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                $draft_array[$index]['name']=$d['name_asin_nric'];
                $draft_array[$index]['updated_at']=$d['updated_at'];
                $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                $index++;

            }
        }

                    ///// psychiatric progress note /////

                    $draft4 = DB::table('psychiatric_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd4  = json_decode(json_encode($draft4), true);


                if($cd4){
                    foreach($cd4 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps progress note/////

                                    $draft5 = DB::table('cps_progress_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd5  = json_decode(json_encode($draft5), true);


                                if($cd5){
                                    foreach($cd5 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// se progress note /////

                    $draft6 = DB::table('se_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd6  = json_decode(json_encode($draft6), true);


                if($cd6){
                    foreach($cd6 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// patient index form /////

                                    $draft7 = DB::table('patient_index_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd7  = json_decode(json_encode($draft7), true);


                                if($cd7){
                                    foreach($cd7 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// counselling progress note /////

                    $draft8 = DB::table('counselling_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd8  = json_decode(json_encode($draft8), true);


                if($cd8){
                    foreach($cd8 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// etp progress note /////

                                    $draft9 = DB::table('etp_progress_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd9  = json_decode(json_encode($draft9), true);


                                if($cd9){
                                    foreach($cd9 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job club progress note /////

                    $draft10 = DB::table('job_club_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd10  = json_decode(json_encode($draft10), true);


                if($cd10){
                    foreach($cd10 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// consultation discharge note /////

                                    $draft11 = DB::table('consultation_discharge_note as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd11  = json_decode(json_encode($draft11), true);


                                if($cd11){
                                    foreach($cd11 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// rehab discharge note /////

                    $draft12 = DB::table('rehab_discharge_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd12  = json_decode(json_encode($draft4), true);


                if($cd12){
                    foreach($cd12 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps discharge note /////

                                    $draft13 = DB::table('cps_discharge_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd13  = json_decode(json_encode($draft13), true);


                                if($cd13){
                                    foreach($cd13 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job start form /////

                    $draft14 = DB::table('job_start_form as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd14  = json_decode(json_encode($draft14), true);


                if($cd14){
                    foreach($cd14 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// job end report /////

                                    $draft15 = DB::table('job_end_report as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd15  = json_decode(json_encode($draft15), true);


                                if($cd15){
                                    foreach($cd15 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job_transition_report /////

                    $draft16 = DB::table('job_transition_report as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd16  = json_decode(json_encode($draft16), true);


                if($cd16){
                    foreach($cd16 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// triage form /////

                                    $draft17 = DB::table('triage_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd17  = json_decode(json_encode($draft17), true);


                                if($cd17){
                                    foreach($cd17 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job interest checklist /////

                    $draft18 = DB::table('job_interest_checklist as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd18  = json_decode(json_encode($draft18), true);



                if($cd18){
                    foreach($cd18 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// work analysis forms /////

                                    $draft19 = DB::table('work_analysis_forms as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd19  = json_decode(json_encode($draft19), true);



                                if($cd19){
                                    foreach($cd19 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// log meeting with employer /////

                    $draft20 = DB::table('log_meeting_with_employer as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd20  = json_decode(json_encode($draft20), true);


                if($cd20){
                    foreach($cd20 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// internal referral form /////

                                    $draft21 = DB::table('internal_referral_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd21  = json_decode(json_encode($draft21), true);


                                if($cd21){
                                    foreach($cd21 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// external referral form /////

                    $draft22 = DB::table('external_referral_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd22  = json_decode(json_encode($draft22), true);


                if($cd22){
                    foreach($cd22 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps referral form /////

                                    $draft23 = DB::table('cps_referral_form as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd23  = json_decode(json_encode($draft23), true);


                                if($cd23){
                                    foreach($cd23 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// occt referral form /////

                    $draft24 = DB::table('occt_referral_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd24  = json_decode(json_encode($draft24), true);


                if($cd24){
                    foreach($cd24 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// psychology referral /////

                                    $draft25 = DB::table('psychology_referral as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd25  = json_decode(json_encode($draft25), true);



                                if($cd25){
                                    foreach($cd25 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// rehab referral and clinical form /////

                    $draft26 = DB::table('rehab_referral_and_clinical_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd26  = json_decode(json_encode($draft26), true);


                if($cd26){
                    foreach($cd26 as $dr => $d) {

                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                $draft_array2 = array_reverse(array_values(array_column(
                    array_reverse($draft_array),
                    null,
                    'patient_id'
                )));


        return response()->json([
            "message" => "All Mentari Staffams", 'review_patient'=> $review_patient, 'review_route'=>$review_route ,'cd_draft'=>$draft_array2,'list' => $list, 'announcement_route'=>$announcment_route, 'today_appointment' => $today_appointment,
            'team_task' => $team_task, 'personal_task' => $personal_task, "code" => 200
        ]);
    }

    public function getuseradminclerk(Request $request)
    {

        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
            ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%" . $search . "%")
            ->where('patient_mrn', '=', $request->patient_mrn)
            ->orwhere('name_asin_nric', '=', $request->name_asin_nric)
            ->where('passport_no', '=', $request->passport_no)
            ->orwhere('nric_no', '=', $request->nric_no)
            ->get();

        $list = PatientAppointmentDetails::select(DB::raw('count(*) as todays_appointment'))
            ->whereDate('created_at', today())
            ->groupBy('booking_date')
            ->get();

        $users = DB::table('patient_appointment_details')
            ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
            ->select(DB::raw('count(appointment_status) as TeamTask'))
            ->where('patient_appointment_details.appointment_status', '=', '0')
            ->groupBy('patient_appointment_details.appointment_status')
            ->get();

        $list2 = AppointmentRequest::select(DB::raw('count(*) as RequestAppointment'))
            ->get();

        $list3 = PatientAppointmentDetails::select(DB::raw('count(*) as TOTAL_CASE'))
            ->get();

        $UAC = [];
        foreach ($result as $key => $value) {
            $UAC[] = $value;
        }
        foreach ($list as $key => $value) {
            $UAC[] = $value;
        }

        foreach ($users as $key => $value) {
            $UAC[] = $value;
        }

        foreach ($list2 as $key => $value) {
            $UAC[] = $value;
        }

        foreach ($list3 as $key => $value) {
            $UAC[] = $value;
        }

        return response()->json(["message" => "All Mentari Staffuac", 'list' => $UAC, "code" => 200]);
    }


    public function getshharp(Request $request)
    {

        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
            ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%" . $search . "%")
            ->where('patient_mrn', '=', $request->patient_mrn)
            ->orwhere('name_asin_nric', '=', $request->name_asin_nric)
            ->where('passport_no', '=', $request->passport_no)
            ->orwhere('nric_no', '=', $request->nric_no)
            ->get();

        $list = PatientAppointmentDetails::select(DB::raw('count(*) as TODAY_CASE'))
            ->whereDate('created_at', today())
            ->groupBy('booking_date')
            ->get();

        $list2 = PatientAppointmentDetails::select(DB::raw('count(*) as TOTAL_CASE'))
            ->get();

        $shharpcase = ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year')
            ->where('report_month', '=', $request->report_month)
            ->where('report_year', '=', $request->report_year)
            ->groupBy('report_month', 'report_year')
            ->get()->toArray();

        $Shharp = [];
        foreach ($result as $key => $value) {
            $Shharp[] = $value;
        }

        foreach ($list as $key => $value) {
            $Shharp[] = $value;
        }

        foreach ($list2 as $key => $value) {
            $Shharp[] = $value;
        }

        foreach ($shharpcase as $key => $value) {
            $Shharp[] = $value;
        }

        return response()->json(["message" => "Shharp Record", 'list' => $Shharp, "code" => 200]);
    }

    public function gethighlevelMgt(Request $request)
    {

        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
            ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%" . $search . "%")
            ->where('patient_mrn', '=', $request->patient_mrn)
            ->orwhere('name_asin_nric', '=', $request->name_asin_nric)
            ->where('passport_no', '=', $request->passport_no)
            ->orwhere('nric_no', '=', $request->nric_no)
            ->get();

        $users = AppointmentRequest::select(DB::raw('count( * ) as TotalAppointmentRequest, YEAR(appointment_request.created_at) AS Year, MONTH(appointment_request.created_at) AS Month, hospital_name '),)
            ->join('hospital_management', 'hospital_management.hospital_status', '=', 'appointment_request.id')
            ->Where('appointment_request.created_at', 'like', '%' . $request->Year . '-' . $request->Month . '%')
            ->where('hospital_name', '=', $request->hospital_name)
            ->groupBy('appointment_request.created_at', 'Month', 'hospital_name')
            ->get();

        $listSQL = AppointmentRequest::select(DB::raw('count(*) as total_appointments_request'));
        if ($request->taryear != 0)
            $listSQL->whereYear('created_at', $request->taryear);
        if ($request->tarmonth != 0)
            $listSQL->whereMonth('created_at', $request->tarmonth);
        if ($request->tarmentari != 0)
            $listSQL->where('branch_id', $request->tarmentari);

        $list = $listSQL->get();

        $totalmentarilocationSQL = HospitalBranchManagement::select(DB::raw('count(*) as TotalMentariLocation'))
                                                            ->where('hospital_branch_name', 'LIKE', '%mentari%');
        if ($request->branch_stateid != 0)
            $totalmentarilocationSQL->where('branch_state', $request->branch_stateid);
        if($request->branch_stateid == 0)
            $totalmentarilocationSQL->where('branch_state', '0');

        $totalmentarilocation = $totalmentarilocationSQL->get();



        $totalmentariSQL = HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')->where('branch_status', '=', '1')->select(DB::raw('count(*) as TotalMentari'));
        if ($request->tmpyear != 0)
            $totalmentariSQL->whereYear('created_at', $request->tmpyear);
        if ($request->tmpmonth != 0)
            $totalmentariSQL->whereMonth('created_at', $request->tmpmonth);


        $totalmentari = $totalmentariSQL->get();

        $totalpatientSQL = PatientRegistration::select(DB::raw('count(*) as TotalPatient'));
        if ($request->tmpyear != 0)
            $totalpatientSQL->whereYear('created_at', $request->tmpyear);
        if ($request->tmpmonth != 0)
            $totalpatientSQL->whereMonth('created_at', $request->tmpmonth);

        $totalpatient = $totalpatientSQL->get();

        $ShharpOverall = PatientRegistration::select(DB::raw('count(*) as Sharptotal'))
                                             ->join('sharp_registraion_final_step as srfs',function($join) {
                                                $join->on('srfs.patient_id', '=', 'patient_registration.id');
                                                })
                                            ->where('srfs.status', '=', '1')
                                            ->where('srfs.hospital_mgmt', '!=', "")
                                            ->where('patient_registration.status', '=', '1')
                                            ->groupby('patient_registration.id');

        $shharpcaseSQL = PatientRegistration::distinct()
                                            ->select('patient_registration.id', 'patient_registration.race_id', 'patient_registration.age', 'patient_registration.sex', 'patient_registration.religion_id', 'patient_registration.marital_id', 'patient_registration.education_level', 'patient_registration.occupation_status', DB::raw('count(distinct patient_registration.id) as Sharptotal'))
                                            ->join('sharp_registraion_final_step as srfs',function($join) {
                                                $join->on('srfs.patient_id', '=', 'patient_registration.id');
                                                })
                                            ->where('srfs.status', '=', '1')
                                            ->where('srfs.hospital_mgmt', '!=', "")
                                            ->where('patient_registration.status', '=', '1');



        if ($request->sharpyear != 0) {
            $ShharpOverall->whereYear('srfs.created_at', $request->sharpyear);
            $shharpcaseSQL->whereYear('srfs.created_at', $request->sharpyear);
        }
        if ($request->sharpmonth != 0) {
            $ShharpOverall->whereMonth('srfs.created_at', $request->sharpmonth);
            $shharpcaseSQL->whereMonth('srfs.created_at', $request->sharpmonth);
        }
        if ($request->sharpmentari != 0) {
            $ShharpOverall->where('patient_registration.branch_id', $request->sharpmentari);
            $shharpcaseSQL->where('patient_registration.branch_id', $request->sharpmentari);
        }

        $shharpraces = [];
        $shharpRangeAge = [];
        $shharpReligions = [];
        $shharpMaritals = [];
        $shharpEducation = [];
        $shharpEmployment = [];
        $shharpGender = [];
        $education = [];
        $maritals = [];
        $religions = [];
        $employment = [];
        $rangeofage = [];
        $shharpSelfHarm = [];
        $selfharm = [];
        $selfharmData = [];
        $races = [];
        $Shharptotal = [0];
        $male = null;
        $female = null;
        if ($request->sharprace == "Race") {
            $shharpraces = $shharpcaseSQL->groupBy('patient_registration.race_id')->get()->toArray();
            foreach ($shharpraces as $key => $value) {
                if ($value['race_id']) {
                    $races = GeneralSetting::select('id', 'section_value')->where('id', $value['race_id'])->where('status', '=', '1')->get()->toArray();
                    if (isset($races)) {
                        $shharpraces[$key]['section_value'] = $races[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Employment Status") {
            $shharpcaseSQL->where('patient_registration.occupation_status', '!=', '0');
            $shharpEmployment = $shharpcaseSQL->groupBy('patient_registration.occupation_status')->get()->toArray();
            foreach ($shharpEmployment as $key => $value) {
                if ($value['occupation_status']) {
                    $employment = GeneralSetting::select('id', 'section_value')->where('id', $value['occupation_status'])->where('status', '=', '1')->get()->toArray();
                    if (isset($employment)) {
                        $shharpEmployment[$key]['section_value'] = $employment[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Education") {
            $shharpcaseSQL->where('patient_registration.education_level', '!=', '0');
            $shharpEducation = $shharpcaseSQL->groupBy('patient_registration.education_level')->get()->toArray();
            foreach ($shharpEducation as $key => $value) {
                if ($value['education_level']) {
                    $education = GeneralSetting::select('id', 'section_value')->where('id', $value['education_level'])->where('status', '=', '1')->get()->toArray();
                    if (isset($education)) {
                        $shharpEducation[$key]['section_value'] = $education[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Gender") {
            $shharpcaseSQL->where('patient_registration.sex', '!=', '0');
            $shharpGender = $shharpcaseSQL->groupBy('patient_registration.sex')->get()->toArray();
            foreach ($shharpGender as $key => $value) {
                if ($value['sex']) {
                    $gender = GeneralSetting::select('id', 'section_value')->where('id', $value['sex'])->where('status', '=', '1')->get()->toArray();
                    if (isset($gender)) {
                        $shharpGender[$key]['section_value'] = $gender[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Method of Self harm") {
            $shharpSelfHarm = $shharpcaseSQL->get()->toArray();
            $selfharmData = SharpRegistrationSelfHarmResult::select('section_value')->where('section', 'Method of Self-Harm')->where('patient_id', $shharpSelfHarm[0]['id'])->groupby('patient_id')->get()->toArray();
            if ($selfharmData) {
                foreach ($selfharmData as $k => $v) {
                    $jsonDecode = json_decode($v['section_value'], true);
                    if (array_key_exists('Method of Self-Harm', $jsonDecode)) {
                        $jsonDecode['Method of Self-Harm']['Firearms_or_explosives'] = $jsonDecode['Method of Self-Harm']['Firearms or explosives'];
                        $jsonDecode['Method of Self-Harm']['Cutting_or_Piercing'] = $jsonDecode['Method of Self-Harm']['Cutting or Piercing'];
                        $jsonDecode['Method of Self-Harm']['Jumping_from_height'] = $jsonDecode['Method of Self-Harm']['Jumping from height'];
                        $jsonDecode['Method of Self-Harm']['Overdose_Poisoning'] = $jsonDecode['Method of Self-Harm']['Overdose/Poisoning'];
                        $jsonDecode['Method of Self-Harm']['Hanging_Suffocation'] = $jsonDecode['Method of Self-Harm']['Hanging/Suffocation'];
                        $jsonDecode['Method of Self-Harm']['Fire_flames'] = $jsonDecode['Method of Self-Harm']['Fire/flames'];
                    }
                }
            }
        } else if ($request->sharprace == "Religion") {
            $shharpcaseSQL->where('patient_registration.religion_id', '!=', '0');
            $shharpReligions = $shharpcaseSQL->groupBy('patient_registration.religion_id')->get()->toArray();
            foreach ($shharpReligions as $key => $value) {
                if ($value['religion_id']) {
                    $religions = GeneralSetting::select('id', 'section_value')->where('id', $value['religion_id'])->where('status', '=', '1')->get()->toArray();
                    if (isset($religions)) {
                        $shharpReligions[$key]['section_value'] = $religions[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Marital Status") {
            $shharpcaseSQL->where('patient_registration.marital_id', '!=', '0');
            $shharpMaritals = $shharpcaseSQL->groupBy('patient_registration.marital_id')->get()->toArray();
            foreach ($shharpMaritals as $key => $value) {
                if ($value['marital_id']) {
                    $maritals = GeneralSetting::select('id', 'section_value')->where('id', $value['marital_id'])->where('status', '=', '1')->get()->toArray();
                    if (isset($maritals)) {
                        $shharpMaritals[$key]['section_value'] = $maritals[0]['section_value'] ?? null;
                    }
                }
            }
        } else if ($request->sharprace == "Range of Age") {
            $shharpRangeAge = $shharpcaseSQL->select(DB::raw('(CASE WHEN `age` < 10 THEN "Less Than 10 Years" WHEN `age` BETWEEN 10 and 19 THEN "Between 10 and 19 Years" WHEN `age` BETWEEN 20 and 59 THEN "Between 20 and 59 Years" WHEN `age` > 59 THEN "60 Years and Above" ELSE "null" END) AS RANGE_AGE, count(distinct patient_registration.id) as COUNT'))->groupBy('RANGE_AGE')->orderBy('patient_registration.age')->get()->toArray();
            foreach ($shharpRangeAge as $key => $value) {
                if ($value['RANGE_AGE']) {
                    $rangeofage = GeneralSetting::select('id', 'section_value')->where('section_value', $value['RANGE_AGE'])->where('status', '=', '1')->get()->toArray();
                    if (isset($rangeofage)) {
                        $shharpRangeAge[$key]['section_value'] = $rangeofage[0]['section_value'] ?? null;
                    }
                }
            }
        }


        $shharpcase = $shharpcaseSQL->get();
        $Shharptotal = $ShharpOverall->get();

        function random_color_part()
        {
            return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        }

        function random_color()
        {
            return random_color_part() . random_color_part() . random_color_part();
        }
        $clinicrepor1 = PatientRegistration::select('services_type', DB::raw('count( * ) as TotalPatient'))->groupBy('services_type');
        if ($request->scryear != 0)
            $clinicrepor1->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicrepor1->whereMonth('created_at', '=', $request->scrmonth);
        if ($request->scrmentari != 0)
            $clinicrepor1->where('branch_id', '=', $request->scrmentari);
        $clini22 = $clinicrepor1->get();
        foreach ($clini22 as $key => $value) {
            if ($value['services_type']) {
                $aa = ServiceRegister::select('service_name')->where('id', $value['services_type'])->get();
                if (isset($aa)) {
                    $clini22[$key]['service_name'] = $aa[0]['service_name'] ?? null;
                    $clini22[$key]['color'] = random_color();
                }
            }
        }

        $id = IcdType::select('id')->where('icd_type_code', "=", 'ICD-10')->get();


        $tabData = [
            array("tab" => "psychiatry_clerking_note", "col" => "type_diagnosis_id" , "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "patient_counsellor_clerking_notes", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "psychiatric_progress_note", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "cps_progress_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "se_progress_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "counselling_progress_note", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "etp_progress_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "job_club_progress_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "consultation_discharge_note", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "rehab_discharge_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "cps_discharge_note", "col" => "diagnosis_type", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "patient_care_paln", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "job_start_form", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "job_end_report", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "job_transition_report", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "laser_assesmen_form", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "triage_form", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "work_analysis_forms", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "list_job_club", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "list_of_etp", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "list_of_job_search", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "log_meeting_with_employer", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "list_previous_current_job", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "internal_referral_form", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "external_referral_form", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "cps_referral_form", "col" => "type_of_diagnosis", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "occt_referral_form", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "psychology_referral", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
            array("tab" => "rehab_referral_and_clinical_form", "col" => "type_diagnosis_id", "created_at" => "created_at", "added_by" => "added_by"),
        ];
        $qry = "";
        $id = IcdType::select('id')->where('icd_type_code', "=", 'ICD-10')->get();

        foreach ($tabData as $key => $value) {
                $qry .= ($qry == "") ? ' ' : ' union all ';
                $qry .= "SELECT count(cpn.{$value['col']}) count_ , cpn.{$value['col']} id_
                FROM {$value['tab']} cpn
                JOIN icd_code ic on ic.id = cpn.{$value['col']}
                JOIN icd_category icdcat on icdcat.id = ic.icd_category_id ";

                if($request->scryear != 0){
                    $qry .= " AND YEAR(cpn.{$value['created_at']}) = $request->scryear";
                }

                if($request->scrmonth != 0){
                    $qry .= " AND MONTH(cpn.{$value['created_at']}) = $request->scrmonth";
                }

                if($request->scrmentari != 0){
                    $qry .= " JOIN staff_management sm on sm.id = cpn.{$value['added_by']} AND sm.branch_id = $request->scrmentari";
                }

                $qry .= " WHERE cpn.{$value['col']} in (select ic.id  from icd_code ic where ic.icd_type_id={$id[0]['id']} ) group by icdcat.icd_category_code";
        }



        $diagnosis  = DB::select("SELECT sum(bb.count_) sum_, bb.id_ , icd.icd_code, icdcat.icd_category_code from ( $qry ) bb , icd_code icd join icd_category icdcat on icdcat.id = icd.icd_category_id where bb.id_=icd.id group by icdcat.icd_category_code;");

        $clinicreportSQLseD = JobInterestList::select(DB::raw('count( * ) as SeProgressNote'));
        if ($request->scryear != 0)
            $clinicreportSQLseD->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLseD->whereMonth('created_at', '=', $request->scrmonth);
        $clinicreportSeD = $clinicreportSQLseD->get();


        $kpiSQL = SeProgressNote::select(DB::raw('count( se_progress_note.employment_status ) as kpiTotalCaseLoad'));
        if ($request->kpiyear != 0)
            $kpiSQL->whereYear('se_progress_note.created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiSQL->whereMonth('se_progress_note.created_at', '=', $request->kpimonth);
        if ($request->kpimentari != 0)
            $kpiSQL->join('patient_registration', 'se_progress_note.patient_id', '=', 'patient_registration.id')
                   ->where('patient_registration.branch_id', '=', $request->kpimentari);

        $kpi = $kpiSQL->get();

        $employid = 0;
        $unemployid = 0;
        $terminateid = 0;
        $employ = GeneralSetting::select('id', 'section_value')->where('section', "=", 'employment-status')->where('status', "=", '1')->get();
        foreach ($employ as $key => $value) {
            if ($value['section_value'] == "Employed") {
                $employid = $value['id'];
            } elseif ($value['section_value'] == "Unemployed") {
                $unemployid = $value['id'];
            } else {
                $terminateid = $value['id'];
            }
        }
        $kpiEmployement1 = SeProgressNote::select(DB::raw('count( se_progress_note.employment_status ) as employed'))
            ->where('se_progress_note.employment_status', '=', $employid);
        if ($request->kpiyear != 0)
            $kpiEmployement1->whereYear('se_progress_note.created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiEmployement1->whereMonth('se_progress_note.created_at', '=', $request->kpimonth);
        if ($request->kpimentari != 0)
            $kpiEmployement1->join('patient_registration', 'se_progress_note.patient_id', '=', 'patient_registration.id')
                            ->where('patient_registration.branch_id', '=', $request->kpimentari);

        $kpiEmployement = $kpiEmployement1->get();

        $kpiUnemployement1 = SeProgressNote::select(DB::raw('count( se_progress_note.employment_status ) as unemployed'))
            ->where('se_progress_note.employment_status', '=', $unemployid);
        if ($request->kpiyear != 0)
            $kpiUnemployement1->whereYear('se_progress_note.created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiUnemployement1->whereMonth('se_progress_note.created_at', '=', $request->kpimonth);
        if ($request->kpimentari != 0)
            $kpiUnemployement1->join('patient_registration', 'se_progress_note.patient_id', '=', 'patient_registration.id')
                              ->where('patient_registration.branch_id', '=', $request->kpimentari);

        $kpiUnemployement = $kpiUnemployement1->get();

        $kpiTerminated1 = SeProgressNote::select(DB::raw('count( se_progress_note.employment_status ) as terminate'))
            ->where('se_progress_note.employment_status', '=', $terminateid);
        if ($request->kpiyear != 0)
            $kpiTerminated1->whereYear('se_progress_note.created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiTerminated1->whereMonth('se_progress_note.created_at', '=', $request->kpimonth);
        if ($request->kpimentari != 0)
            $kpiTerminated1->join('patient_registration', 'se_progress_note.patient_id', '=', 'patient_registration.id')
                           ->where('patient_registration.branch_id', '=', $request->kpimentari);

        $kpiTerminated = $kpiTerminated1->get();

        $HLM = [];

        return response()->json([
            "message" => "High Level Mgt", 'TotalMintari' => $totalmentari,
            'TotalAppoitment' => $list, 'User1' => $users,
            'totalpatient' => $totalpatient,
            'totalmentarilocation' => $totalmentarilocation,
            'totalsharp' => $Shharptotal,
            'kpi' => $kpi,
            "kpiEmployement" => $kpiEmployement,
            "kpiUnemployement" => $kpiUnemployement,
            "kpiTerminated" => $kpiTerminated,
            "shharpGender" => $shharpGender,
            "summaryActivity" => $clini22,
            "shharpRaces" => $shharpraces,
            "rangeOfAge" => $shharpRangeAge,
            "shharpReligions" => $shharpReligions,
            "shharpMaritals" => $shharpMaritals,
            "shharpEducation" => $shharpEducation,
            "shharpEmployment" => $shharpEmployment,
            "diagnosis" => $diagnosis,
            "code" => 200
        ]);
    }

    public function getYears()
    {
        $list = Year::select('id', 'years')
            ->get();
        return response()->json(["message" => "Years List", 'list' => $list, "code" => 200]);
    }

    public function AdminSpeciallist(Request $request)
    {

        $today_appointment = 0;
        $query = DB::table('patient_appointment_details as p')
            ->select('p.id')
            ->leftjoin('users as u', function ($join) {
                $join->on('u.id', '=', 'p.added_by');
            })
            ->leftjoin('staff_management as s', function ($join) {
                $join->on('u.email', '=', 's.email');
            })
            ->Where("booking_date", '=', date('Y-m-d'))
            ->Where("branch_id", '=', $request->branch)->get();
        $today_appointment = $query->count();

        $list = StaffManagement::select("team_id", "id")->Where("email", '=', $request->email)->get();

        $personal_task = 0;
        $query2 = DB::table('von_appointment as v')
            ->select('v.id')
            ->leftjoin('staff_management as s', function ($join) {
                $join->on('v.interviewer_id', '=', 's.id');
            })
            ->where('v.interviewer_id', '=', $list[0]['id'])
            ->Where("booking_date", '=', date('Y-m-d'))->get();
        $personal_task = $query2->count();


        $team_task = 0;

        $query3 = DB::table('patient_care_paln as p')
            ->select('p.id')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("p.services", '=', $list[0]['team_id'])
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '=', date('Y-m-d'))->get();
        $team_task = $query3->count();

        $request_appointment = 0;
        $query = DB::table('appointment_request')
            ->Where("branch_id", '=', $request->branch)
            ->Where("status", '=', '1')
            ->get();
        $request_appointment = $query->count();

        $date = date('Y-m-d');

$screen_id_announcement=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','AM')->first();
$announcment_route=$screen_id_announcement->dashboard_route;

        $staff_check = DB::table('staff_management')
                        ->leftjoin('roles', function ($join) {
                                        $join->on('roles.id', '=', 'staff_management.role_id');
                                    })
                        ->Where("staff_management.email", '=', $request->email)
                        ->pluck("roles.role_name");

        if($staff_check){
            if($staff_check[0] == "Psychiatrist"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 1, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Medical Officer"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 3, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Counsellor"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 5, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Occupational Therapy"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 7, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Staff Nurse"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 9, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else if($staff_check[0] == "Healthcare Assistant"){
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->where(DB::raw('substr(audience_ids, 11, 1)'), '=' , 1)
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
            else{
                $list = Announcement::select("id", "title", "start_date", "end_date")
                ->Where("branch_id", '=', $request->branch)
                ->Where("status", '=', "1")
                ->Where("start_date", '<=', $date)
                ->Where("end_date", '>=', $date)
                ->OrderBy("start_date", 'DESC')
                ->get();
            }
        }

            ////Review Patient////
            $dateReview = Carbon::now()->subDays(7)->toDateString();
            $team = StaffManagement::select("id","team_id")->Where("email", '=', $request->email)->get();
            $screen_id_review=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','APV')->first();
            $review_route=$screen_id_review->dashboard_route;

            $review_patient = DB::table('patient_care_paln as p')
           ->select('p.id','r.name_asin_nric','p.next_review_date')
            ->leftjoin('patient_registration as r', function ($join) {
                $join->on('p.patient_id', '=', 'r.id');
            })
            ->Where("r.services_type", '=', $team[0]['team_id'])
            ->Where("r.branch_id", '=', $request->branch)
            ->Where("p.next_review_date", '>=', $dateReview)->get()->toArray();


            ////All Clinical Documentation Draft////
            $team_id= User::select('id')->Where("email", '=', $request->email)->get();
        $dateDraft = Carbon::now()->subDays(2)->toDateString();
        $screen_id=ScreenPageModule::select('id','dashboard_route')->where('notifi_code','=','RPC')->first();
        $route=$screen_id->dashboard_route;
        $draft_array=[];
        $index=0;
        ///// patient care plan /////
        $draft1 = DB::table('patient_care_paln as p')
            ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
             ->leftjoin('patient_registration as r', function ($join) {
                 $join->on('p.patient_id', '=', 'r.id');
             })
             ->Where("p.added_by", '=', $team_id [0]['id'])
             ->Where("r.branch_id", '=', $request->branch)
             ->Where("p.status", '=', '0')
             ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

            $cd  = json_decode(json_encode($draft1), true);



        if($cd){
            foreach($cd as $dr => $d) {
                $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                $draft_array[$index]['name']=$d['name_asin_nric'];
                $draft_array[$index]['updated_at']=$d['updated_at'];
                $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                $index++;
            }
        }

        ///// psychiatry clerking note /////

        $draft2 = DB::table('psychiatry_clerking_note as p')
        ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
         ->leftjoin('patient_registration as r', function ($join) {
             $join->on('p.patient_mrn_id', '=', 'r.id');
         })
         ->Where("p.added_by", '=', $team_id [0]['id'])
         ->Where("r.branch_id", '=', $request->branch)
         ->Where("p.status", '=', '0')
         ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

        $cd2  = json_decode(json_encode($draft2), true);

    if($cd2){
        foreach($cd2 as $dr => $d) {
            $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
            $draft_array[$index]['name']=$d['name_asin_nric'];
            $draft_array[$index]['updated_at']=$d['updated_at'];
            $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
            $index++;

        }
    }

            ///// patient counsellor clerking notes /////

            $draft3 = DB::table('patient_counsellor_clerking_notes as p')
            ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
             ->leftjoin('patient_registration as r', function ($join) {
                 $join->on('p.patient_mrn_id', '=', 'r.id');
             })
             ->Where("p.added_by", '=', $team_id [0]['id'])
             ->Where("r.branch_id", '=', $request->branch)
             ->Where("p.status", '=', '0')
             ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

            $cd3  = json_decode(json_encode($draft3), true);


        if($cd3){
            foreach($cd3 as $dr => $d) {
                $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                $draft_array[$index]['name']=$d['name_asin_nric'];
                $draft_array[$index]['updated_at']=$d['updated_at'];
                $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                $index++;

            }
        }

                    ///// psychiatric progress note /////

                    $draft4 = DB::table('psychiatric_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd4  = json_decode(json_encode($draft4), true);


                if($cd4){
                    foreach($cd4 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps progress note/////

                                    $draft5 = DB::table('cps_progress_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd5  = json_decode(json_encode($draft5), true);


                                if($cd5){
                                    foreach($cd5 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// se progress note /////

                    $draft6 = DB::table('se_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd6  = json_decode(json_encode($draft6), true);


                if($cd6){
                    foreach($cd6 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// patient index form /////

                                    $draft7 = DB::table('patient_index_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd7  = json_decode(json_encode($draft7), true);


                                if($cd7){
                                    foreach($cd7 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// counselling progress note /////

                    $draft8 = DB::table('counselling_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd8  = json_decode(json_encode($draft8), true);


                if($cd8){
                    foreach($cd8 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// etp progress note /////

                                    $draft9 = DB::table('etp_progress_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd9  = json_decode(json_encode($draft9), true);


                                if($cd9){
                                    foreach($cd9 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job club progress note /////

                    $draft10 = DB::table('job_club_progress_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd10  = json_decode(json_encode($draft10), true);


                if($cd10){
                    foreach($cd10 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// consultation discharge note /////

                                    $draft11 = DB::table('consultation_discharge_note as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd11  = json_decode(json_encode($draft11), true);


                                if($cd11){
                                    foreach($cd11 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// rehab discharge note /////

                    $draft12 = DB::table('rehab_discharge_note as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd12  = json_decode(json_encode($draft4), true);


                if($cd12){
                    foreach($cd12 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps discharge note /////

                                    $draft13 = DB::table('cps_discharge_note as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd13  = json_decode(json_encode($draft13), true);


                                if($cd13){
                                    foreach($cd13 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job start form /////

                    $draft14 = DB::table('job_start_form as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd14  = json_decode(json_encode($draft14), true);


                if($cd14){
                    foreach($cd14 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// job end report /////

                                    $draft15 = DB::table('job_end_report as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd15  = json_decode(json_encode($draft15), true);


                                if($cd15){
                                    foreach($cd15 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job_transition_report /////

                    $draft16 = DB::table('job_transition_report as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd16  = json_decode(json_encode($draft16), true);


                if($cd16){
                    foreach($cd16 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// triage form /////

                                    $draft17 = DB::table('triage_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd17  = json_decode(json_encode($draft17), true);


                                if($cd17){
                                    foreach($cd17 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// job interest checklist /////

                    $draft18 = DB::table('job_interest_checklist as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd18  = json_decode(json_encode($draft18), true);



                if($cd18){
                    foreach($cd18 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// work analysis forms /////

                                    $draft19 = DB::table('work_analysis_forms as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd19  = json_decode(json_encode($draft19), true);



                                if($cd19){
                                    foreach($cd19 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// log meeting with employer /////

                    $draft20 = DB::table('log_meeting_with_employer as p')
                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd20  = json_decode(json_encode($draft20), true);


                if($cd20){
                    foreach($cd20 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// internal referral form /////

                                    $draft21 = DB::table('internal_referral_form as p')
                                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_mrn_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd21  = json_decode(json_encode($draft21), true);


                                if($cd21){
                                    foreach($cd21 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// external referral form /////

                    $draft22 = DB::table('external_referral_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd22  = json_decode(json_encode($draft22), true);


                if($cd22){
                    foreach($cd22 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// cps referral form /////

                                    $draft23 = DB::table('cps_referral_form as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd23  = json_decode(json_encode($draft23), true);


                                if($cd23){
                                    foreach($cd23 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// occt referral form /////

                    $draft24 = DB::table('occt_referral_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd24  = json_decode(json_encode($draft24), true);


                if($cd24){
                    foreach($cd24 as $dr => $d) {
                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                                    ///// psychology referral /////

                                    $draft25 = DB::table('psychology_referral as p')
                                    ->select('p.id','p.patient_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                                     ->leftjoin('patient_registration as r', function ($join) {
                                         $join->on('p.patient_id', '=', 'r.id');
                                     })
                                     ->Where("p.added_by", '=', $team_id [0]['id'])
                                     ->Where("r.branch_id", '=', $request->branch)
                                     ->Where("p.status", '=', '0')
                                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                                    $cd25  = json_decode(json_encode($draft25), true);



                                if($cd25){
                                    foreach($cd25 as $dr => $d) {
                                        $draft_array[$index]['patient_id']=$d['patient_id'] ??= $d;
                                        $draft_array[$index]['name']=$d['name_asin_nric'];
                                        $draft_array[$index]['updated_at']=$d['updated_at'];
                                        $draft_array[$index]['route']=$route.'?id='.$d['patient_id'].'&appId='.$d['appointment_details_id'];
                                        $index++;

                                    }
                                }

                                                    ///// rehab referral and clinical form /////

                    $draft26 = DB::table('rehab_referral_and_clinical_form as p')
                    ->select('p.id','p.patient_mrn_id','p.appointment_details_id','r.name_asin_nric','p.updated_at')
                     ->leftjoin('patient_registration as r', function ($join) {
                         $join->on('p.patient_mrn_id', '=', 'r.id');
                     })
                     ->Where("p.added_by", '=', $team_id [0]['id'])
                     ->Where("r.branch_id", '=', $request->branch)
                     ->Where("p.status", '=', '0')
                     ->Where("p.updated_at", '<', $dateDraft)->get()->toArray();

                    $cd26  = json_decode(json_encode($draft26), true);


                if($cd26){
                    foreach($cd26 as $dr => $d) {

                        $draft_array[$index]['patient_id']=$d['patient_mrn_id'] ??= $d;
                        $draft_array[$index]['name']=$d['name_asin_nric'];
                        $draft_array[$index]['updated_at']=$d['updated_at'];
                        $draft_array[$index]['route']=$route.'?id='.$d['patient_mrn_id'].'&appId='.$d['appointment_details_id'];
                        $index++;

                    }
                }

                $draft_array2 = array_reverse(array_values(array_column(
                    array_reverse($draft_array),
                    null,
                    'patient_id'
                )));


        return response()->json([
            "message" => "Admin & Specialist inCharge", 'today_appointment' => $today_appointment, 'review_route'=>$review_route, 'review_patient'=> $review_patient,'cd_draft'=>$draft_array2,
            'personal_task' => $personal_task, 'team_task' => $team_task, 'request_appointment' => $request_appointment, 'list' => $list, 'announcement_route'=>$announcment_route,  "code" => 200
        ]);
    }
}

