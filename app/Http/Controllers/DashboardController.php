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
use App\Models\ShharpReportGenerateHistory;
use App\Models\StaffManagement;
use App\Models\TriageForm;
use App\Models\Year;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $search = "";
        $result = PatientRegistration::select("patient_mrn", "name_asin_nric", "passport_no", "nric_no")
            ->Where(DB::raw("concat(patient_mrn, ' ', name_asin_nric)"), 'LIKE', "%" . $search . "%")
            ->where('patient_mrn', '=', $request->patient_mrn)
            ->orwhere('name_asin_nric', '=', $request->name_asin_nric)
            ->where('passport_no', '=', $request->passport_no)
            ->orwhere('nric_no', '=', $request->nric_no)
            ->get();

        $list = PatientAppointmentDetails::select(DB::raw('count(*) as todays_appointment'))
            //    ->whereDate('created_at', today())
            ->where('booking_date', date('Y-m-d'))
            ->groupBy('booking_date')
            ->get();

        $team_task = DB::table('patient_appointment_details')
            ->join('patient_index_form', 'patient_index_form.patient_mrn_id', '=', 'patient_appointment_details.patient_mrn_id')
            ->select(DB::raw('count(appointment_status) as team_task'))
            ->where('patient_appointment_details.appointment_status', '=', '0')
            ->groupBy('patient_appointment_details.appointment_status')
            ->get();

        $AMS = [];
        // foreach ($result as $key => $value) {
        //     $AMS[] = $value;
        // }
        // $count = [];
        // foreach ($list as $key => $value) {
        //     $count[] = $value;
        // }

        // foreach ($users as $key => $value) {
        //     $count[] = $value;
        // }

        return response()->json(["message" => "All Mentari Staff", 'list' => $AMS, 'today_appointment' => $list,
        'team_task' => $team_task, "code" => 200]);
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

        return response()->json(["message" => "All Mentari Staff", 'list' => $UAC, "code" => 200]);
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

        $listSQL = PatientAppointmentDetails::select(DB::raw('count(*) as total_appointments_request'));
        if ($request->taryear != 0)
            $listSQL->whereYear('created_at', $request->taryear);
        if ($request->tarmonth != 0)
            $listSQL->whereMonth('created_at', $request->tarmonth);
        // if ($request->tarmentari != 0)
        //     $listSQL->where('hospital_id', $request->tarmentari);

        $list = $listSQL->get();

        $totalmentarilocationSQL = HospitalBranchManagement::select(DB::raw('count(*) as TotalMentariLocation'));
        if ($request->taryear != 0)
            $totalmentarilocationSQL->whereYear('created_at', $request->taryear);
        if ($request->tarmonth != 0)
            $totalmentarilocationSQL->whereMonth('created_at', $request->tarmonth);
        if ($request->tarmentari != 0)
            $totalmentarilocationSQL->where('hospital_id', $request->tarmentari);

        $totalmentarilocation = $totalmentarilocationSQL->get();



        $totalmentariSQL = HospitalManagement::select(DB::raw('count(*) as TotalMentari'));
        if ($request->tmpyear != 0)
            $totalmentariSQL->whereYear('created_at', $request->tmpyear);
        if ($request->tmpmonth != 0)
            $totalmentariSQL->whereMonth('created_at', $request->tmpmonth);

        // if ($request->tarmentari != 0)
        //     $totalmentariSQL->whereMonth('created_at', $request->tarmentari);

        $totalmentari = $totalmentariSQL->get();

        $totalpatientSQL = PatientRegistration::select(DB::raw('count(*) as TotalPatient'));
        if ($request->tmpyear != 0)
            $totalpatientSQL->whereYear('created_at', $request->tmpyear);
        if ($request->tmpmonth != 0)
            $totalpatientSQL->whereMonth('created_at', $request->tmpmonth);

        $totalpatient = $totalpatientSQL->get();

        // $users2 = DB::table('state')->where('state_name', '=', $request->state_name)
        //     ->join('hospital_management', 'hospital_management.id', '=', 'state.country_id')
        //     ->select('state_name', DB::raw('count(state_name) as TotalState'))
        //     ->where('state.state_status', '=', '1')
        //     ->groupBy('state.state_name')
        //     ->get();

        // $shharpcase = ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'), DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary' WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5 THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'  WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'  WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November' ELSE 'December' END as month"), 'report_month', 'report_year', 'state_name')
        //     ->join('state', 'state.id', '=', 'shharp_report_generate_history.id')
        //     ->where('report_month', '=', $request->report_month)
        //     ->where('report_year', '=', $request->report_year)
        //     ->where('state_name', '=', $request->state_name)
        //     ->groupBy('report_month', 'report_year', 'state_name')
        //     ->get()->toArray();

        $shharpcaseSQL = ShharpReportGenerateHistory::select(DB::raw('count(*) as Sharptotal'));
        if ($request->sharpyear != 0)
            $shharpcaseSQL->whereYear('report_year', $request->sharpyear);
        if ($request->sharpmonth != 0)
            $shharpcaseSQL->whereMonth('report_month', $request->sharpmonth);
        // if ($request->sharpmentari != 0)
        //     $shharpcaseSQL->where('hospital_id', $request->sharpmentari);


        $shharpcase = $shharpcaseSQL->get();
        // dd($shharpcase);

        $clinicreportSQLse = SeProgressNote::select(DB::raw('count( * ) as SeProgressNote'));
        if ($request->scryear != 0)
            $clinicreportSQLse->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLse->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLse->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportSe = $clinicreportSQLse->get();

        $clinicreportSQLConsultation = ConsultationDischargeNote::select(DB::raw('count( * ) as ConsultationDischargeNote'));
        if ($request->scryear != 0)
            $clinicreportSQLConsultation->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLConsultation->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLse->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportConsultation = $clinicreportSQLConsultation->get();

        $clinicreportSQLjobclub = JobClubConsentForm::select(DB::raw('count( * ) as JobClubConsentForm'));
        if ($request->scryear != 0)
            $clinicreportSQLjobclub->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLjobclub->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLse->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportjobclub = $clinicreportSQLjobclub->get();

        $clinicreportSQLrehabilitation = RehabDischargeNote::select(DB::raw('count( * ) as RehabDischargeNote'));
        if ($request->scryear != 0)
            $clinicreportSQLrehabilitation->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLrehabilitation->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLse->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportrehabilitation = $clinicreportSQLrehabilitation->get();

        $clinicreportSQLCps = CPSReferralForm::select(DB::raw('count( * ) as CPSReferralForm'));
        if ($request->scryear != 0)
            $clinicreportSQLCps->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLCps->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLse->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportCps = $clinicreportSQLCps->get();

        $summaryActivity = $clinicreportSe[0]['SeProgressNote'] + $clinicreportConsultation[0]['ConsultationDischargeNote'] +
            $clinicreportjobclub[0]['JobClubConsentForm'] + $clinicreportrehabilitation[0]['RehabDischargeNote'] +
            $clinicreportCps[0]['CPSReferralForm'];
        // dd($summaryActivity);
        // ----------------------------start for diagnosis---------------------------------------

        $id = IcdType::select('id')->where('icd_type_code', "=", 'ICD-10')->get();
        // dd($id[0]['id']); "F00-F07",


        $tabData = [
            array("tab" => "psychiatry_clerking_note", "col" => "type_diagnosis_id"),
            array("tab" => "patient_counsellor_clerking_notes", "col" => "type_diagnosis_id"),
            array("tab" => "psychiatric_progress_note", "col" => "type_diagnosis_id"),
            array("tab" => "cps_progress_note", "col" => "diagnosis_type"),
            array("tab" => "se_progress_note", "col" => "diagnosis_type"),
            array("tab" => "counselling_progress_note", "col" => "type_diagnosis_id"),
            array("tab" => "etp_progress_note", "col" => "diagnosis_type"),
            array("tab" => "job_club_progress_note", "col" => "diagnosis_type"),
            array("tab" => "consultation_discharge_note", "col" => "type_diagnosis_id"),
            array("tab" => "rehab_discharge_note", "col" => "diagnosis_type"),
            array("tab" => "cps_discharge_note", "col" => "diagnosis_type"),
            array("tab" => "patient_care_paln", "col" => "type_of_diagnosis"),
            array("tab" => "job_start_form", "col" => "type_of_diagnosis"),
            array("tab" => "job_end_report", "col" => "type_of_diagnosis"),
            array("tab" => "job_transition_report", "col" => "type_of_diagnosis"),
            array("tab" => "laser_assesmen_form", "col" => "type_of_diagnosis"),
            array("tab" => "triage_form", "col" => "type_diagnosis_id"),
            array("tab" => "work_analysis_forms", "col" => "type_diagnosis_id"),
            array("tab" => "list_job_club", "col" => "type_diagnosis_id"),
            array("tab" => "list_of_etp", "col" => "type_diagnosis_id"),
            array("tab" => "list_of_job_search", "col" => "type_diagnosis_id"),
            array("tab" => "log_meeting_with_employer", "col" => "type_diagnosis_id"),
            array("tab" => "list_previous_current_job", "col" => "type_diagnosis_id"),
            array("tab" => "internal_referral_form", "col" => "type_diagnosis_id"),
            array("tab" => "external_referral_form", "col" => "type_diagnosis_id"),
            array("tab" => "cps_referral_form", "col" => "type_of_diagnosis"),
            array("tab" => "occt_referral_form", "col" => "type_diagnosis_id"),
            array("tab" => "psychology_referral", "col" => "type_diagnosis_id"),
            array("tab" => "rehab_referral_and_clinical_form", "col" => "type_diagnosis_id"),
        ];
        $qry = "";
        $id = IcdType::select('id')->where('icd_type_code', "=", 'ICD-10')->get();
        // dd($id[0]['id']);

        foreach ($tabData as $key => $value) { 
            if ($qry) {
                $qry .= " union all ";
            }
            $qry .= "SELECT count(cpn.{$value['col']}) count_ , cpn.{$value['col']} id_
            FROM {$value['tab']} cpn
            WHERE cpn.{$value['col']} in (select ic.id  from icd_category ic where ic.icd_type_id={$id[0]['id']} ) group by cpn.{$value['col']}";
        }


        $diagnosis  = DB::select("SELECT sum(bb.count_) sum_, bb.id_ , icd.icd_category_code from ( $qry ) bb , icd_category icd where bb.id_=icd.id group by icd.id,bb.id_,icd.icd_category_code;");
        // dd($diagnosis);
        // dd("SELECT sum(bb.count_) sum_, bb.id_ , icd.icd_category_code from ( $qry ) bb , icd_category icd where bb.id_=icd.id group by icd.id;");

        $clinicreportSQLseD = JobInterestList::select(DB::raw('count( * ) as SeProgressNote'));
        if ($request->scryear != 0)
            $clinicreportSQLseD->whereYear('created_at', '=', $request->scryear);
        if ($request->scrmonth != 0)
            $clinicreportSQLseD->whereMonth('created_at', '=', $request->scrmonth);
        // if ($request->scrmentari != 0)
        // $clinicreportSQLseD->whereYear('created_at', '=', $request->scrmentari);
        $clinicreportSeD = $clinicreportSQLseD->get();


        // --------------------------end for Diagnosis graph-----------------------------

        $kpiSQL = SeProgressNote::select(DB::raw('count( employment_status ) as kpiTotalCaseLoad'));
        if ($request->kpiyear != 0)
            $kpiSQL->whereYear('created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiSQL->whereMonth('created_at', '=', $request->kpimonth);
        $kpi = $kpiSQL->get();


        $employid = 0;
        $unemployid = 0;
        $terminateid = 0;
        $employ = GeneralSetting::select('id', 'section_value')->where('section', "=", 'employment-status')->where('status', "=", '1')->get();
        foreach ($employ as $key => $value) {
            // dd($value['section_value']);
            if ($value['section_value'] == "Employed") {
                $employid = $value['id'];
            } elseif ($value['section_value'] == "Unemployed") {
                $unemployid = $value['id'];
            } else {
                $terminateid = $value['id'];
            }
        }
        // dd($employid);
        $kpiEmployement1 = SeProgressNote::select(DB::raw('count( employment_status ) as employed'))
            ->where('employment_status', '=', $employid);
        if ($request->kpiyear != 0)
            $kpiEmployement1->whereYear('created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiEmployement1->whereMonth('created_at', '=', $request->kpimonth);
        $kpiEmployement = $kpiEmployement1->get();

        $kpiUnemployement1 = SeProgressNote::select(DB::raw('count( employment_status ) as unemployed'))
            ->where('employment_status', '=', $unemployid);
        if ($request->kpiyear != 0)
            $kpiUnemployement1->whereYear('created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiUnemployement1->whereMonth('created_at', '=', $request->kpimonth);
        $kpiUnemployement = $kpiUnemployement1->get();

        $kpiTerminated1 = SeProgressNote::select(DB::raw('count( employment_status ) as terminate'))
            ->where('employment_status', '=', $terminateid);
        if ($request->kpiyear != 0)
            $kpiTerminated1->whereYear('created_at', '=', $request->kpiyear);
        if ($request->kpimonth != 0)
            $kpiTerminated1->whereMonth('created_at', '=', $request->kpimonth);
        $kpiTerminated = $kpiTerminated1->get();


        $HLM = [];
        // foreach ($result as $key => $value) {
        //     $HLM[] = $value;
        // }


        return response()->json([
            "message" => "High Level Mgt", 'TotalMintari' => $totalmentari,
            'TotalAppoitment' => $list, 'User1' => $users,
            // 'TotalShharp' => $shharpcase,totalmentarilocation
            'totalpatient' => $totalpatient,
            'totalmentarilocation' => $totalmentarilocation,
            'totalsharp' => $shharpcase,
            'kpi' => $kpi,
            "kpiEmployement" => $kpiEmployement,
            "kpiUnemployement" => $kpiUnemployement,
            "kpiTerminated" => $kpiTerminated,

            "summaryActivity" => $summaryActivity,
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
    public function getNotification(Request $request)
    {
        $list = Notifications::select('*')
            ->where('added_by', '=', $request->added_by)
            ->where('isseen_staff', '=', '1')
            ->orderBy('id', 'DESC')
            ->get()->toArray();
        $count = count($list);
        // dd(count($list));
        $ab = [];
        if (count($list) > 0) {
            foreach ($list as $key => $value) {

                $datetime1 = new DateTime();
                $datetime12 = new DateTime($value['created_at']);

                if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
                    $ab[$key]['time']  = $datetime1->diff(new DateTime($value['created_at']))->format('%h hours %i minutes');
                } else {
                    $ab[$key]['time']  = $datetime1->diff(new DateTime($value['created_at']))->format('%a days %h hours %i minutes');
                }

                $ab[$key]['message']  = $value['message'];
                // dd($ab);

            }
        }
        // dd($ab);
        return response()->json(["message" => "Notifications List", 'list' => $ab, 'notification_count' => $count, "code" => 200]);
    }
}
