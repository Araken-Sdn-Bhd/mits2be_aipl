<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\PatientRegistration;
use App\Models\PatientAppointmentDetails;
use App\Models\Postcode;
use App\Models\State;
use App\Models\PatientShharpRegistrationRiskProtective;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShharpReportExport;
use App\Exports\WorkloadTotalPatienTypeRefferalReportExport;
use App\Exports\PatientActivityReportExport;
use App\Exports\VONActivityReportExport;
use App\Exports\GeneralReportExport;
use App\Exports\KPIReportExport;
use App\Models\ShharpReportGenerateHistory;
use App\Models\PatientAppointmentCategory;
use App\Models\PatientAppointmentVisit;
use App\Models\PatientCounsellorClerkingNotes;
use App\Models\PsychiatryClerkingNote;
use App\Models\PatientAppointmentType;
use App\Models\GeneralSetting;
use App\Models\IcdCode;
use App\Models\StaffManagement;
use App\Models\NetworkingContribution;
use App\Models\VonOrgRepresentativeBackground;
use App\Models\OutReachProjects;
use App\Models\Volunteerism;
use App\Models\HospitalBranchManagement;
use App\Models\SeProgressNote;
use App\Models\EtpProgressNote;
use App\Models\JobClubProgressNote;
use App\Models\CpsProgressNote;
use App\Models\ServiceRegister;
use App\Models\ListOfJobSearch;
use App\Models\LogMeetingWithEmployer;
use App\Models\JobStartForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\PatientCounsellingClerkingNote;
use App\Models\PsychiatricProgressNote;
use App\Models\PatientIndexForm;
use App\Models\CounsellingProgressNote;
use App\Models\ConsultationDischargeNote;
use App\Models\RehabDischargeNote;
use App\Models\CpsDischargeNote;
use App\Models\PatientCarePlan;
use App\Models\JobEndReport;
use App\Models\JobTransitionReport;
use App\Models\LaserAssesmentForm;
use App\Models\TriageForm;
use App\Models\JobInterestChecklist;
use App\Models\WorkAnalysisForm;
use App\Models\ListJobClub;
use App\Models\ListOfETP;
use App\Models\ListPreviousCurrentJob;
use App\Models\InternalReferralForm;
use App\Models\ExternalReferralForm;
use App\Models\CPSReferralForm;
use App\Models\Occt_Referral_Form;
use App\Models\PsychologyReferral;
use App\Models\RehabReferralAndClinicalForm;
use Carbon\Carbon;



class ReportController extends Controller
{
    public function getSharpReport(Request $request)
    {

        $age=[];
            $demo = [];
            if ($request->name) {
                $demo['name_asin_nric'] = $request->name;
            }
            if ($request->citizenship) {
                $demo['citizenship'] = $request->citizenship;
            }
            if ($request->Age) {
                $age = GeneralSetting::where('id', $request->Age)->first();
                $age['agemin']=$age['min_age'];
                $age['agemax']=$age['max_age'];
            }
            if ($request->gender) {
                $demo['sex'] = $request->gender;
            }
            if ($request->race) {
                $demo['race_id'] = $request->race;
            }
            if ($request->religion) {
                $demo['religion_id'] = $request->religion;
            }
            if ($request->marital_status) {
                $demo['marital_id'] = $request->marital_status;
            }
            if ($request->education_level) {
                $demo['education_level'] = $request->education_level;
            }
            if ($request->accommodation_id) {
                $demo['accomodation_id'] = $request->accommodation_id;

            }
            if ($request->occupation_status) {
                $demo['occupation_status'] = $request->occupation_status;
            }
            if ($request->fee_exemption_status) {
                $demo['fee_exemption_status'] = $request->fee_exemption_status;
            }
            if ($request->occupation_sector) {
                $demo['occupation_sector'] = $request->occupation_sector;
            }

            $users = DB::table('staff_management')
            ->select('roles.code')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->where('staff_management.email', '=', $request->email)
            ->first();
            $users2  = json_decode(json_encode($users), true);


                $query = DB::table('patient_shharp_registration_data_producer as psrdp')
                ->select('srfs.id','srfs.risk','srfs.protective','srfs.self_harm','srfs.patient_id',
                'p.name_asin_nric','p.address1','p.city_id','p.nric_no','p.state_id','p.postcode',
                'p.mobile_no','p.birth_date','srfs.harm_date','srfs.harm_time','psrdp.hospital_name',
                'psrhm.id as id2','psrhm.main_psychiatric_diagnosis','psrhm.additional_diagnosis')
                ->leftjoin('sharp_registraion_final_step as srfs', function($join) {
                    $join->on('srfs.id', '=', 'psrdp.shharp_register_id');
                })
                ->leftjoin('patient_registration as p', function($join) {
                    $join->on('srfs.patient_id', '=', 'p.id');
                })
                ->leftjoin('patient_shharp_registration_hospital_management as psrhm', function($join) {
                    $join->on('psrhm.id', '=', 'srfs.hospital_mgmt');
                })
                ->whereBetween('harm_date', [$request->fromDate, $request->toDate])
                ->where('srfs.hospital_mgmt', '!=','')
                ->where('srfs.status', '=','1');

                if($users2['code']!='superadmin'){
                    $query->where('p.branch_id','=',$request->branch_id);
                }

                if ($demo)
                $query->where($demo);

                if ($age){


                    if($age['agemin'] && $age['agemax']!=NULL){
                    $query->whereBetween('age',[$age['agemin'],$age['agemax']]);
                    }
                    else if($age['agemin']==NULL) {
                        $query->where('age','<=',$age['agemax']);
                    }else if($age['agemax']==NULL) {
                        $query->where('age','>=',$age['agemin']);
                    }
                }
                $run_query = $query->get()->toArray();
                $response  = json_decode(json_encode($run_query), true);

                $icd=NULL;
                $cd_array=[];
                $count=0;


                $index = 0;
                $result = [];

                foreach ($response as $k => $v) {
                //////////////////////////diagnosis/////////////////////////
            if (isset($request->report_type) && $request->report_type == 'excel') {
                if($v['main_psychiatric_diagnosis']!=NULL){
                    $main_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $v['main_psychiatric_diagnosis'])->first();
                    $result[$index]['MAIN_DIAGNOSIS'] = $main_diagnosis['icd_code'].' '.$main_diagnosis['icd_name'];

                        if($v['additional_diagnosis']!=NULL && !empty($v['additional_diagnosis'])){

                        $d=0;
                            foreach (explode(',',$v['additional_diagnosis']) as $add) {
                                $add_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $add)->first();
                                $additional_diagnosis[$d]['additional_diagnosis']=$add_diagnosis['icd_code'].' '.$add_diagnosis['icd_name'];
                                $d++;
                            }
                                $result[$index]['ADDITIONAL_DIAGNOSIS'] ='';

                            for($i=0; $i<$d; $i++){
                            $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'].'<br/>';
                            }

                        }else{

                            $result[$index]['ADDITIONAL_DIAGNOSIS'] = 'NA';
                        }

                }else if($v['additional_diagnosis']!=NULL){

                    $result[$index]['MAIN_DIAGNOSIS'] = 'NA';
                    $d=0;
                        foreach (explode(',',$v['additional_diagnosis']) as $add) {
                            $add_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $add)->first();
                            $additional_diagnosis[$d]['additional_diagnosis']=$add_diagnosis['icd_code'].' '.$add_diagnosis['icd_name'];
                            $d++;

                        }
                        $result[$index]['ADDITIONAL_DIAGNOSIS'] ='';

                        for($i=0; $i<$d; $i++){
                        $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'].'<br/>';
                        }

                }else{

                    $result[$index]['MAIN_DIAGNOSIS'] = 'NA';
                    $result[$index]['ADDITIONAL_DIAGNOSIS'] = 'NA';

                }

            }else{

                    if($v['main_psychiatric_diagnosis']!=NULL){
                        $main_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $v['main_psychiatric_diagnosis'])->first();
                        $result[$index]['MAIN_DIAGNOSIS'] = $main_diagnosis['icd_code'].' '.$main_diagnosis['icd_name'];

                            if($v['additional_diagnosis']!=NULL && !empty($v['additional_diagnosis'])){

                            $d=0;
                                foreach (explode(',',$v['additional_diagnosis']) as $add) {
                                    $add_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $add)->first();
                                    $additional_diagnosis[$d]['additional_diagnosis']=$add_diagnosis['icd_code'].' '.$add_diagnosis['icd_name'];
                                    $d++;
                                }
                                    $result[$index]['ADDITIONAL_DIAGNOSIS'] ='';
                                $d2=$d-1;
                                for($i=0; $i<$d; $i++){
                                    if($i==$d2){
                                        $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'];
                                    }else{
                                        $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'].', ';
                                    }
                                }

                            }else{

                                $result[$index]['ADDITIONAL_DIAGNOSIS'] = 'NA';
                            }

                    }else if($v['additional_diagnosis']!=NULL){

                        $result[$index]['MAIN_DIAGNOSIS'] = 'NA';
                        $d=0;
                            foreach (explode(',',$v['additional_diagnosis']) as $add) {
                                $add_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $add)->first();
                                $additional_diagnosis[$d]['additional_diagnosis']=$add_diagnosis['icd_code'].' '.$add_diagnosis['icd_name'];
                                $d++;

                            }
                            $result[$index]['ADDITIONAL_DIAGNOSIS'] ='';

                            for($i=0; $i<$d; $i++){
                                if($i==$d2){
                                    $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'];
                                }else{
                                    $result[$index]['ADDITIONAL_DIAGNOSIS'] .= $additional_diagnosis[$i]['additional_diagnosis'].', ';
                                }
                            }


                    }else{

                        $result[$index]['MAIN_DIAGNOSIS'] = 'NA';
                        $result[$index]['ADDITIONAL_DIAGNOSIS'] = 'NA';

                    }
                }
                //////////////////////////diagnosis/////////////////////////


                    if($request->protective_factor!=NULL){
                        $protective=$v['protective'];
                        $count=0;

                            foreach (explode('^',$protective) as $p) {
                                $protectives[$count]['protectives']=$p;
                                $count++;
                            }

                        if($request->protective_factor==13){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[0]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                        }
                        if($request->protective_factor==14){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[1]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                         }
                        if($request->protective_factor==15){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[2]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                        }
                        if($request->protective_factor==16){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[3]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                        }
                        if($request->protective_factor==17){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[4]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                        }
                        if($request->protective_factor==18){
                        $prpa=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$protectives[5]['protectives'])
                        ->where('Answer','=','Yes')
                        ->where('factor_type','=','protective')->first();
                        }
                        if(empty($prpa)){
                        continue;
                        }

                    }

                    if($request->risk_factor!=NULL){
                            $risk=$v['risk'];
                            $count=0;
                            foreach (explode('^',$risk) as $r) {
                                $risks[$count]['risks']=$r;
                                $count++;
                            }

                            if($request->risk_factor==1){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[0]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==2){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[1]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==3){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[2]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==4){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[3]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==5){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[4]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==6){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[5]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==7){
                                 $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[6]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==8){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[7]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                 }
                            if($request->risk_factor==9){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[8]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==10){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[9]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==11){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[10]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                            if($request->risk_factor==12){
                                $prpa2=PatientRiskProtectiveAnswer::select('Answer')
                                ->where('id','=',$risks[11]['risks'])
                                ->where('Answer','=','Yes')
                                ->where('factor_type','=','risk')->first();
                                }
                                if(empty($prpa2)){
                                    continue;
                                    }

                    }



                    $self_harm=$v['self_harm'];
                    $count=0;
                    foreach (explode('^',$self_harm) as $sh) {
                        $self_harms[$count]['self_harms']=$sh;
                        $query_protective[$count]=PatientRiskProtectiveAnswer::select('Answer')
                        ->where('id','=',$self_harms[$count]['self_harms'])->first();
                        $count++;
                    }
                    $ssh=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('id','=',$self_harms[1]['self_harms'])
                    ->where('section','=','Method of Self-Harm');

                    if($request->self_harm!=NULL){
                        if($request->self_harm=='Overdose/Poisoning'){
                            $sh=$ssh->where('section_value','LIKE','%Poisoning":true%');

                        }elseif($request->self_harm=='Hanging/Suffocation'){

                            $sh=$ssh->where('section_value','LIKE','%Suffocation":true%');

                        }elseif($request->self_harm=='Drowning'){
                            $sh=$ssh->where('section_value','LIKE','%"Drowning":true%');

                        }
                        elseif($request->self_harm=='Firearms or explosives'){
                            $sh=$ssh->where('section_value','LIKE','%"Firearms or explosives":true%');

                        }
                        elseif($request->self_harm=='Fire/flames'){
                            $sh=$ssh->where('section_value','LIKE','%flames":true%');

                        }
                        elseif($request->self_harm=='Cutting or Piercing'){
                            $sh=$ssh->where('section_value','LIKE','%"Cutting or Piercing":true%');

                        }
                        elseif($request->self_harm=='Jumping from height'){
                            $sh=$ssh->where('section_value','LIKE','%"Jumping from height":true%');

                        }
                        elseif($request->self_harm=='Other'){
                            $sh=$ssh->where('section_value','LIKE','%"Other":true%');

                        }
                    }


                    $sh=$ssh->first();

                    if(empty($sh)){
                    continue;
                    }

                    $method_self_harm1=str_contains($sh['section_value'],'"Overdose\/Poisoning":true');
                    $method_self_harm2=str_contains($sh['section_value'],'"Hanging\/Suffocation":true');
                    $method_self_harm3=str_contains($sh['section_value'],'"Drowning":true');
                    $method_self_harm4=str_contains($sh['section_value'],'"Firearms or explosives":true');
                    $method_self_harm5=str_contains($sh['section_value'],'"Fire\/flames":true');
                    $method_self_harm6=str_contains($sh['section_value'],'"Cutting or Piercing":true');
                    $method_self_harm7=str_contains($sh['section_value'],'"Jumping from height":true');
                    $method_self_harm8=str_contains($sh['section_value'],'"Other":true');

                    if($method_self_harm1==true){
                        $msh1['METHOD_OF_SELF_HARM'] = 'Overdose/Poisoning';
                    }else{
                        $msh1['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm2==true){
                        $msh2['METHOD_OF_SELF_HARM'] = 'Hanging/Suffocation';

                    }else{
                        $msh2['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm3==true){
                        $msh3['METHOD_OF_SELF_HARM'] = 'Drowning';

                    }else{
                        $msh3['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm4==true){
                        $msh4['METHOD_OF_SELF_HARM'] = 'Firearms or explosives';

                    }else{
                        $msh4['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm5==true){
                        $msh5['METHOD_OF_SELF_HARM'] = 'Fire/flames';
                    }else{
                        $msh5['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm6==true){
                        $msh6['METHOD_OF_SELF_HARM'] = 'Cutting or Piercing';
                    }else{
                        $msh6['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm7==true){
                        $msh7['METHOD_OF_SELF_HARM'] = 'Jumping from height';
                    }else{
                        $msh7['METHOD_OF_SELF_HARM']='';
                    }

                    if($method_self_harm8==true){
                        $msh8['METHOD_OF_SELF_HARM'] = 'Other';
                    }else{
                        $msh8['METHOD_OF_SELF_HARM']='';
                    }

                    $imm=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('id','=',$self_harms[2]['self_harms'])
                    ->where('section','=','How did Patient Get Idea about Method');

                    if($request->idea_about_method!=NULL){
                        if($request->idea_about_method=='Family, friends, peer group'){
                            $im=$imm->where('section_value','LIKE','%"Family, friends, peer group":true%');

                        }elseif($request->idea_about_method=='Internet (website, social media platform, app, blogs, forum, video/photosharing)'){

                            $im=$imm->where('section_value','LIKE','%photosharing)":true%');

                        }elseif($request->idea_about_method=='Printed media (newspaper, books, magazine, etc)'){
                            $im=$imm->where('section_value','LIKE','%"Printed media (newspaper, books, magazine, etc)":true%');

                        }elseif($request->idea_about_method=='Broadcast media (television, radio)'){
                            $im=$imm->where('section_value','LIKE','%"Broadcast media (television, radio)":true%');

                        }elseif($request->idea_about_method=='Specify patient actual words'){
                            $im=$imm->where('section_value','LIKE','%"Specify patient actual words":true%');

                        }
                    }
                    $im=$imm->first();

                    if(empty($im)){
                        continue;
                    }
                    $idea_method1=str_contains($im['section_value'],'"Family, friends, peer group":true');
                    $idea_method2=str_contains($im['section_value'],'"Internet (website, social media platform, app, blogs, forum, video\/photosharing)":true');
                    $idea_method3=str_contains($im['section_value'],'"Printed media (newspaper, books, magazine, etc)":true');
                    $idea_method4=str_contains($im['section_value'],'"Broadcast media (television, radio)":true');
                    $idea_method5=str_contains($im['section_value'],'"Specify patient actual words":true');

                    if($idea_method1==true){
                        $im1['IDEA_METHOD'] = 'Family, friends, peer group';
                    }else{
                        $im1['IDEA_METHOD']='';
                    }

                    if($idea_method2==true){
                        $im2['IDEA_METHOD'] = 'Internet (website, social media platform, app, blogs, forum, video/photosharing)';

                    }else{
                        $im2['IDEA_METHOD']='';
                    }

                    if($idea_method3==true){
                        $im3['IDEA_METHOD'] = 'Printed media (newspaper, books, magazine, etc)';

                    }else{
                        $im3['IDEA_METHOD']='';
                    }

                    if($idea_method4==true){
                        $im4['IDEA_METHOD'] = 'Broadcast media (television, radio)';

                    }else{
                        $im4['IDEA_METHOD']='';
                    }

                    if($idea_method5==true){
                        $im5['IDEA_METHOD'] = 'Specify patient actual words';
                    }else{
                        $im5['IDEA_METHOD']='';
                    }

                    $ssi=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('id','=',$self_harms[3]['self_harms'])
                    ->where('section','=','Suicidal Intent');

                    if($request->suicidal_intent!=NULL){
                        if($request->suicidal_intent=='Yes'){
                            $si=$ssi->where('section_value','LIKE','%"intent":"intent-yes%');

                        }elseif($request->suicidal_intent=='No'){

                            $si=$ssi->where('section_value','LIKE','%"intent":"no"%');

                        }elseif($request->suicidal_intent=='Undetermined'){
                            $si=$ssi->where('section_value','LIKE','%"intent":"Undetermined"%');

                        }

                    }

                    $si=$ssi->first();

                    if(empty($si)){
                    continue;
                    }

                    $suicidal_intent1=str_contains($si['section_value'],'"intent":"intent-yes');
                    $suicidal_intent2=str_contains($si['section_value'],'"intent":"Undetermined"');
                    $suicidal_intent3=str_contains($si['section_value'],'"intent":"no"');

                    if($suicidal_intent1==true){
                        $si1['SUCIDAL_INTENT'] = 'Yes';
                    }else{
                        $si1['SUCIDAL_INTENT']='';
                    }

                    if($suicidal_intent2==true){
                        $si2['SUCIDAL_INTENT'] = 'Undetermined';

                    }else{
                        $si2['SUCIDAL_INTENT']='';
                    }

                    if($suicidal_intent3==true){
                        $si3['SUCIDAL_INTENT'] = 'No';

                    }else{
                        $si3['SUCIDAL_INTENT']='';
                    }


///////////////////RiskAnswer////////////////////////////////////////////////////////////

                        $risk=$v['risk'];
                        $count=0;
                        foreach (explode('^',$risk) as $r) {
                             $risks[$count]['risks']=$r;
                        $count++;
                        }

                    $PatientRiskProtectiveAnswer1=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[0]['risks'])
                    ->where('factor_type','=','risk')->first();


                    if($PatientRiskProtectiveAnswer1['Answer']=='Yes'){
                        $prpa1['RISK_ANSWER'] = 'Presence of psychiatric disorder';
                    }else{
                        $prpa1['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer2=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[1]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer2['Answer']=='Yes'){
                        $prpa2['RISK_ANSWER'] = 'Hopelessness or despair';

                    }else{
                        $prpa2['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer3=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[2]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer3['Answer']=='Yes'){
                        $prpa3['RISK_ANSWER'] = 'Previous suicide attempts';
                    }else{
                        $prpa3['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer4=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[3]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer4['Answer']=='Yes'){
                        $prpa4['RISK_ANSWER'] = 'Presence of substance use/abuse';
                    }else{
                        $prpa4['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer5=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[4]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer5['Answer']=='Yes'){
                        $prpa5['RISK_ANSWER'] = 'Family history of suicidal behavior';
                    }else{
                        $prpa5['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer6=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[5]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer6['Answer']=='Yes'){
                        $prpa6['RISK_ANSWER'] = 'Family history of psychiatric disorders';
                    }else{
                        $prpa6['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer7=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[6]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer7['Answer']=='Yes'){
                        $prpa7['RISK_ANSWER'] = 'Family history of substance abuse';
                    }else{
                        $prpa7['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer8=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[7]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer8['Answer']=='Yes'){
                        $prpa8['RISK_ANSWER'] = 'Stressful life events or loss';
                    }else{
                        $prpa8['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer9=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[8]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer9['Answer']=='Yes'){
                        $prpa9['RISK_ANSWER'] = 'Isolation, rejection or feelings of shame';
                    }else{
                        $prpa9['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer10=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[9]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer10['Answer']=='Yes'){
                        $prpa10['RISK_ANSWER'] = 'chronic physical illness or condition';
                    }else{
                        $prpa10['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer11=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[10]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer11['Answer']=='Yes'){
                        $prpa11['RISK_ANSWER'] = 'History of physical , sexual or emotional abuse';
                    }else{
                        $prpa11['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer12=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$risks[11]['risks'])
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer12['Answer']=='Yes'){
                        $prpa12['RISK_ANSWER'] = 'Access to lethal methods/weapons';
                    }else{
                        $prpa12['RISK_ANSWER']='';
                    }

///////////////////ProtectiveFactorAnswer////////////////////////////////////////////////////////////

                    $protective=$v['protective'];
                    $count=0;
                    foreach (explode('^',$protective) as $p) {
                         $protectives[$count]['protectives']=$p;
                    $count++;
                    }

                    $PatientRiskProtectiveAnswer13=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[0]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer13['Answer']=='Yes'){
                        $prpa13['PROTECTIVE_FACTORS'] = 'Ability to cope with stress/tolerate frustrations';
                    }else{
                        $prpa13['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer14=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[1]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer14['Answer']=='Yes'){
                        $prpa14['PROTECTIVE_FACTORS'] = 'Strongy held religious/cultural beliefs';
                    }else{
                        $prpa14['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer15=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[2]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer15['Answer']=='Yes'){
                        $prpa15['PROTECTIVE_FACTORS'] = 'Realistic life goals or future plans';
                    }else{
                        $prpa15['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer16=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[3]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer16['Answer']=='Yes'){
                        $prpa16['PROTECTIVE_FACTORS'] = 'Responsibility to children/beloved pets';
                    }else{
                        $prpa16['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer17=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[4]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer17['Answer']=='Yes'){
                        $prpa17['PROTECTIVE_FACTORS'] = 'Social support';
                    }else{
                        $prpa17['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer18=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('id','=',$protectives[5]['protectives'])
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer18['Answer']=='Yes'){
                        $prpa18['PROTECTIVE_FACTORS'] = 'Positive therapeutic relationship';
                    }else{
                        $prpa18['PROTECTIVE_FACTORS']='';
                    }




                //////////////////////City///////////////////////
                $city=Postcode::select('city_name')
                ->where('id','=',$v['city_id'])->first();
                 if($city==NULL){
                    // dd($v['patient_id']);
                    $result[$index]['CITY'] = 'NA';
                 }else{
                    $result[$index]['CITY'] = $city['city_name'];
                 }  //For testing purpose
                //////////////////////State///////////////////////
                $state=State::select('state_name')
                ->where('id','=',$v['state_id'])->first();
                if($state==NULL){
                    $result[$index]['STATE'] = 'NA';
                 }else{
                    $result[$index]['STATE'] = $state['state_name'];
                 }
                //////////////////////State///////////////////////
                $postcode=Postcode::select('postcode')
                ->where('id','=',$v['postcode'])->first();
                if($postcode==NULL){
                    $result[$index]['POSTCODE'] = 'NA';
                 }else{
                    $result[$index]['POSTCODE'] = $postcode['postcode'];
                 }
                 if($v['address1']==NULL){
                    $result[$index]['ADDRESS'] = 'NA';
                 }else{
                    $result[$index]['ADDRESS'] = $v['address1'];
                 }

                    $result[$index]['NO'] = $index+1;
                    $result[$index]['HOSPITAL'] = $v['hospital_name'];
                    $result[$index]['DATE'] = $v['harm_date'];
                    $result[$index]['TIME'] = $v['harm_time'];
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $v['nric_no'];
                    $result[$index]['NAME'] = $v['name_asin_nric'];
                    $result[$index]['PHONE_NUMBER'] = $v['mobile_no'];
                    $result[$index]['DATE_OF_BIRTH'] = $v['birth_date'];

                    $result[$index]['RISK_FACTOR1'] = $prpa1['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR2'] = $prpa2['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR3'] = $prpa3['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR4'] = $prpa4['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR5'] = $prpa5['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR6'] = $prpa6['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR7'] = $prpa7['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR8'] = $prpa8['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR9'] = $prpa9['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR10'] = $prpa10['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR11'] = $prpa11['RISK_ANSWER'];
                    $result[$index]['RISK_FACTOR12'] = $prpa12['RISK_ANSWER'];



                    $result[$index]['PROTECTIVE_FACTOR13'] =  $prpa13['PROTECTIVE_FACTORS'];
                    $result[$index]['PROTECTIVE_FACTOR14'] =  $prpa14['PROTECTIVE_FACTORS'];
                    $result[$index]['PROTECTIVE_FACTOR15'] =  $prpa15['PROTECTIVE_FACTORS'];
                    $result[$index]['PROTECTIVE_FACTOR16'] =  $prpa16['PROTECTIVE_FACTORS'];
                    $result[$index]['PROTECTIVE_FACTOR17'] =  $prpa17['PROTECTIVE_FACTORS'];
                    $result[$index]['PROTECTIVE_FACTOR18'] =  $prpa18['PROTECTIVE_FACTORS'];


                    $result[$index]['METHOD_OF_SELF_HARM1'] = $msh1['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM2'] = $msh2['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM3'] = $msh3['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM4'] = $msh4['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM5'] = $msh5['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM6'] = $msh6['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM7'] = $msh7['METHOD_OF_SELF_HARM'];
                    $result[$index]['METHOD_OF_SELF_HARM8'] = $msh8['METHOD_OF_SELF_HARM'];


                    $result[$index]['IDEA_OF_METHOD1'] = $im1['IDEA_METHOD'];
                    $result[$index]['IDEA_OF_METHOD2'] = $im2['IDEA_METHOD'];
                    $result[$index]['IDEA_OF_METHOD3'] = $im3['IDEA_METHOD'];
                    $result[$index]['IDEA_OF_METHOD4'] = $im4['IDEA_METHOD'];
                    $result[$index]['IDEA_OF_METHOD5'] = $im5['IDEA_METHOD'];



                    $result[$index]['SUCIDAL_INTENT1'] = $si1['SUCIDAL_INTENT'];
                    $result[$index]['SUCIDAL_INTENT2'] = $si2['SUCIDAL_INTENT'];
                    $result[$index]['SUCIDAL_INTENT3'] = $si3['SUCIDAL_INTENT'];

////////////////////For Excel//////////////////////////////////////////////

                    $result[$index]['RISK_FACTOR'] =    $prpa1['RISK_ANSWER'].'<br>'.$prpa2['RISK_ANSWER'].'<br>'.
                                                        $prpa3['RISK_ANSWER'].'<br>'.$prpa4['RISK_ANSWER'].'<br>'.
                                                        $prpa5['RISK_ANSWER'].'<br>'.$prpa6['RISK_ANSWER'].'<br>'.
                                                        $prpa7['RISK_ANSWER'].'<br>'.$prpa8['RISK_ANSWER'].'<br>'.
                                                        $prpa9['RISK_ANSWER'].'<br>'.$prpa10['RISK_ANSWER'].'<br>'.
                                                        $prpa11['RISK_ANSWER'].'<br>'.$prpa12['RISK_ANSWER'];


                    $result[$index]['PROTECTIVE_FACTOR'] =  $prpa13['PROTECTIVE_FACTORS'].'<br>'.$prpa14['PROTECTIVE_FACTORS'].'<br>'.
                                                            $prpa15['PROTECTIVE_FACTORS'].'<br>'.$prpa16['PROTECTIVE_FACTORS'].'<br>'.
                                                            $prpa17['PROTECTIVE_FACTORS'].'<br>'.$prpa18['PROTECTIVE_FACTORS'];

                    $result[$index]['METHOD_OF_SELF_HARM'] = $msh1['METHOD_OF_SELF_HARM'].'<br>'.$msh2['METHOD_OF_SELF_HARM'].'<br>'.
                                                             $msh3['METHOD_OF_SELF_HARM'].'<br>'.$msh4['METHOD_OF_SELF_HARM'].'<br>'.
                                                             $msh5['METHOD_OF_SELF_HARM'].'<br>'.$msh6['METHOD_OF_SELF_HARM'].'<br>'.
                                                             $msh7['METHOD_OF_SELF_HARM'].'<br>'.$msh8['METHOD_OF_SELF_HARM'];

                    $result[$index]['IDEA_OF_METHOD'] = $im1['IDEA_METHOD'].'<br>'.$im2['IDEA_METHOD'].'<br>'.
                                                        $im3['IDEA_METHOD'].'<br>'.$im4['IDEA_METHOD'].'<br>'.
                                                        $im5['IDEA_METHOD'];

                    $result[$index]['SUCIDAL_INTENT'] = $si1['SUCIDAL_INTENT'].'<br>'.$si2['SUCIDAL_INTENT'].'<br>'.
                                                        $si3['SUCIDAL_INTENT'];




                    $index++;
                    $totalReports =  $index;






            }
                    if ($result) {


                    if (isset($request->report_type) && $request->report_type == 'excel') {
                        $filename = 'ShharpReport-'.time().'.xls';

                        $totalReports= $index;

                        return response([
                            'message' => 'Data successfully retrieved.',
                            'result' => $result,
                            'header' => 'Shharp Report from '.$request->fromDate.' To '.$request->toDate.'<br>'.
                                        'Total Reports = '.$totalReports,

                            'filename' => $filename,
                            'code' => 200
                        ]);

                    } else {

                            $periodofservices= $request->fromDate.' To '.$request->toDate;
                            return response()->json(["message" => "Shharp Report", 'result' => $result,'periodofservices' => $periodofservices,
                             'TotalReport'=>$totalReports, "code" => 200]);
                    }
            } else {
                return response()->json(["message" => "Shharp Report", 'result' => [], 'filepath' => null, "code" => 200]);
            }
        }




    public function getTotalPatientTypeRefferalReport(Request $request)
    {
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate])
        ->where('status','=',1);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);

        $ssh = $appointments->get()->toArray();
        $result = [];
        $cpa = [];
        $vta = [];
        $rfa = ['Walk-In' => 0, 'Refferal' => 0];
        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);

                    $users = DB::table('staff_management')
                    ->select('roles.code')
                    ->join('roles', 'staff_management.role_id', '=', 'roles.id')
                    ->where('staff_management.email', '=', $request->email)
                    ->first();
                    $users2  = json_decode(json_encode($users), true);

                    if($users2['code']!='superadmin'){
                        $query->where('branch_id','=',$request->branch_id);
                    }

                $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = Postcode::where(['id' => $patientInfo['postcode']])->get()->toArray();
                    $st = State::where(['id' => $patientInfo['state_id']])->get()->toArray();
                    $vt = GeneralSetting::where('id', $v['type_visit'])->get()->toArray();
                    $cp = GeneralSetting::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    $city_name = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $state_name = ($st) ? $st[0]['state_name'] : 'NA';
                    $postcode = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['section_value'] : 'NA';
                    $category = ($cp) ? $cp[0]['section_value'] : 'NA';
                    if (array_key_exists($cp[0]['section_value'], $cpa)) {
                        $cpa[$cp[0]['section_value']] = $cpa[$cp[0]['section_value']] + 1;
                    } else {
                        $cpa[$cp[0]['section_value']] = 1;
                    }
                    if (array_key_exists($vt[0]['section_value'], $vta)) {
                        $vta[$vt[0]['section_value']] = $vta[$vt[0]['section_value']] + 1;
                    } else {
                        $vta[$vt[0]['section_value']] = 1;
                    }

                    if (in_array($request->referral_type, [7, 253])) {
                        $rfa['Walk-In'] = $rfa['Walk-In'] + 1;
                    } else {
                        $rfa['Refferal'] = $rfa['Refferal'] + 1;
                    }
                    $result[$index]['No']=$index+1;
                    $result[$index]['DATE'] = date('d/m/Y', strtotime($v['booking_date']));
                    $result[$index]['TIME'] = date('h:i:s A', strtotime($v['booking_time']));
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = ($patientInfo['nric_no']) ? $patientInfo['nric_no'] : $patientInfo['passport_no'];;
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['ADDRESS'] = $patientInfo['address1'] . ' ' . $patientInfo['address2'] . ' ' . $patientInfo['address3'];
                    $result[$index]['CITY'] = $city_name;
                    $result[$index]['STATE'] = $state_name;
                    $result[$index]['POSTCODE'] = $postcode;
                    $result[$index]['PHONE_NUMBER'] = $patientInfo['mobile_no'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo['birth_date'];
                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $index++;
                }
            }
        }
        if ($result) {
            $totalPatients = count($result);
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = $diff->format("%a");
            $patientCategories = $cpa;

            $visitTypes = $vta;

            foreach ($visitTypes as $k => $v) {
                $visitTypes[str_replace(' ', '_', $k)] = $v;
            }

            $refferals = $rfa;

            foreach ($refferals as $k => $v) {
                $refferals[str_replace('-', '_', $k)] = $v;
            }

            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'TotalPatient&TypeOfReferralReport-'.time().'.xls';


                $summary= 'TOTAL PATIENT AND TYPE OF REFERRAL'.'<br>'.'TOTAL DAYS:   '.$totalDays.'<br>'.'TOTAL PATIENT:    '.$totalPatients.'<br>';

                return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'header' => $summary,
                    'filename' => $filename,
                    'code' => 200]);

            } else {
                return response()->json([
                    "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => '', 'Total_Patient' => $totalPatients, 'Total_Days' => $totalDays,
                    'Referal_walk' => $rfa, 'Visit_Type' => $visitTypes, 'refferals' =>  $refferals, 'Category_Patient' => $patientCategories, "code" => 200
                ]);
            }
        } else {
            return response()->json(["message" => "Toal Patient & Type of Refferal Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getPatientActivityReport(Request $request)
    {
        if ($request->appointment_type) {
            $demo['appointment_type'] = $request->appointment_type;
        }

        if ($request->patient_category) {
            $demo['patient_category'] = $request->patient_category;
        }
        if ($request->referral_type) {
            $demo['referral_type'] = $request->referral_type;
        }
        if ($request->type_visit ) {
            $demo['type_visit'] = $request->type_visit ;
        }
        if ($request->gender) {
            $demo['sex'] = $request->gender;
        }
            $query = DB::table('patient_appointment_details as pad')
            ->select('*')
            ->join('patient_registration as p', function($join) {
                $join->on('pad.patient_mrn_id', '=', 'p.id');
            })
            ->whereBetween('booking_date', [$request->fromDate, $request->toDate]);

            $users = DB::table('staff_management')
                ->select('roles.code')
                ->join('roles', 'staff_management.role_id', '=', 'roles.id')
                ->where('staff_management.email', '=', $request->email)
                ->first();
            $users2  = json_decode(json_encode($users), true);

                if($users2['code']!='superadmin'){
                    $query->where('branch_id','=',$request->branch_id);
                }
            if ($demo)
            $query->where($demo);

            if ($request->appointment_status!=NULL) {
                if($request->appointment_status==1){
                    $query->where('appointment_status','!=', 2);
                }else{
                    $query->where('appointment_status','=', 2);

                }

            }

       $response = $query->get()->toArray();
        $ssh  = json_decode(json_encode($response), true);
        $apcount = [];
        $result = [];
        $attendanceStatus = [];
        $attend = 0;
        $noShow = 0;
        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                if (array_key_exists($v['patient_mrn_id'], $apcount)) {
                    $apcount[$v['patient_mrn_id']] = $apcount[$v['patient_mrn_id']] + 1;
                } else {
                    $apcount[$v['patient_mrn_id']] = 1;
                }
                $notes = [];
                $job_search = [];
                $job_visit = [];
                $jv= 0;
                $log_meeting = [];
                $empStatus = [];
                $jobStart = [];
                $js = [];
                $curr_interv = [];
                $cd_array=[];
                $icd=NULL;
                $count=0;

                if($request->appointment_type == 1){


                        $query_diagnosis1 = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                            if($diagnosis1!=NULL){
                                $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis1_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis_id'];
                                $cd_array[$count]['procedure']=$diagnosis1['category_services'];
                                $cd_array[$count]['medication']=$diagnosis1['medication_des'];
                                $count++;
                            }

                        $query_diagnosis2 = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                                $diagnosis2=$query_diagnosis2->orderBy('id', 'DESC')->first();

                            if($diagnosis2!=NULL){
                                    $diagnosis2_ts=strtotime($diagnosis2['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis2_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis2['diagnosis_id'];
                                    $cd_array[$count]['procedure']=$diagnosis2['category_services'];
                                    $cd_array[$count]['medication']=$diagnosis2['medication_des'];
                                    $count++;
                               }

                            $query_diagnosis3 = PsychiatricProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                            ->where('status','=','1');
                                    $diagnosis3=$query_diagnosis3->orderBy('id', 'DESC')->first();

                                if($diagnosis3!=NULL){
                                        $diagnosis3_ts=strtotime($diagnosis3['updated_at']);
                                        $cd_array[$count]['updated_at']=$diagnosis3_ts;
                                        $cd_array[$count]['diagnosis_id']=$diagnosis3['diagnosis'];
                                        $cd_array[$count]['procedure']=$diagnosis3['category_services'];
                                        $cd_array[$count]['medication']=$diagnosis3['medication_des'];
                                        $count++;
                                }

                                $query_diagnosis4 = PatientIndexForm::where('patient_mrn_id', $v['patient_mrn_id'])
                                ->where('status','=','1');

                                    $diagnosis4=$query_diagnosis4->orderBy('id', 'DESC')->first();

                                        if($diagnosis4!=NULL){
                                            $diagnosis4_ts=strtotime($diagnosis4['updated_at']);
                                            $cd_array[$count]['updated_at']=$diagnosis4_ts;
                                            $cd_array[$count]['diagnosis_id']=$diagnosis4['diagnosis'];
                                            $cd_array[$count]['procedure']=$diagnosis4['category_services'];
                                            $cd_array[$count]['medication']=$diagnosis4['medication_des'];
                                            $count++;
                                        }

                $query_diagnosis5 = CounsellingProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                ->where('status','=','1');
                    $diagnosis5=$query_diagnosis5->orderBy('id', 'DESC')->first();

                    if($diagnosis5!=NULL){
                        $diagnosis5_ts=strtotime($diagnosis5['updated_at']);
                        $cd_array[$count]['updated_at']=$diagnosis5_ts;
                        $cd_array[$count]['diagnosis_id']=$diagnosis5['diagnosis_id'];
                        $cd_array[$count]['procedure']=$diagnosis5['category_services'];
                        $cd_array[$count]['medication']=$diagnosis5['medication_des'];
                        $count++;
                    }


                    $query_diagnosis6 = ConsultationDischargeNote::where('patient_id', $v['patient_mrn_id'])
                    ->where('status','=','1');

                            $diagnosis6=$query_diagnosis6->orderBy('id', 'DESC')->first();

                        if($diagnosis6!=NULL){
                            $diagnosis6_ts=strtotime($diagnosis6['updated_at']);
                            $cd_array[$count]['updated_at']=$diagnosis6_ts;
                            $cd_array[$count]['diagnosis_id']=$diagnosis6['diagnosis_id'];
                            $cd_array[$count]['procedure']=$diagnosis6['category_services'];
                            $cd_array[$count]['medication']=$diagnosis6['medication_des'];
                            $count++;
                        }


            if(!empty($cd_array)){

                    $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                    $array_date=max($Dates);


                    foreach ($cd_array as $c => $d){

                        if($array_date==$d['updated_at']){
                            $icd=$d['diagnosis_id'];
                            $procedure=$d['procedure'];
                            if($procedure==NULL){
                                $procedure='NA';
                            }
                            $medication=$d['medication'];
                            if($medication==NULL){
                                $medication='NA';
                            }
                        }
                    }
                    if($icd!=NULL){
                        $icd_query = IcdCode::where('id', $icd)->first();
                        $icd_name=$icd_query['icd_name'];
                    }else{
                        $icd_name='NA';
                    }

            }else{
                $icd_name = 'NA';
                $procedure='NA';
                $medication='NA';
            }

            if($request->diagnosis_id!=NULL){
                if($icd!=$request->diagnosis_id){
                    continue;
                }
            }


                }elseif($request->appointment_type == 3){

                    $query_diagnosis1 = SeProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
                        $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                            if($diagnosis1!=NULL){
                                $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis1_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis_type'];
                                $cd_array[$count]['procedure']=$diagnosis1['service_category'];
                                $cd_array[$count]['medication']=$diagnosis1['medication'];
                                if($diagnosis1['restart']==!NULL){
                                    $restart=$diagnosis1['restart'];
                                }else{
                                    $restart='NA';
                                }
                                $count++;
                            }else{
                                $restart='NA';
                            }

                        $query_diagnosis2 = PatientIndexForm::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis2=$query_diagnosis2->orderBy('id', 'DESC')->first();

                                if($diagnosis2!=NULL){
                                    $diagnosis2_ts=strtotime($diagnosis2['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis2_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis2['diagnosis'];
                                    $cd_array[$count]['procedure']=$diagnosis2['category_of_services'];
                                    $cd_array[$count]['medication']=$diagnosis2['medication'];
                                    $count++;
                                }

                        $query_diagnosis3 = RehabDischargeNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis3=$query_diagnosis3->orderBy('id', 'DESC')->first();

                                if($diagnosis3!=NULL){
                                    $diagnosis3_ts=strtotime($diagnosis3['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis3_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis3['diagnosis_id'];
                                    $cd_array[$count]['procedure']=$diagnosis3['service_category'];
                                    $cd_array[$count]['medication']=$diagnosis3['medication'];
                                    $count++;
                                }

                                if($request->diagnosis_id){
                                    if($diagnosis1==NULL && $diagnosis2==NULL && $diagnosis3==NULL){
                                        continue;
                                    }
                                 }

                if(!empty($cd_array)){

                    $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                    $array_date=max($Dates);


                    foreach ($cd_array as $c => $d){

                        if($array_date==$d['updated_at']){
                            $icd=$d['diagnosis_id'];
                            $procedure=$d['procedure'];
                            if($procedure==NULL){
                                $procedure='NA';
                            }
                            $medication=$d['medication'];
                            if($medication==NULL){
                                $medication='NA';
                            }

                        }
                    }
                    if($icd!=NULL){
                        $icd_query = IcdCode::where('id', $icd)->first();
                        $icd_name=$icd_query['icd_name'];
                    }else{
                        $icd_name='NA';
                    }

                }else{
                    $icd_name = 'NA';
                    $procedure='NA';
                    $restart='NA';
                    $medication='NA';
                }
                if($request->diagnosis_id!=NULL){
                    if($icd!=$request->diagnosis_id){
                        continue;
                    }
                }


                    if($request->employment_status){
                        $employment_status = SeProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('employment_status',$request->employment_status)
                        ->where('status', '1')
                        ->get()->toArray();

                        if(empty($employment_status)){
                            continue;
                        }
                    }
                    if($request->case_manager){
                        $case_manager = JobStartForm::where('patient_id', $v['patient_mrn_id'])
                        ->where('is_deleted', 0)
                        ->where('status', '1')
                        ->where('case_manager',$request->case_manager)
                        ->get()->toArray();
                        if(empty($case_manager)){
                            continue;
                        }
                    }
                    if($request->employer_list){
                        $employer_list = JobStartForm::where('patient_id', $v['patient_mrn_id'])
                        ->where('is_deleted', 0)
                        ->where('status', '1')
                        ->where('name_of_employer',$request->employer_list)
                        ->get()->toArray();
                        if(empty($employer_list)){
                            continue;
                        }
                    }



                    $jobStart = JobStartForm::where('patient_id', $v['patient_mrn_id'])
                                            ->where('status', '1')
                                            ->where('is_deleted', 0)
                                            ->get()->toArray();

                    if($diagnosis1 != NULL){
                        $job_visit = GeneralSetting::where('id', $diagnosis1['activity_type'])->get()->toArray();
                        if($job_visit[0]['section_value'] == 'FASC (Follow Along Support For Client)' || $job_visit[0]['section_value'] == 'FASE (Follow Along Support For Employer)')
                        {
                            $jv += 1;
                        }
                        $empStatus = GeneralSetting::where('id', $diagnosis1['employment_status'])->get()->toArray();
                    }
                        $log_meeting = LogMeetingWithEmployer::where(['patient_id' => $v['patient_mrn_id']])->where('status','=','1')->get()->toArray();
                    if($log_meeting){
                        $jv = $jv + count($log_meeting);
                    }
                }
                elseif($request->appointment_type == 4){


                     $query_diagnosis1 = PatientIndexForm::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                                if($diagnosis1!=NULL){
                                    $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis1_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis'];
                                    $cd_array[$count]['procedure']=$diagnosis1['category_of_services'];
                                    $cd_array[$count]['medication']=$diagnosis1['medication'];
                                    $count++;
                                }

                    $query_diagnosis2 = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
                            $diagnosis2=$query_diagnosis2->orderBy('id', 'DESC')->first();

                        if($diagnosis2!=NULL){
                                $diagnosis2_ts=strtotime($diagnosis2['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis2_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis2['diagnosis_type'];
                                $cd_array[$count]['procedure']=$diagnosis2['service_category'];
                                $cd_array[$count]['medication']=$diagnosis2['medication'];

                                $count++;
                        }

                        $query_diagnosis3 = RehabDischargeNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis3=$query_diagnosis3->orderBy('id', 'DESC')->first();

                                if($diagnosis3!=NULL){
                                    $diagnosis3_ts=strtotime($diagnosis3['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis3_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis3['diagnosis_id'];
                                    $cd_array[$count]['procedure']=$diagnosis3['service_category'];
                                    $cd_array[$count]['medication']=$diagnosis3['medication'];
                                    $count++;
                                }
                                if($request->diagnosis_id){
                                    if($diagnosis1==NULL && $diagnosis2==NULL && $diagnosis3==NULL){
                                        continue;
                                    }
                                 }

                if(!empty($cd_array)){

                                $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                                $array_date=max($Dates);


                    foreach ($cd_array as $c => $d){

                        if($array_date==$d['updated_at']){
                            $icd=$d['diagnosis_id'];
                            $procedure=$d['procedure'];
                            if($procedure==NULL){
                                $procedure='NA';
                            }
                            $medication=$d['medication'];
                            if($medication==NULL){
                                $medication='NA';
                            }
                        }
                    }

                    if($icd!=NULL){
                        $icd_query = IcdCode::where('id', $icd)->first();
                        $icd_name=$icd_query['icd_name'];
                    }else{
                        $icd_name='NA';
                    }

                }else{
                    $icd_name = 'NA';
                    $procedure='NA';
                    $medication='NA';
                }

                if($request->diagnosis_id!=NULL){
                    if($icd!=$request->diagnosis_id){
                        continue;
                    }
                }

                if($request->work_readiness){
                        $work_readiness = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                        ->where('work_readiness',$request->work_readiness)
                        ->where('status', '1')
                        ->where('is_deleted', 0)
                        ->get()->toArray();
                        if(empty($work_readiness)){
                            continue;
                        }
                    }
                    if($request->case_manager){
                        $staff_name = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                        ->where('status', '1')
                        ->where('is_deleted', 0)
                        ->first();
                        $case_manager = StaffManagement::where('name', $staff_name['staff_name'])
                        ->where('status', '1')
                        ->get()->toArray();
                        if(empty($case_manager)){
                            continue;
                        }
                    }
                    $etp = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                    ->get()->toArray();




                }elseif($request->appointment_type == 5){


                        $query_diagnosis1 = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                                $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                            if($diagnosis1!=NULL){
                                    $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis1_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis_type'];
                                    $cd_array[$count]['procedure']=$diagnosis1['service_category'];
                                    $cd_array[$count]['medication']=$diagnosis1['medication'];
                                    $count++;
                            }
                            if($request->diagnosis_id){
                                if($diagnosis1==NULL){
                                    continue;
                                }
                             }

            if(!empty($cd_array)){

                    $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                    $array_date=max($Dates);


                        foreach ($cd_array as $c => $d){

                            if($array_date==$d['updated_at']){
                                $icd=$d['diagnosis_id'];
                                $procedure=$d['procedure'];
                                if($procedure==NULL){
                                    $procedure='NA';
                                }
                                $medication=$d['medication'];
                                if($medication==NULL){
                                    $medication='NA';
                                }
                            }
                        }
                        if($icd!=NULL){
                            $icd_query = IcdCode::where('id', $icd)->first();
                            $icd_name=$icd_query['icd_name'];
                        }else{
                            $icd_name='NA';
                        }

            }else{
                $icd_name = 'NA';
                $procedure='NA';
                $medication='NA';
            }

            if($request->diagnosis_id!=NULL){
                if($icd!=$request->diagnosis_id){
                    continue;
                }
            }



                    if($request->work_readiness){
                        $work_readiness = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                        ->where('work_readiness',$request->work_readiness)
                        ->where('status', 1)
                        ->where('is_deleted', 0)
                        ->get()->toArray();
                        if(empty($employment_status)){
                            continue;
                        }
                    }
                    if($request->case_manager){
                        $staff_name = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                        ->where('status', 1)
                        ->where('is_deleted', 0)
                        ->first();
                        $case_manager = StaffManagement::where('name', $staff_name['staff_name'])
                        ->where('status', 1)
                        ->get()->toArray();
                        if(empty($case_manager)){
                            continue;
                        }
                    }
                    $job_club = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                    ->get()->toArray();

                }elseif($request->appointment_type == 6){


                    $query_diagnosis1 = PatientIndexForm::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
                        $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                            if($diagnosis1!=NULL){
                                $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis1_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis'];
                                $cd_array[$count]['procedure']=$diagnosis1['category_of_services'];
                                $cd_array[$count]['medication']=$diagnosis1['medication'];
                                $count++;
                            }

                        $query_diagnosis2 = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis2=$query_diagnosis2->orderBy('id', 'DESC')->first();

                                if($diagnosis2!=NULL){
                                    $diagnosis2_ts=strtotime($diagnosis2['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis2_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis2['diagnosis_id'];
                                    $cd_array[$count]['procedure']=$diagnosis2['service_category'];
                                    $cd_array[$count]['medication']=$diagnosis2['medication'];
                                    $count++;
                                }

                        $query_diagnosis3 = CpsDischargeNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
                            $diagnosis3=$query_diagnosis3->orderBy('id', 'DESC')->first();

                                if($diagnosis3!=NULL){
                                    $diagnosis3_ts=strtotime($diagnosis3['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis3_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis3['diagnosis'];
                                    $cd_array[$count]['procedure']=$diagnosis3['service_category'];
                                    $cd_array[$count]['medication']=$diagnosis3['medication'];
                                    $count++;
                                }

                             if(!empty($cd_array)){

                            $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                            $array_date=max($Dates);


                    foreach ($cd_array as $c => $d){

                        if($array_date==$d['updated_at']){
                            $icd=$d['diagnosis_id'];
                            $procedure=$d['procedure'];
                            if($procedure==NULL){
                                $procedure='NA';
                            }
                            $medication=$d['medication'];
                            if($medication==NULL){
                                $medication='NA';
                            }
                        }
                    }
                    if($icd!=NULL){
                        $icd_query = IcdCode::where('id', $icd)->first();
                        $icd_name=$icd_query['icd_name'];
                    }else{
                        $icd_name='NA';
                    }


            }else{
                $icd_name = 'NA';
                $procedure='NA';
                $medication='NA';
            }

            if($request->diagnosis_id!=NULL){
                if($icd!=$request->diagnosis_id){
                    continue;
                }
            }


                    if($request->current_intervention){
                        $current_intervention = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                        ->where('current_intervention',$request->current_intervention)
                        ->where('status', 1)
                        ->where('is_deleted', 0)
                        ->get()->toArray();
                        if(empty($employment_status)){
                            continue;
                        }
                    }
                    if($request->case_manager){
                        $case_manager_name = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status', 1)
                        ->where('is_deleted', 0)
                        ->first();
                        $case_manager = StaffManagement::where('id', $case_manager_name['case_manager'])
                        ->where('status', 1)
                        ->get()->toArray();
                        if(empty($case_manager)){
                            continue;
                        }
                    }

                    $cps = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])->where('status','=','1')
                    ->get()->toArray();
                    if(count($cps) != 0){
                        $curr_interv = GeneralSetting::where('id', $cps[0]['current_intervention'])->get()->toArray();

                    }
                }

                $job_search = ListOfJobSearch::where('id', $v['patient_mrn_id'])->where('status','=','1')
                    ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                    ->get()->toArray();

                $staff = StaffManagement::select('name')->where('id', $v['staff_id'])->get()->toArray();

                    $pc = GeneralSetting::where(['id' => $v['sex']])->get()->toArray();
                    $st = ServiceRegister::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = GeneralSetting::where('id', $v['type_visit'])->get()->toArray();
                    $cp = GeneralSetting::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $v['referral_type']])->get()->toArray();


                    // if ($notes)
                    //     $icd = IcdCode::where('id', $notes[0]['code_id'])->get()->toArray();
                        $nap = PatientAppointmentDetails::where('patient_mrn_id', $v['patient_mrn_id'])
                            ->where('booking_date', '>', $v['booking_date'])->where('appointment_status', 0)->first();
                    $nxtAppointments = ($nap) ? $nap->toArray() : [];
                    $gender = ($pc) ? $pc[0]['section_value'] : 'NA';
                    $appointment_type = ($st) ? $st[0]['service_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['section_value'] : 'NA';
                    $category = ($cp) ? $cp[0]['section_value'] : 'NA';
                    $result[$index]['No']=$index+1;
                    $result[$index]['Next_visit'] = ($nxtAppointments) ? date('d/m/Y', strtotime($nxtAppointments['booking_date'])) : '-';
                    $result[$index]['time_registered'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['time_seen'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['Procedure'] = $procedure;
                    $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                    $result[$index]['Name'] = $v['name_asin_nric'];
                    $result[$index]['Attending_staff'] = ($staff) ? $staff[0]['name'] : 'NA';
                    $result[$index]['IC_NO'] = $v['nric_or_passportno'];
                    $result[$index]['GENDER'] = $gender;
                    $result[$index]['APPOINTMENT_TYPE'] = $appointment_type;
                    $result[$index]['AGE'] = $v['age'];
                    $result[$index]['DIAGNOSIS'] = $icd_name;
                    if($request->appointment_type == 3){
                        $result[$index]['MEDICATIONS'] = $medication;
                        $result[$index]['RESTART']=$restart;
                        $result[$index]['EMPSTATUS'] = ($empStatus) ? $empStatus[0]['section_value'] : "NA";
                        $result[$index]['EMPLOYER'] = ($jobStart) ? $jobStart[0]['name_of_employer'] : "NA";
                        $result[$index]['JOBSTARTDATE'] = ($jobStart) ? $jobStart[0]['first_date_of_work'] : "NA";
                        $result[$index]['ADDRESS'] = ($jobStart) ? $jobStart[0]['address'] : "NA";
                    }
                    elseif($request->appointment_type == 4 ){
                        $result[$index]['MEDICATIONS'] = $medication;
                        $result[$index]['WORKREADY'] = ($etp) ? $etp[0]['work_readiness'] : "NA";
                    }
                    elseif($request->appointment_type == 5 ){
                        $result[$index]['MEDICATIONS'] = $medication;
                        $result[$index]['WORKREADY'] = ($job_club ) ? $job_club [0]['work_readiness'] : "NA";
                    }
                    elseif($request->appointment_type == 6 ){
                        $result[$index]['MEDICATIONS'] = $medication;
                        $result[$index]['CURRENTINTERV'] = ($curr_interv) ? $curr_interv[0]['section_value'] : "NA";
                        $result[$index]['CONTACT'] = ($cps) ? $cps[0]['informants_contact'] : "NA";
                    }
                    else{
                        $result[$index]['MEDICATIONS'] = $medication;
                    }

                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $result[$index]['app_no'] = 'C' . $apcount[$v['patient_mrn_id']];
                    $result[$index]['app_no_se'] = 'SE' . $apcount[$v['patient_mrn_id']];
                    $result[$index]['app_no_etp'] = 'ETP' . $apcount[$v['patient_mrn_id']];
                    $result[$index]['app_no_jc'] = 'JC' . $apcount[$v['patient_mrn_id']];
                    $result[$index]['app_no_cps'] = 'CPS' . $apcount[$v['patient_mrn_id']];
                    $attendanceStatus[$index] = $result[$index]['Attendance_status'];
                    $result[$index]['no_job_search'] = ($job_search) ? count($job_search) : '0';
                    $result[$index]['no_job_visit'] = ($jv) ? $jv : '0';
                    if($attendanceStatus[$index] == "Attend"){
                        $attend += 1;
                    }
                    elseif($attendanceStatus[$index] == "No Show"){
                        $noShow += 1;
                    };

                    $index++;



            }
        }
        if ($result) {

            $totalPatients ='Total Patient:   '.count($result).'<br>';
            $totalPatientsPDF =count($result);
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = 'Total Days:  '.$diff->format("%a").'<br>';
            $totalDaysPDF = $diff->format("%a");
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $filePath = '';
            $filename='';
            $periodofservice='Period of Services :'.$fromDate. ' To '. $toDate .'<br>';
            $Attend='Attend:   '.$attend.'<br>';
            $NoShow='No Show:   '.$noShow.'<br>';

            if($request->appointment_type == 1){
                $summary= '<b>'.'<h2>'.'REPORT OF CONSULTATION CLINIC'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }
            elseif($request->appointment_type == 3){
                $summary= '<b>'.'<h2>'.'REPORT OF SUPPORTED EMPLOYMENT'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }
            elseif($request->appointment_type == 4){
                $summary= '<b>'.'<h2>'.'REPORT OF ETP'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }
            elseif($request->appointment_type == 5){
                $summary= '<b>'.'<h2>'.'REPORT OF JOB CLUB'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }
            elseif($request->appointment_type == 6){
                $summary= '<b>'.'<h2>'.'REPORT OF CPS'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }
            else{
                $summary= $periodofservice.'<br>'.$totalDays.'<br>'.$totalPatients.'<br>'.$Attend.'<br>'.$NoShow.'<br>';
            }

            if (isset($request->report_type) && $request->report_type == 'excel') {
                if($request->appointment_type == 1){
                    $filename = 'consultation-report-'.time().'.xls';
                }
                elseif($request->appointment_type == 3){
                    $filename = 'se-report-'.time().'.xls';
                }
                elseif($request->appointment_type == 4){
                    $filename = 'etp-report-'.time().'.xls';
                }
                elseif($request->appointment_type == 5){
                    $filename = 'jobclub-report-'.time().'.xls';
                }
                elseif($request->appointment_type == 6){
                    $filename = 'cps-report-'.time().'.xls';
                }
                else{
                    $filename = 'patient-report-'.time().'.xls';
                };
                return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'totalPatients' => $totalPatients,
                    'totalDays' =>  $totalDays,
                    'fromDate' => $fromDate,
                    'toDate' =>  $toDate,
                    'header' => $summary,
                    'filename' => $filename,
                    'code' => 200
                ]);
            } else {
                return response()->json([
                    "message" => "Activity Report", 'result' => $result, 'filepath' => '',
                    "Total_Days" => $totalDays, "Total_Patient" => $totalPatients, "Attend" => $attend, "No_Show" => $noShow, "Total_PatientsPDF" => $totalPatientsPDF, "Total_DaysPDF"=> $totalDaysPDF, "code" => 200
                ]);
            }
        } else {
            return response()->json(["message" => "Activity Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getVONActivityReport(Request $request)
    {
        $from = $request->fromDate;
        $to = $request->toDate;
        $result = [];
        $index = 0;
        $toc = ['individual' => 'INDIVIDUAL', 'org' => 'ORGANIZATION', 'group' => 'GROUP'];
        $toi = ['Volunteerism' => 'VOLUNTEERISM', 'Networking Make a Contribution' => 'NETWORKING', 'Outreach Project Collaboration' => 'OUTREACH'];
        $toiArr = ['VOLUNTEER' => 0, 'OUTREACH' => 0, 'NETWORKING' => 0];
        $ssh = VonOrgRepresentativeBackground::whereBetween('created_at', [$from, $to]);
        if ($request->toc != NULL){
            $ssh->where('section', $request->toc);
        }
        if ($request->aoi != NULL){

            $ssh->where('area_of_involvement', $request->aoi);
        }
        if ($request->screening != NULL){
            $ssh->where('screening_mode', $request->screening);
        }
        $response = $ssh->get()->toArray();
        $vorb  = json_decode(json_encode($response), true);

        if ($vorb) {
            foreach ($vorb as $k => $v) {
                if ($request->location == NULL) {

                        $location_value1=OutReachProjects::where('parent_section_id', $v['id'])
                        ->where('project_loaction','mentari')
                        ->where('project_loaction_value', $request->branch_name)->first();

                        $location_value2=NetworkingContribution::where('parent_section_id', $v['id'])
                        ->where('project_loaction','project-location-mentari')
                        ->where('project_loaction_value', $request->branch_name)->first();

                            if(empty($location_value1) && empty($location_value2)){
                                continue;
                            }
                 }
                if ($request->event != NULL){

                    $event=OutReachProjects::where('parent_section_id', $v['id'])
                    ->where('project_name','LIKE','%'.$request->event.'%')->first();

                    if(empty($event)){
                        continue;
                    }
                }
                if ($request->others_value != NULL) {

                    $location_value_other=OutReachProjects::where('parent_section_id', $v['id'])
                    ->where('project_loaction','=','project-location-others')
                    ->where('project_loaction_value','LIKE',"%".$request->others_value."%")->first();

                    if(empty($location_value_other)){
                        continue;
                    }
                }
                if ($request->location_value != NULL) {

                    $location_value=OutReachProjects::where('parent_section_id', $v['id'])
                    ->where('project_loaction','mentari')
                    ->where('project_loaction_value', $request->location_value)->first();

                    if(empty($location_value)){
                        continue;
                    }

                }
                $result[$index]['No']=$index+1;
                $result[$index]['Name'] = $v['name'];
                $result[$index]['Type_of_Collaboration'] = $toc[$v['section']];
                $result[$index]['Type_of_Involvement'] = $toi[$v['area_of_involvement']];
                $result[$index]['Screening_Done'] = ($v['screening_mode'] == 1) ? 'YES' : 'NO';
                $result[$index]['Contact_Number'] = $v['phone_number'];
                $orp = [];
                $vol = [];
                $result[$index]['Cost'] = '-';
                $result[$index]['Others'] = '-';
                $result[$index]['No_of_Participants'] = '-';
                $result[$index]['Mentari'] = '-';
                $result[$index]['Location'] = '-';
                if ($v['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $toiArr['OUTREACH'] = $toiArr['OUTREACH'] + 1;
                    $orp = OutReachProjects::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($v['area_of_involvement'] == 'Networking Make a Contribution') {
                    $toiArr['NETWORKING'] = $toiArr['NETWORKING'] + 1;
                    $orp = NetworkingContribution::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($v['area_of_involvement'] == 'Volunteerism') {
                    $toiArr['VOLUNTEER'] = $toiArr['VOLUNTEER'] + 1;
                    $vol = Volunteerism::where('parent_section_id', $v['id'])->get()->toArray();
                }
                if ($orp) {
                    if (array_key_exists('estimated_budget', $orp[0])) {
                        $budget = $orp[0]['estimated_budget'];
                    } else {
                        $budget = $orp[0]['budget'];
                    }
                    $result[$index]['Cost'] = 'RM' . $budget;
                    $result[$index]['Location'] = strtoupper($orp[0]['project_loaction']);
                    $result[$index]['Mentari'] = ($orp[0]['project_loaction'] == 'mentari') ? $orp[0]['project_loaction_value'] : '-';
                    $result[$index]['Others'] = ($orp[0]['project_loaction'] != 'mentari') ? $orp[0]['project_loaction_value'] : '-';
                    $result[$index]['No_of_Participants'] = $orp[0]['no_of_paricipants'];
                }
                if ($vol) {
                    $result[$index]['Cost'] = '-';
                    $result[$index]['Others'] = '-';
                    $result[$index]['Location'] = 'MENTARI';
                    $brnchName = HospitalBranchManagement::where('id', $v['branch_id'])->get()->toArray();
                    $result[$index]['No_of_Participants'] = '-';
                    $result[$index]['Mentari'] = $brnchName[0]['hospital_branch_name'];
                }

                if ($request->location == 'other') {
                    if ($result[$index]['Others'] != $request->location_value) {
                        unset($result[$index]);
                        if ($v['area_of_involvement'] == 'Outreach Project Collaboration') {
                            $toiArr['OUTREACH'] = $toiArr['OUTREACH'] - 1;
                        }
                        if ($v['area_of_involvement'] == 'Networking Make a Contribution') {
                            $toiArr['NETWORKING'] = $toiArr['NETWORKING'] - 1;
                        }
                        if ($v['area_of_involvement'] == 'Volunteerism') {
                            $toiArr['VOLUNTEER'] = $toiArr['VOLUNTEER'] - 1;
                        }
                    }
                }

                $index++;
            }
        }

        if ($result) {
            $totalPatients = count($result);
            $totalPatientsExl ='Total Patient:   '.count($result).'<br>';
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDaysExl = 'Total Days:  '.$diff->format("%a").'<br>';
            $totalDays = $diff->format("%a");
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;
            $filePath = '';
            $filename='';
            $volunteer= 'Volunteer:  '.$toiArr['VOLUNTEER'].'<br>';
            $outreach= 'Outreach:  '.$toiArr['OUTREACH'].'<br>';
            $networking= 'Networking:  '.$toiArr['NETWORKING'].'<br>';
            $periodofservice='Period of Services :'.$fromDate. ' To '. $toDate .'<br>';
            $summary= '<b>'.'<h2>'.'REPORT OF VOLUNTEER, OUTREACH AND NETWORKING'.'</h2>'.'</b>'.$periodofservice.'<br>'.$totalDaysExl.'<br>'.$totalPatientsExl.'<br>'.$volunteer.'<br>'.$outreach.'<br>'.$networking.'<br>';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'VON-report-'.time().'.xls';
                return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'totalPatients' => $totalPatientsExl,
                    'totalDays' =>  $totalDaysExl,
                    'fromDate' => $fromDate,
                    'toDate' =>  $toDate,
                    'header' => $summary,
                    'filename' => $filename,
                    'code' => 200
                ]);
            } else {
                return response()->json(["message" => "Activity VON Report", 'result' => $result, 'toiArr' => $toiArr, 'Total_Patient' => $totalPatients, 'Total_Days' => $totalDays, 'filepath' => null, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "Activity VON Report", 'result' => [], 'toiArr' => [], 'filepath' => null, "code" => 200]);
        }
    }

    public function getGeneralReport(Request $request)
    {
        $user = DB::table('staff_management')
        ->select('roles.code')
        ->join('roles', 'staff_management.role_id', '=', 'roles.id')
        ->where('staff_management.email', '=', $request->email)
        ->first();

        $month = ['January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7,
        'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12];
        $Month=$month[$request->month];

        $demo = [];
        $age=[];
        if($user->code!='superadmin'){
            $demo['pr.branch_id'] = $request->branch_id;
        }
        if ($request->name) {
            $demo['pr.name_asin_nric'] = $request->name;
        }
        if ($request->citizenship) {
            $demo['pr.citizenship'] = $request->citizenship;
        }
        if ($request->gender) {
            $demo['sex'] = $request->gender;
        }
        if ($request->race) {
            $demo['race_id'] = $request->race;
        }
        if ($request->religion) {
            $demo['religion_id'] = $request->religion;
        }
        if ($request->marital_status) {
            $demo['marital_id'] = $request->marital_status;
        }
        if ($request->education_level) {
            $demo['education_level'] = $request->education_level;
        }
        if ($request->accommodation_id) {
            $demo['accomodation_id'] = $request->accommodation_id;

        }
        if ($request->occupation_status) {
            $demo['occupation_status'] = $request->occupation_status;
        }
        if ($request->fee_exemption_status) {
            $demo['fee_exemption_status'] = $request->fee_exemption_status;
        }
        if ($request->occupation_sector) {
            $demo['occupation_sector'] = $request->occupation_sector;
        }

        if ($request->referral_type){
            $demo['referral_type'] = $request->referral_type;
        }
        if ($request->diagnosis_id){
            $demo['ud.diagnosis_id'] = $request->diagnosis_id;
        }
        $appointments = DB::table('user_diagnosis as ud')->select('pad.id','pad.patient_mrn_id','pad.booking_date',
        'pad.booking_time','pr.created_at','pr.nric_no','pr.passport_no','name_asin_nric','pad.appointment_status','pr.address1','pr.address2',
        'pr.address3','pc.city_name','s.state_name','pc.postcode','pr.mobile_no','pr.birth_date','pr.age','gs1.section_value as citizenship','gs2.section_value as race',
        'gs3.section_value as marital','gs4.section_value as religion','gs5.section_value as accomodation','gs6.section_Value as education_level',
        'gs7.section_value as occupation_status','gs8.section_value as fee_exemption_status','gs9.section_value as occupation_sector','gs10.section_value as sex',
        'sr.service_name','gs11.section_value as patient_category','gs12.section_value as type_visit','gs13.section_value as type_referral','sm.name as staff_name',
        'gs14.section_value as outcome','ud.diagnosis_id','ud.add_diagnosis_id','ud.code_id','ud.sub_code_id','ud.add_code_id',
        'ud.add_sub_code_id','ud.category_services','ic.icd_code as diagnosis_code','ic.icd_name as diagnosis_name','icat.icd_category_code as code','icat.icd_category_name as code_name',
        'icat2.icd_category_code as add_code','icat2.icd_category_name as add_code_name')

        ->leftJoin('patient_appointment_details as pad', 'pad.id', '=', 'ud.app_id')
        ->leftJoin('patient_registration as pr', 'pr.id', '=', 'pad.patient_mrn_id')
        ->leftJoin('state as s', 's.id', '=', 'pr.state_id')
        ->leftJoin('postcode as pc', 'pc.id', '=', 'pr.postcode')
        ->leftJoin('service_register as sr','sr.id', '=', 'pad.appointment_type')
        ->leftJoin('staff_management as sm','sm.id', '=', 'pad.staff_id')
        ->leftJoin('general_setting as gs1', 'gs1.id', '=', 'pr.citizenship')
        ->leftJoin('general_setting as gs2', 'gs2.id', '=', 'pr.race_id')
        ->leftJoin('general_setting as gs3', 'gs3.id', '=', 'pr.marital_id')
        ->leftJoin('general_setting as gs4', 'gs4.id', '=', 'pr.religion_id')
        ->leftJoin('general_setting as gs5', 'gs5.id', '=', 'pr.accomodation_id')
        ->leftJoin('general_setting as gs6', 'gs6.id', '=', 'pr.education_level')
        ->leftJoin('general_setting as gs7', 'gs7.id', '=', 'pr.occupation_status')
        ->leftJoin('general_setting as gs8', 'gs8.id', '=', 'pr.fee_exemption_status')
        ->leftJoin('general_setting as gs9', 'gs9.id', '=', 'pr.occupation_sector')
        ->leftJoin('general_setting as gs10','gs10.id', '=', 'pr.sex')
        ->leftJoin('general_setting as gs11','gs11.id', '=', 'pad.patient_category')
        ->leftJoin('general_setting as gs12','gs12.id', '=', 'pad.type_visit')
        ->leftJoin('general_setting as gs13','gs13.id', '=', 'pr.referral_type')
        //->leftJoin('user_diagnosis as ud','ud.app_id','=','pad.id')
        ->leftJoin('general_setting as gs14','gs14.id','=','ud.outcome_id')
        ->leftJoin('icd_code as ic','ic.id','=','ud.diagnosis_id')
        ->leftJoin('icd_category as icat','icat.id','=','ud.code_id')
        ->leftJoin('icd_category as icat2','icat2.id','=','ud.add_code_id')

        ->whereYear('pad.booking_date', $request->year)
        ->whereMonth('pad.booking_date', $Month)
        ->where('pad.appointment_status','!=',0);
        if ($request->type_visit != 0)
            $appointments = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $appointments =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $appointments = $appointments->where('appointment_type', $request->appointment_type);

        if ($request->Age) {
            $age = GeneralSetting::where('id', $request->Age)->first();
            $age['agemin']=$age['min_age'];
            $age['agemax']=$age['max_age'];
                if ($age){
                    if($age['agemin'] && $age['agemax']!=NULL){
                        $appointments->whereBetween('age',[$age['agemin'],$age['agemax']]);
                    }else if($age['agemin']==NULL) {
                        $ssh->where('age','<=',$age['agemax']);
                    }else if($age['agemax']==NULL) {
                        $appointments->where('age','>=',$age['agemin']);
                    }
                }
        }
        if ($demo){
            $appointments->where($demo);
        }

        $ssh = $appointments->get()->toArray();

        if ($ssh) {
            $result = [];
            $index=0;
            $ssh  = json_decode(json_encode($ssh), true);

            foreach($ssh as $k=>$v){

                ////////////////////////////////////////////for additional diagnosis/////////////////////////////////////////////////
                if($v['add_diagnosis_id']!=NULL && $v['add_diagnosis_id']!='0' && $v['add_diagnosis_id']!='-'){
                    $e=0;
                    $add_diagnosis_id=[];
                        foreach (explode(',',$v['add_diagnosis_id']) as $add) {
                                if($add!='0'|| $add!=NULL){
                                    $add_diagnosis=DB::select('CALL icd_code(' . $add . ')');
                                    $additional_diagnosis_id[$e]['additional_diagnosis']=$add_diagnosis[0]->icd_code.' '.$add_diagnosis[0]->icd_name;
                                }else{                                   
                                    $additional_diagnosis_id[$e]['additional_diagnosis']='-';
                                }
                            $e++;
                        }
                        if($e==1){
                            $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis2']='-';
                            $result[$index]['additional_diagnosis3']='-';
                            $result[$index]['additional_diagnosis4']='-';
                            $result[$index]['additional_diagnosis5']='-';
                        }else if($e==2){
                            $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis3']='-';
                            $result[$index]['additional_diagnosis4']='-';
                            $result[$index]['additional_diagnosis5']='-';
                        }else if($e==3){
                            $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis4']='-';
                            $result[$index]['additional_diagnosis5']='-';
                        }else if($e==4){
                            $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis4']=$additional_diagnosis_id[3]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis5']='-';
                        }else if($e==5){
                            $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis4']=$additional_diagnosis_id[3]['additional_diagnosis'];
                            $result[$index]['additional_diagnosis5']=$additional_diagnosis_id[4]['additional_diagnosis'];
                        } 
                }else{
                    $result[$index]['additional_diagnosis1']='-';
                    $result[$index]['additional_diagnosis2']='-';
                    $result[$index]['additional_diagnosis3']='-';
                    $result[$index]['additional_diagnosis4']='-';
                    $result[$index]['additional_diagnosis5']='-';
                }


                                ///////////////////////////for sub code///////////////////////////////////////////
                if($v['sub_code_id']!=NULL && $v['sub_code_id']!='0' && $v['sub_code_id']!='-'){
                    $f=0;
                    $sub_code_id_array=[];
                        foreach (explode(',',$v['sub_code_id']) as $sub) {
                                    if($sub!=NULL && $sub!='0'){
                                        $sub_code=DB::select('CALL icd_code(' . $sub . ')');
                                        $sub_code_id_array[$f]['sub_code']=$sub_code[0]->icd_code.' '.$sub_code[0]->icd_name;
                                    }else{
                                        $sub_code_id_array[$f]['sub_code']='-';
                                        }
                                    $f++;
                                }
                        if($f==1){
                            $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
                            $result[$index]['sub_code2']='-';
                            $result[$index]['sub_code3']='-';
                            $result[$index]['sub_code4']='-';
                            $result[$index]['sub_code5']='-';
                        }else if($f==2){
                            $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
                            $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
                            $result[$index]['sub_code3']='-';
                            $result[$index]['sub_code4']='-';
                            $result[$index]['sub_code5']='-';
                        }else if($f==3){
                            $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
                            $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
                            $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
                            $result[$index]['sub_code4']='-';
                            $result[$index]['sub_code5']='-';
                        }else if($f==4){
                            $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
                            $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
                            $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
                            $result[$index]['sub_code4']=$sub_code_id_array[3]['sub_code'];
                            $result[$index]['sub_code5']='-';
                        }else if($f==5){
                            $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
                            $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
                            $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
                            $result[$index]['sub_code4']=$sub_code_id_array[3]['sub_code'];
                            $result[$index]['sub_code5']=$sub_code_id_array[4]['sub_code'];
                        }
                }else{
                    $result[$index]['sub_code1']='-';
                    $result[$index]['sub_code2']='-';
                    $result[$index]['sub_code3']='-';
                    $result[$index]['sub_code4']='-';
                    $result[$index]['sub_code5']='-';
                }

                            ///////////////////////////for additional sub code/////////////////////////////////////
            if($v['add_sub_code_id']!=NULL && $v['add_sub_code_id']!='0' && $v['add_sub_code_id']!='-'){
                $f=0;
                $add_sub_code_val=[];
                    foreach (explode(',',$v['add_sub_code_id']) as $add_sub) {
                                    if($add_sub!=NULL && $add_sub!='0'){
                                    $add_sub_code=DB::select('CALL icd_code(' . $add_sub . ')');
                                    $add_sub_code_val[$f]['add_sub_code']=$add_sub_code[0]->icd_code.' '.$add_sub_code[0]->icd_name;
                                    }else{
                                    $add_sub_code_val[$f]['add_sub_code']='-';
                                    }
                            $f++;
                    }
                    if($f==1){
                        $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
                        $result[$index]['add_sub_code2']='-';
                        $result[$index]['add_sub_code3']='-';
                        $result[$index]['add_sub_code4']='-';
                        $result[$index]['add_sub_code5']='-';
                    }else if($f==2){
                        $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
                        $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
                        $result[$index]['add_sub_code3']='-';
                        $result[$index]['add_sub_code4']='-';
                        $result[$index]['add_sub_code5']='-';
                    }else if($f==3){
                        $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
                        $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
                        $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
                        $result[$index]['add_sub_code4']='-';
                        $result[$index]['add_sub_code5']='-';
                    }else if($f==4){
                        $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
                        $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
                        $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
                        $result[$index]['add_sub_code4']=$add_sub_code_val[3]['add_sub_code'];
                        $result[$index]['add_sub_code5']='-';
                    }else if($f==5){
                        $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
                        $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
                        $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
                        $result[$index]['add_sub_code4']=$add_sub_code_val[3]['add_sub_code'];
                        $result[$index]['add_sub_code5']=$add_sub_code_val[4]['add_sub_code'];
                    }
            }else{
                $result[$index]['add_sub_code1']='-';
                $result[$index]['add_sub_code2']='-';
                $result[$index]['add_sub_code3']='-';
                $result[$index]['add_sub_code4']='-';
                $result[$index]['add_sub_code5']='-';
            }     


            $result[$index]['No']=$index+1;
            $result[$index]['Registration_date'] = date('d/m/Y', strtotime($v['created_at']));
            $result[$index]['Registration_Time'] = date('h:i:s A', strtotime($v['created_at']));
            if($v['nric_no']==NULL){
                $result[$index]['nric_no'] = $v['passport_no'];
            }else{
                $result[$index]['nric_no'] = $v['nric_no'];
            }
            $result[$index]['Name'] = $v['name_asin_nric'];
            $result[$index]['Attendance_status'] = $v['appointment_status'];
            $result[$index]['ADDRESS'] = $v['address1'] . ' ' . $v['address2'] . ' ' . $v['address3'];
            $result[$index]['CITY'] = $v['city_name'];
            $result[$index]['STATE'] = $v['state_name'];
            $result[$index]['POSTCODE'] = $v['postcode'];
            $result[$index]['PHONE_NUMBER'] = $v['mobile_no'];
            $result[$index]['DATE_OF_BIRTH'] = $v['birth_date'];
            $result[$index]['AGE'] = $v['age'];
            $result[$index]['citizenship'] = $v['citizenship'];
            $result[$index]['race'] = $v['race'];
            $result[$index]['religion'] = $v['religion'];
            $result[$index]['marital'] = $v['marital'];
            $result[$index]['accomodation'] = $v['accomodation'];
            $result[$index]['education_level'] = $v['education_level'];
            $result[$index]['occupation_status'] = $v['occupation_status'];
            $result[$index]['fee_exemption_status'] = $v['fee_exemption_status'];
            $result[$index]['occupation_sector'] = $v['occupation_sector'];
            $result[$index]['GENDER'] = $v['sex'];
            $result[$index]['APPOINTMENT_TYPE'] = $v['service_name'];
            $result[$index]['DIAGNOSIS'] = $v['diagnosis_name'];
            $result[$index]['DIAGNOSIS_CODE'] = $v['diagnosis_code'];
            $result[$index]['code_id'] = $v['code'].' '.$v['code_name'];
            $result[$index]['add_code_id'] = $v['add_code'].' '.$v['add_code_name'];
            $result[$index]['CATEGORY_OF_PATIENTS'] = $v['patient_category'];
            $result[$index]['TYPE_OF_Visit'] = $v['type_visit'];
            $result[$index]['TYPE_OF_Refferal'] = $v['type_referral'] ;
            $result[$index]['Attending_staff'] = $v['staff_name'];
            $result[$index]['outcome'] = $v['outcome'];
            $result[$index]['category_of_services'] = $v['category_services'];

            $index++;
            }


        }
        //dd($result); //confirmkan status and data yang amik based on table2 cd yang lain. takut nama column lain. and check either data is correct
        if ($result) {
            $totalReports = count($result);
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'GeneralReport-'.time().'.xls';
                  return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'header' => 'General Report '.$request->month.' '.$request->year,
                    'filename' => $filename,
                    'code' => 200]);
            } else {
                $filename = 'GeneralReport-'.time().'.pdf';
                return response()->json(["message" => "General Report", 'result' => $result, 'filename' => $filename, "code" => 200]);
            }
        } else {
            return response()->json(["message" => "General Report", 'result' => [], 'filename' => null, "code" => 200]);
        }
    }
    // { 
    //     $user = DB::table('staff_management')
    //     ->select('roles.code')
    //     ->join('roles', 'staff_management.role_id', '=', 'roles.id')
    //     ->where('staff_management.email', '=', $request->email)
    //     ->first();

    //     $month = ['January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7,
    //     'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12];
    //     $Month=$month[$request->month];

    //     $demo = [];
    //     $age=[];

    //     if($user->code!='superadmin'){
    //         $demo['pr.branch_id'] = $request->branch_id;
    //     }
    //     if ($request->name) {
    //         $demo['pr.name_asin_nric'] = $request->name;
    //     }
    //     if ($request->citizenship) {
    //         $demo['pr.citizenship'] = $request->citizenship;
    //     }
    //     if ($request->gender) {
    //         $demo['sex'] = $request->gender;
    //     }
    //     if ($request->race) {
    //         $demo['race_id'] = $request->race;
    //     }
    //     if ($request->religion) {
    //         $demo['religion_id'] = $request->religion;
    //     }
    //     if ($request->marital_status) {
    //         $demo['marital_id'] = $request->marital_status;
    //     }
    //     if ($request->education_level) {
    //         $demo['education_level'] = $request->education_level;
    //     }
    //     if ($request->accommodation_id) {
    //         $demo['accomodation_id'] = $request->accommodation_id;

    //     }
    //     if ($request->occupation_status) {
    //         $demo['occupation_status'] = $request->occupation_status;
    //     }
    //     if ($request->fee_exemption_status) {
    //         $demo['fee_exemption_status'] = $request->fee_exemption_status;
    //     }
    //     if ($request->occupation_sector) {
    //         $demo['occupation_sector'] = $request->occupation_sector;
    //     }

    //     if ($request->referral_type){
    //         $demo['referral_type'] = $request->referral_type;
    //     }

    //     $appointments = DB::table('patient_appointment_details as pad')->select('pad.id','pad.patient_mrn_id','pad.booking_date',
    //     'pad.booking_time','pr.created_at','pr.nric_no','pr.passport_no','name_asin_nric','pad.appointment_status','pr.address1','pr.address2',
    //     'pr.address3','pc.city_name','s.state_name','pc.postcode','pr.mobile_no','pr.birth_date','pr.age','gs1.section_value as citizenship','gs2.section_value as race',
    //     'gs3.section_value as marital','gs4.section_value as religion','gs5.section_value as accomodation','gs6.section_Value as education_level',
    //     'gs7.section_value as occupation_status','gs8.section_value as fee_exemption_status','gs9.section_value as occupation_sector','gs10.section_value as sex',
    //     'sr.service_name','gs11.section_value as patient_category','gs12.section_value as type_visit','gs13.section_value as type_referral','sm.name as staff_name')
        
    //     ->leftJoin('patient_registration as pr', 'pr.id', '=', 'pad.patient_mrn_id')
    //     ->leftJoin('state as s', 's.id', '=', 'pr.state_id')
    //     ->leftJoin('postcode as pc', 'pc.id', '=', 'pr.postcode')
    //     ->leftJoin('service_register as sr','sr.id', '=', 'pad.appointment_type')
    //     ->leftJoin('staff_management as sm','sm.id', '=', 'pad.staff_id')
    //     ->leftJoin('general_setting as gs1', 'gs1.id', '=', 'pr.citizenship')
    //     ->leftJoin('general_setting as gs2', 'gs2.id', '=', 'pr.race_id')
    //     ->leftJoin('general_setting as gs3', 'gs3.id', '=', 'pr.marital_id')
    //     ->leftJoin('general_setting as gs4', 'gs4.id', '=', 'pr.religion_id')
    //     ->leftJoin('general_setting as gs5', 'gs5.id', '=', 'pr.accomodation_id')
    //     ->leftJoin('general_setting as gs6', 'gs6.id', '=', 'pr.education_level')
    //     ->leftJoin('general_setting as gs7', 'gs7.id', '=', 'pr.occupation_status')
    //     ->leftJoin('general_setting as gs8', 'gs8.id', '=', 'pr.fee_exemption_status')
    //     ->leftJoin('general_setting as gs9', 'gs9.id', '=', 'pr.occupation_sector')
    //     ->leftJoin('general_setting as gs10','gs10.id', '=', 'pr.sex')
    //     ->leftJoin('general_setting as gs11','gs11.id', '=', 'pad.patient_category')
    //     ->leftJoin('general_setting as gs12','gs12.id', '=', 'pad.type_visit')
    //     ->leftJoin('general_setting as gs13','gs13.id', '=', 'pr.referral_type')

    //     ->whereYear('pad.booking_date', $request->year)
    //     ->whereMonth('pad.booking_date', $Month)
    //     ->where('pad.appointment_status','!=',0);
    //     if ($request->type_visit != 0)
    //         $appointments = $appointments->where('type_visit', $request->type_visit);
    //     if ($request->patient_category != 0)
    //         $appointments =  $appointments->where('patient_category', $request->patient_category);
    //     if ($request->appointment_type != 0)
    //         $appointments = $appointments->where('appointment_type', $request->appointment_type);

    //     if ($request->Age) {
    //         $age = GeneralSetting::where('id', $request->Age)->first();
    //         // $age=DB::select('CALL general_setting(' . $request->Age . ')');
    //         $age['agemin']=$age['min_age'];
    //         $age['agemax']=$age['max_age'];
    //             if ($age){
    //                 if($age['agemin'] && $age['agemax']!=NULL){
    //                     $appointments->whereBetween('age',[$age['agemin'],$age['agemax']]);
    //                 }else if($age['agemin']==NULL) {
    //                     $ssh->where('age','<=',$age['agemax']);
    //                 }else if($age['agemax']==NULL) {
    //                     $appointments->where('age','>=',$age['agemin']);
    //                 }
    //             }
    //     }


    //     if ($demo){
    //         $appointments->where($demo);
    //     }
    //     $ssh = $appointments->get()->toArray();
    //     if ($ssh) {
    //         $result = [];
    //         $index=0;
    //         $ssh  = json_decode(json_encode($ssh), true);

    //         foreach($ssh as $k=>$v){

    //             $id=$v['id'];

    //             if (PatientCounsellorClerkingNotes::where('appointment_details_id', $id)
    //                 ->where('status','1')->exists()) {
    //                     $diagnosis=DB::select('CALL patient_counsellor_clerking_notes(' . $id . ')');
    //                     //dd($diagnosis[0]->icd_name); /////////////// sambung sini
    //                 // $diagnosis = PatientCounsellorClerkingNotes::select('icd_code.icd_code','icd_code.icd_name','diagnosis_id','additional_diagnosis','code_id','sub_code_id',
    //                 // 'additional_code_id','additional_subcode','outcome_id','category_services')
    //                 // ->leftjoin('icd_code','icd_code.id','=','patient_counsellor_clerking_notes.diagnosis_id')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('patient_counsellor_clerking_notes.status','=','1')->first();
    //                     $outcome=$diagnosis[0]->outcome_id;
    //                     $category_services=$diagnosis[0]->category_services;
    //                     // $diagnosis_id=$diagnosis['diagnosis_id'];
    //                     $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                     $code_id=$diagnosis[0]->code_id;
    //                     $sub_code_id=$diagnosis[0]->sub_code_id;
    //                     $add_code_id=$diagnosis[0]->additional_code_id;
    //                     $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                     if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                         if( $diagnosis[0]->diagnosis_id!=$request->diagnosis_id ){
    //                             continue;
    //                         }
    //                     }
    //                     if($diagnosis[0]->diagnosis_id!=0){
    //                         $icd_code=$diagnosis[0]->icd_code;
    //                         $icd_name=$diagnosis[0]->icd_name; //dah
    //                     }else{
    //                         $icd_code='-';
    //                         $icd_name='-'; //dah
    //                     }
                        

    //             }else if (PsychiatryClerkingNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                     $diagnosis=DB::select('CALL psychiatry_clerking_note(' . $id . ')');
    //                     // $diagnosis = PsychiatryClerkingNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_id','additional_diagnosis','code_id','sub_code_id',
    //                     // 'additional_code_id','additional_subcode','outcome_id','category_services')
    //                     // ->leftjoin('icd_code','icd_code.id','=','psychiatry_clerking_note.diagnosis_id')
    //                     // ->where('appointment_details_id', $id)
    //                     // ->where('psychiatry_clerking_note.status','=','1')->first();
    //                     // if($diagnosis){
                               
    //                     // }else{
    //                     //     dd($id);
    //                     // }
    //                     $outcome=$diagnosis[0]->outcome_id;
    //                     $category_services=$diagnosis[0]->category_services;
    //                     // $diagnosis_id=$diagnosis['diagnosis_id'];
    //                     $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                     $code_id=$diagnosis[0]->code_id;
    //                     $sub_code_id=$diagnosis[0]->sub_code_id;
    //                     $add_code_id=$diagnosis[0]->additional_code_id;
    //                     $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                     if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                         if( $diagnosis[0]->diagnosis_id!=$request->diagnosis_id ){
    //                             continue;
    //                         }
    //                     }
    //                     if($diagnosis[0]->diagnosis_id!=0){
    //                         $icd_code=$diagnosis[0]->icd_code;
    //                         $icd_name=$diagnosis[0]->icd_name; //dah
    //                     }else{
    //                         $icd_code='-';
    //                         $icd_name='-'; //dah
    //                     }

    //             }else if (PsychiatricProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) {   
    //                 $diagnosis=DB::select('CALL psychiatric_progress_note(' . $id . ')');
    //                 // $diagnosis = PsychiatricProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis','additional_diagnosis','code_id','sub_code_id',
    //                 //     'additional_code_id','additional_subcode','outcome_id','category_services')
    //                 //     ->leftjoin('icd_code','icd_code.id','=','psychiatric_progress_note.diagnosis')
    //                 //     ->where('appointment_details_id', $id)
    //                 //     ->where('psychiatric_progress_note.status','=','1')->first();
    //                     $outcome=$diagnosis[0]->outcome_id;
    //                     $category_services=$diagnosis[0]->category_services;
    //                     // $diagnosis_id=$diagnosis['diagnosis'];
    //                     $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                     $code_id=$diagnosis[0]->code_id;
    //                     $sub_code_id=$diagnosis[0]->sub_code_id;
    //                     $add_code_id=$diagnosis[0]->additional_code_id;
    //                     $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                     if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                         if( $diagnosis[0]->diagnosis!=$request->diagnosis_id ){
    //                             continue;
    //                         }
    //                     }
    //                     if($diagnosis[0]->diagnosis!=0){
    //                         $icd_code=$diagnosis[0]->icd_code;
    //                         $icd_name=$diagnosis[0]->icd_name; //dah
    //                     }else{
    //                         $icd_code='-';
    //                         $icd_name='-'; //dah
    //                     }

    //             }else if (CpsProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                     $diagnosis=DB::select('CALL cps_progress_note(' . $id . ')');
    //                     // $diagnosis = CpsProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_type','additional_diagnosis','code_id','sub_code_id',
    //                     // 'additional_code_id','additional_subcode','outcome','service_category')
    //                     // ->leftjoin('icd_code','icd_code.id','=','cps_progress_note.diagnosis_type')
    //                     // ->where('appointment_details_id', $id)
    //                     // ->where('cps_progress_note.status','=','1')->first();
    //                     $outcome=$diagnosis[0]->outcome;
    //                     $category_services=$diagnosis[0]->service_category;
    //                     // $diagnosis_id=$diagnosis['diagnosis_type'];
    //                     $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                     $code_id=$diagnosis[0]->code_id;
    //                     $sub_code_id=$diagnosis[0]->sub_code_id;
    //                     $add_code_id=$diagnosis[0]->additional_code_id;
    //                     $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                     if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                         if( $diagnosis[0]->diagnosis_type!=$request->diagnosis_id ){
    //                             continue;
    //                         }
    //                     }
    //                     if($diagnosis[0]->diagnosis_type!=0){
    //                         $icd_code=$diagnosis[0]->icd_code;
    //                         $icd_name=$diagnosis[0]->icd_name; //dah
    //                     }else{
    //                         $icd_code='-';
    //                         $icd_name='-'; //dah
    //                     }

    //              }else if (SeProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL se_progress_note(' . $id . ')');
    //                 // $diagnosis = SeProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_type','additional_diagnosis','code_id','sub_code_id',
    //                 // 'additional_code_id','additional_subcode','outcome','service_category')
    //                 // ->leftjoin('icd_code','icd_code.id','=','se_progress_note.diagnosis_type')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('se_progress_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->service_category;
    //                 // $diagnosis_id=$diagnosis['diagnosis_type'];
    //                 $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->additional_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_type!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_type!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //             }else if (PatientIndexForm::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL patient_index_form(' . $id . ')');
    //                 // $diagnosis = PatientIndexForm::select('icd_code.icd_code','icd_code.icd_name','diagnosis','additional_diagnosis','code_id','sub_code_id',
    //                 // 'additional_code_id','additional_subcode','outcome','category_of_services')
    //                 // ->leftjoin('icd_code','icd_code.id','=','patient_index_form.diagnosis')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('patient_index_form.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->category_of_services;
    //                 // $diagnosis_id=$diagnosis['diagnosis'];
    //                 $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->additional_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //              }else if (CounsellingProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL counselling_progress_note(' . $id . ')');
    //                 // $diagnosis = CounsellingProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_id','additional_diagnosis','code_id',
    //                 // 'sub_code_id','additional_code_id','additional_subcode','outcome_id','category_services')
    //                 // ->leftjoin('icd_code','icd_code.id','=','counselling_progress_note.diagnosis_id')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('counselling_progress_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome_id;
    //                 $category_services=$diagnosis[0]->category_services;
    //                 // $diagnosis_id=$diagnosis['diagnosis_id'];
    //                 $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->additional_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_id!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_id!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //             }else if (EtpProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) { //sambung sini
    //                 $diagnosis=DB::select('CALL etp_progress_note(' . $id . ')');
    //                 // $diagnosis = EtpProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_type','additional_diagnosis','code_id',
    //                 // 'sub_code_id','additional_code_id','additional_subcode','outcome','service_category')
    //                 // ->leftjoin('icd_code','icd_code.id','=','etp_progress_note.diagnosis_type')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('etp_progress_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->service_category;
    //                 // $diagnosis_id=$diagnosis['diagnosis_type'];
    //                 $additional_diagnosis=$diagnosis[0]->additional_diagnosis;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->additional_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->additional_subcode;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_type!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_type!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //             }else if (JobClubProgressNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL job_club_progress_note(' . $id . ')');
    //                 // $diagnosis = JobClubProgressNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_type','add_diagnosis_type','code_id',
    //                 // 'sub_code_id','add_code_id','add_sub_code_id','outcome','service_category')
    //                 // ->leftjoin('icd_code','icd_code.id','=','job_club_progress_note.diagnosis_type')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('job_club_progress_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->service_category;
    //                 // $diagnosis_id=$diagnosis['diagnosis_type'];
    //                 $additional_diagnosis=$diagnosis[0]->add_diagnosis_type;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->add_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->add_sub_code_id;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_type!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_type!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //              }else if (ConsultationDischargeNote::where('appointment_details_id', $id)->where('status','1')->exists()) {  
    //                 $diagnosis=DB::select('CALL consultation_discharge_note(' . $id . ')');
    //                 // $diagnosis = ConsultationDischargeNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_id','add_type_diagnosis_id','code_id',
    //                 // 'sub_code_id','add_code_id','add_sub_code_id','outcome','category_services')
    //                 // ->leftjoin('icd_code','icd_code.id','=','consultation_discharge_note.diagnosis_id')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('consultation_discharge_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->category_services;
    //                 // $diagnosis_id=$diagnosis['diagnosis_id'];
    //                 $additional_diagnosis=$diagnosis[0]->add_type_diagnosis_id;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->add_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->add_sub_code_id;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_id!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_id!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //             }else if (RehabDischargeNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL rehab_discharge_note(' . $id . ')');
    //                 // $diagnosis = RehabDischargeNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis_id','add_diagnosis_type','code_id','sub_code_id',
    //                 // 'add_code_id','add_sub_code_id','outcome','service_category')
    //                 // ->leftjoin('icd_code','icd_code.id','=','rehab_discharge_note.diagnosis_id')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('rehab_discharge_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->service_category;
    //                 // $diagnosis_id=$diagnosis['diagnosis_id'];
    //                 $additional_diagnosis=$diagnosis[0]->add_diagnosis_type;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->add_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->add_sub_code_id;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis_id!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis_id!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }

    //             }else if (CpsDischargeNote::where('appointment_details_id', $id)->where('status','1')->exists()) {
    //                 $diagnosis=DB::select('CALL cps_discharge_note(' . $id . ')');
    //                 // $diagnosis = CpsDischargeNote::select('icd_code.icd_code','icd_code.icd_name','diagnosis','add_diagnosis_type','code_id','sub_code_id',
    //                 // 'add_code_id','add_sub_code_id','outcome','service_category')
    //                 // ->leftjoin('icd_code','icd_code.id','=','cps_discharge_note.diagnosis')
    //                 // ->where('appointment_details_id', $id)
    //                 // ->where('cps_discharge_note.status','=','1')->first();
    //                 $outcome=$diagnosis[0]->outcome;
    //                 $category_services=$diagnosis[0]->service_category;
    //                 // $diagnosis_id=$diagnosis['diagnosis'];
    //                 $additional_diagnosis=$diagnosis[0]->add_diagnosis_type;
    //                 $code_id=$diagnosis[0]->code_id;
    //                 $sub_code_id=$diagnosis[0]->sub_code_id;
    //                 $add_code_id=$diagnosis[0]->add_code_id;
    //                 $add_sub_code_id=$diagnosis[0]->add_sub_code_id;
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                     if( $diagnosis[0]->diagnosis!=$request->diagnosis_id ){
    //                         continue;
    //                     }
    //                 }
    //                 if($diagnosis[0]->diagnosis!=0){
    //                     $icd_code=$diagnosis[0]->icd_code;
    //                     $icd_name=$diagnosis[0]->icd_name; //dah
    //                 }else{
    //                     $icd_code='-';
    //                     $icd_name='-'; //dah
    //                 }
    //             }else{

    //                 //$result[$index]['diagnosis']='-'; //untuk filter sahaja
    //                 if($request->diagnosis_id!=NULL && $request->diagnosis_id!='0'){
    //                         continue;

    //                 }
    //                  $outcome='-';
    //                  $category_services='-';
    //                  $additional_diagnosis='-';
    //                  $code_id='-';
    //                  $sub_code_id='-';
    //                  $add_code_id='-';
    //                  $icd_code='-';
    //                  $icd_name='-'; //dah
    //                  $add_sub_code_id='-';
    //              }
    //             ////////////////////////////////////////////for additional diagnosis/////////////////////////////////////////////////
    //             if($additional_diagnosis!=NULL && $additional_diagnosis!='0' && $additional_diagnosis!='-'){
    //                 $e=0;
    //                 $additional_diagnosis_id=[];
    //                     foreach (explode(',',$additional_diagnosis) as $add) {
    //                             if($add!='0'|| $add!=NULL){
    //                                 // $add_diagnosis = IcdCode::select('icd_code','icd_name')->where('id', $add)->first();
    //                                 $add_diagnosis=DB::select('CALL icd_code(' . $add . ')');

    //                         // if($add_diagnosis){
                               
    //                         // }else{
    //                         //     dd($ssh[$index]->id);
    //                         // }
    //                                 $additional_diagnosis_id[$e]['additional_diagnosis']=$add_diagnosis[0]->icd_code.' '.$add_diagnosis[0]->icd_name;
    //                             }else{                                   
    //                                 $additional_diagnosis_id[$e]['additional_diagnosis']='-';
    //                             }
    //                         $e++;
    //                     }
    //                     if($e==1){
    //                         $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis2']='-';
    //                         $result[$index]['additional_diagnosis3']='-';
    //                         $result[$index]['additional_diagnosis4']='-';
    //                         $result[$index]['additional_diagnosis5']='-';
    //                     }else if($e==2){
    //                         $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis3']='-';
    //                         $result[$index]['additional_diagnosis4']='-';
    //                         $result[$index]['additional_diagnosis5']='-';
    //                     }else if($e==3){
    //                         $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis4']='-';
    //                         $result[$index]['additional_diagnosis5']='-';
    //                     }else if($e==4){
    //                         $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis4']=$additional_diagnosis_id[3]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis5']='-';
    //                     }else if($e==5){
    //                         $result[$index]['additional_diagnosis1']=$additional_diagnosis_id[0]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis2']=$additional_diagnosis_id[1]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis3']=$additional_diagnosis_id[2]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis4']=$additional_diagnosis_id[3]['additional_diagnosis'];
    //                         $result[$index]['additional_diagnosis5']=$additional_diagnosis_id[4]['additional_diagnosis'];
    //                     } 
    //             }else{
    //                 $result[$index]['additional_diagnosis1']='-';
    //                 $result[$index]['additional_diagnosis2']='-';
    //                 $result[$index]['additional_diagnosis3']='-';
    //                 $result[$index]['additional_diagnosis4']='-';
    //                 $result[$index]['additional_diagnosis5']='-';
    //             }

    //             ///////////////////////////for code and additional code/////////////////////////////////////

    //             if($add_code_id!=NULL && $add_code_id!='0' && $add_code_id!='-'){
    //                 // $add_code_id = IcdCode::select('icd_code','icd_name')->where('id', $add_code_id)->first();
    //                 $add_code_id=DB::select('CALL icd_code(' . $add_code_id . ')');
    //                 $result[$index]['add_code_id']=$add_code_id[0]->icd_code.''.$add_code_id[0]->icd_name;
    //             }else{
    //                 $result[$index]['add_code_id']='-';
    //             }

    //                             ///////////////////////////for sub code///////////////////////////////////////////
    //             if($sub_code_id!=NULL && $sub_code_id!='0' && $add_code_id!='-'){
    //                 $f=0;
    //                 $sub_code_id_array=[];
    //                     foreach (explode(',',$sub_code_id) as $sub) {
    //                                 if($sub!=NULL && $sub!='0'){
    //                                     // $sub_code = IcdCode::select('icd_code','icd_name')->where('id', $sub)->first();
    //                                     $sub_code=DB::select('CALL icd_code(' . $sub . ')');
    //                                     //$sub_code = $sub_code->jsonserialize();
    //                                     $sub_code_id_array[$f]['sub_code']=$sub_code[0]->icd_code.' '.$sub_code[0]->icd_name;
    //                                 }else{
    //                                     $sub_code_id_array[$f]['sub_code']='-';
    //                                     }
    //                                 $f++;
    //                             }
    //                     if($f==1){
    //                         $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
    //                         $result[$index]['sub_code2']='-';
    //                         $result[$index]['sub_code3']='-';
    //                         $result[$index]['sub_code4']='-';
    //                         $result[$index]['sub_code5']='-';
    //                     }else if($f==2){
    //                         // if(isset($sub_code_id_array[0]['sub_code'])){
                               
    //                         // }else{
    //                         //     dd($ssh[$index]->id);
    //                         // }
    //                         $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
    //                         $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
    //                         $result[$index]['sub_code3']='-';
    //                         $result[$index]['sub_code4']='-';
    //                         $result[$index]['sub_code5']='-';
    //                     }else if($f==3){
    //                         $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
    //                         $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
    //                         $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
    //                         $result[$index]['sub_code4']='-';
    //                         $result[$index]['sub_code5']='-';
    //                     }else if($f==4){
    //                         $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
    //                         $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
    //                         $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
    //                         $result[$index]['sub_code4']=$sub_code_id_array[3]['sub_code'];
    //                         $result[$index]['sub_code5']='-';
    //                     }else if($f==5){
    //                         $result[$index]['sub_code1']=$sub_code_id_array[0]['sub_code'];
    //                         $result[$index]['sub_code2']=$sub_code_id_array[1]['sub_code'];
    //                         $result[$index]['sub_code3']=$sub_code_id_array[2]['sub_code'];
    //                         $result[$index]['sub_code4']=$sub_code_id_array[3]['sub_code'];
    //                         $result[$index]['sub_code5']=$sub_code_id_array[4]['sub_code'];
    //                     }
    //             }else{
    //                 $result[$index]['sub_code1']='-';
    //                 $result[$index]['sub_code2']='-';
    //                 $result[$index]['sub_code3']='-';
    //                 $result[$index]['sub_code4']='-';
    //                 $result[$index]['sub_code5']='-';
    //             }

    //                         ///////////////////////////for additional sub code/////////////////////////////////////
    //         if($add_sub_code_id!=NULL && $add_sub_code_id!='0' && $add_code_id!='-'){
    //             $f=0;
    //             $add_sub_code_val=[];
    //                 foreach (explode(',',$add_sub_code_id) as $add_sub) {
    //                                 if($add_sub!=NULL && $add_sub!='0'){
    //                                 // $add_sub_code = IcdCode::select('icd_code','icd_name')->where('id', $add_sub)->first();
    //                                 $add_sub_code=DB::select('CALL icd_code(' . $add_sub . ')');
    //                                // $add_sub_code = $add_sub_code->jsonserialize();
    //                                 $add_sub_code_val[$f]['add_sub_code']=$add_sub_code[0]->icd_code.' '.$add_sub_code[0]->icd_name;
    //                                 }else{
    //                                 $add_sub_code_val[$f]['add_sub_code']='-';
    //                                 }
    //                         }$f++;
    //                 if($f==1){
    //                     $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
    //                     $result[$index]['add_sub_code2']='-';
    //                     $result[$index]['add_sub_code3']='-';
    //                     $result[$index]['add_sub_code4']='-';
    //                     $result[$index]['add_sub_code5']='-';
    //                 }else if($f==2){
    //                     $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
    //                     $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
    //                     $result[$index]['add_sub_code3']='-';
    //                     $result[$index]['add_sub_code4']='-';
    //                     $result[$index]['add_sub_code5']='-';
    //                 }else if($f==3){
    //                     $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
    //                     $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
    //                     $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
    //                     $result[$index]['add_sub_code4']='-';
    //                     $result[$index]['add_sub_code5']='-';
    //                 }else if($f==4){
    //                     $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
    //                     $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
    //                     $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
    //                     $result[$index]['add_sub_code4']=$add_sub_code_val[3]['add_sub_code'];
    //                     $result[$index]['add_sub_code5']='-';
    //                 }else if($f==5){
    //                     $result[$index]['add_sub_code1']=$add_sub_code_val[0]['add_sub_code'];
    //                     $result[$index]['add_sub_code2']=$add_sub_code_val[1]['add_sub_code'];
    //                     $result[$index]['add_sub_code3']=$add_sub_code_val[2]['add_sub_code'];
    //                     $result[$index]['add_sub_code4']=$add_sub_code_val[3]['add_sub_code'];
    //                     $result[$index]['add_sub_code5']=$add_sub_code_val[4]['add_sub_code'];
    //                 }
    //         }else{
    //             $result[$index]['add_sub_code1']='-';
    //             $result[$index]['add_sub_code2']='-';
    //             $result[$index]['add_sub_code3']='-';
    //             $result[$index]['add_sub_code4']='-';
    //             $result[$index]['add_sub_code5']='-';
    //         }


    //         if($outcome!=NULL && $outcome!='0' && $outcome!='-'){
    //             $outcome_q = GeneralSetting::where('id',$outcome)->first();
    //             // $outcome_q=DB::select('CALL general_setting(' . $outcome . ')');
    //             $outcome_value=$outcome_q['section_value'];
    //         }else{
    //             $outcome_value='-';
    //         }
            



    //         $result[$index]['No']=$index+1;
    //         $result[$index]['Registration_date'] = date('d/m/Y', strtotime($v['created_at']));
    //         $result[$index]['Registration_Time'] = date('h:i:s A', strtotime($v['created_at']));
    //         if($v['nric_no']==NULL){
    //             $result[$index]['nric_no'] = $v['passport_no'];
    //         }else{
    //             $result[$index]['nric_no'] = $v['nric_no'];
    //         }
    //         $result[$index]['Name'] = $v['name_asin_nric'];
    //         $result[$index]['Attendance_status'] = $v['appointment_status'];
    //         $result[$index]['ADDRESS'] = $v['address1'] . ' ' . $v['address2'] . ' ' . $v['address3'];
    //         $result[$index]['CITY'] = $v['city_name'];
    //         $result[$index]['STATE'] = $v['state_name'];
    //         $result[$index]['POSTCODE'] = $v['postcode'];
    //         $result[$index]['PHONE_NUMBER'] = $v['mobile_no'];
    //         $result[$index]['DATE_OF_BIRTH'] = $v['birth_date'];
    //         $result[$index]['AGE'] = $v['age'];
    //         $result[$index]['citizenship'] = $v['citizenship'];
    //         $result[$index]['race'] = $v['race'];
    //         $result[$index]['religion'] = $v['religion'];
    //         $result[$index]['marital'] = $v['marital'];
    //         $result[$index]['accomodation'] = $v['accomodation'];
    //         $result[$index]['education_level'] = $v['education_level'];
    //         $result[$index]['occupation_status'] = $v['occupation_status'];
    //         $result[$index]['fee_exemption_status'] = $v['fee_exemption_status'];
    //         $result[$index]['occupation_sector'] = $v['occupation_sector'];
    //         $result[$index]['GENDER'] = $v['sex'];
    //         $result[$index]['APPOINTMENT_TYPE'] = $v['service_name'];
    //         $result[$index]['DIAGNOSIS'] = $icd_name;
    //         $result[$index]['DIAGNOSIS_CODE'] = $icd_code;
    //         $result[$index]['CATEGORY_OF_PATIENTS'] = $v['patient_category'];
    //         $result[$index]['TYPE_OF_Visit'] = $v['type_visit'];
    //         $result[$index]['TYPE_OF_Refferal'] = $v['type_referral'] ;
    //         $result[$index]['Attending_staff'] = $v['staff_name'];
    //         $result[$index]['outcome'] = $outcome_value;
    //         $result[$index]['category_of_services'] = $category_services;

    //         $index++;
    //         }


    //     }
    //     //dd($result); //confirmkan status and data yang amik based on table2 cd yang lain. takut nama column lain. and check either data is correct
    //     if ($result) {
    //         $totalReports = count($result);
    //         $filePath = '';
    //         if (isset($request->report_type) && $request->report_type == 'excel') {
    //             $filename = 'GeneralReport-'.time().'.xls';
    //               return response([
    //                 'message' => 'Data successfully retrieved.',
    //                 'result' => $result,
    //                 'header' => 'General Report '.$request->month.' '.$request->year,
    //                 'filename' => $filename,
    //                 'code' => 200]);
    //         } else {
    //             $filename = 'GeneralReport-'.time().'.pdf';
    //             return response()->json(["message" => "General Report", 'result' => $result, 'filename' => $filename, "code" => 200]);
    //         }
    //     } else {
    //         return response()->json(["message" => "General Report", 'result' => [], 'filename' => null, "code" => 200]);
    //     }
    // }
   

    public function getKPIReport(Request $request)
    {

        $month = ['January' => 1, 'February' => 2, 'March' => 3, 'April' => 4, 'May' => 5, 'June' => 6, 'July' => 7,
        'August' => 8, 'September' => 9, 'October' => 10, 'November' => 11, 'December' => 12];
        $year=$request->year;
        $fromMonth=$month[$request->fromMonth];
        $toMonth=$month[$request->toMonth];

        if ($request->report_type == 'excel') { //If EXCEL
        for ($m=$fromMonth; $m<=$toMonth; $m++){

            $month_array=[];
        if(!in_array($m, $month_array, true)){
            array_push($month_array, $m);

            $averageResult['average'][$m]=0;

            }
        }
        $users = DB::table('staff_management')
            ->select('roles.code','roles.role_name')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->where('staff_management.email', '=', $request->email)
            ->first();
            $users2  = json_decode(json_encode($users), true);
        $index=0;

        if($users2['code']=='superadmin' || $users2['code']=='high level'){
            if($request->hospital!=NULL){
                        $branchName=HospitalBranchManagement::where('id', '=', $request->hospital)->get()->toArray();

            } else if($request->state!=NULL){
                        $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')
                        ->where('branch_state','=', $request->state)->get()->toArray();
            }else{
                $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')->get()->toArray();
            }

        }else{
            $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')->where('id',$request->branch_id)->get()->toArray();
        }

        $totalBranch=count($branchName);

        foreach($branchName as $v =>$val){

        for ($m=$fromMonth; $m<=$toMonth; $m++){

            $a1 = DB::table('job_start_form as jsf')
            ->select('jsf.id','jsf.patient_id')
            ->join('patient_registration as p', function($join) {
                $join->on('jsf.patient_id', '=', 'p.id');
            })
            ->whereYear('jsf.updated_at', '=', $request->year)
            ->whereMonth('jsf.updated_at', '=', $m)
            ->where('jsf.status', '=', '1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('jsf.patient_id');

            if($request->state!=NULL){
                $a1->where('p.state_id','=', $request->state);
            }

            $a2=$a1->get()->toArray();
            $a  = json_decode(json_encode($a2), true);

            $mainResult['group_name'][$val['hospital_branch_name']][$m]['a'] = count($a);

            // $testing = DB::table(DB::raw('testing as t1'))
            //             ->join(DB::raw('(SELECT patient_id,MAX(id) AS id FROM testing GROUP BY patient_id) as t2'),
            //             function($query){
            //                 $query->on('t1.patient_id','=','t2.patient_id')
            //                       ->on('t1.id','=','t2.id');
            //             })
            //             ->where('status','=','employed')->get();

            $b1 = DB::table('se_progress_note as spn')
            ->select('spn.id','spn.patient_mrn_id')
            ->join('general_setting as gs', function($join) {
                $join->on('gs.id', '=', 'spn.employment_status');
            })
            ->join('patient_registration as p', function($join) {
                $join->on('p.id', '=', 'spn.patient_mrn_id');
            })
            ->whereYear('spn.updated_at', '=', $request->year)
            ->whereMonth('spn.updated_at', '=', $m)
            ->where('gs.section_value','=','Employed')
            ->where('spn.status', '=', '1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('spn.patient_mrn_id');


            if($request->state!=NULL){
                $b1->where('p.state_id','=', $request->state);
            }


            $b2=$b1->get()->toArray();
            $b  = json_decode(json_encode($b2), true);
            $mainResult['group_name'][$val['hospital_branch_name']][$m]['b'] = count($b);

            if($b!=Null && $a!=Null){
                foreach($a as $k =>$y){
                    foreach($b as $v => $z){
                        if($y['patient_id']==$z['patient_mrn_id']){
                            $mainResult['group_name'][$val['hospital_branch_name']][$m]['b'] -= 1;
                        }
                    }
                }
            }

            $c1 = DB::table('se_progress_note as spn')
            ->select('spn.id','spn.patient_mrn_id')
            ->join('patient_registration as p', function($join) {
                $join->on('p.id', '=', 'spn.patient_mrn_id');
            })
            ->whereYear('spn.updated_at', '=', $request->year)
            ->whereMonth('spn.updated_at', '=', $m)
            ->where('spn.status', '=','1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('spn.patient_mrn_id');

            if($request->state!=NULL){
                $c1->where('p.state_id','=', $request->state);
            }


            $c2=$c1->get()->toArray();
            $c  = json_decode(json_encode($c2), true);

            $mainResult['group_name'][$val['hospital_branch_name']][$m]['c'] = count($c);

            if($c!=Null && $a!=Null){
                foreach($a as $k =>$y){
                    foreach($c as $v => $z){
                        if($y['patient_id']==$z['patient_mrn_id']){
                            $mainResult['group_name'][$val['hospital_branch_name']][$m]['c'] -= 1;
                        }
                    }
                }
            }

            $mainResult['group_name'][$val['hospital_branch_name']][$m]['c'] += $mainResult['group_name'][$val['hospital_branch_name']][$m]['a'];


            // $d1 = DB::table('se_progress_note as spn')
            // ->select('spn.id','spn.patient_mrn_id')
            // ->join('general_setting as gs', function($join) {
            //     $join->on('gs.id', '=', 'spn.employment_status');
            // })
            // ->join('patient_registration as p', function($join) {
            //     $join->on('p.id', '=', 'spn.patient_mrn_id');
            // })
            // ->whereYear('spn.updated_at', '=', $request->year)
            // ->whereMonth('spn.updated_at', '=', $m)
            // ->where('gs.section_value','!=','Employed')
            // ->where('spn.status', '=', '1')
            // ->where('p.branch_id',$val['id'])
            // ->GroupBy('spn.patient_mrn_id');

            // if($request->state!=NULL){
            //     $d1->where('p.state_id','=', $request->state);
            // }

            // $d2=$d1->get()->toArray();
            // $d  = json_decode(json_encode($d2), true);
            // $mainResult['group_name'][$val['hospital_branch_name']][$m]['d'] = count($d);


        if((count($a)!=0 || count($b)!=0) && count($c)!=0){
            $kpi=(($mainResult['group_name'][$val['hospital_branch_name']][$m]['a']+$mainResult['group_name'][$val['hospital_branch_name']][$m]['b'])
            /$mainResult['group_name'][$val['hospital_branch_name']][$m]['c'])*(100);
            $mainResult['group_name'][$val['hospital_branch_name']][$m]['kpi'] = number_format($kpi,2);
            $averagekpi[$index]=number_format($kpi,2);


        }else{
            $mainResult['group_name'][$val['hospital_branch_name']][$m]['kpi'] = 0;
            $averagekpi[$index]=0;
        }

        $averageResult['average'][$m] += $averagekpi[$index];
        $index++;
    }
        }
        for ($m=$fromMonth; $m<=$toMonth; $m++){


            $averageResult['average'][$m]= number_format(($averageResult['average'][$m]/$totalBranch),2);


        }
    }else{//IF  PDF

        for ($m=$fromMonth; $m<=$toMonth; $m++){

            $month_array=[];
        if(!in_array($m, $month_array, true)){
            array_push($month_array, $m);

            $averageResult['average'][$m]=0;

            }
        }

        $users = DB::table('staff_management')
            ->select('roles.code')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->where('staff_management.email', '=', $request->email)
            ->first();
            $users2  = json_decode(json_encode($users), true);
        $index=0;
        if($users2['code']=='superadmin' || $users2['code']='high level'){
            if($request->hospital!=NULL){
                        $branchName=HospitalBranchManagement::where('id', '=', $request->hospital)->get()->toArray();

            } else if($request->state!=NULL){
                        $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')
                        ->where('branch_state','=', $request->state)->get()->toArray();
            }else{
                $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')->get()->toArray();
            }

        }else{
            $branchName=HospitalBranchManagement::where('hospital_branch_name', 'LIKE', '%mentari%')->where('id',$request->branch_id)->get()->toArray();
        }

        $totalBranch=count($branchName);

        foreach($branchName as $v =>$val){

        for ($m=$fromMonth; $m<=$toMonth; $m++){

            $a1 = DB::table('job_start_form as jsf')
            ->select('jsf.id','jsf.patient_id')
            ->join('patient_registration as p', function($join) {
                $join->on('jsf.patient_id', '=', 'p.id');
            })
            ->whereYear('jsf.updated_at', '=', $request->year)
            ->whereMonth('jsf.updated_at', '=', $m)
            ->where('jsf.status', '=', '1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('jsf.patient_id');

            if($request->state!=NULL){
                $a1->where('p.state_id','=', $request->state);
            }

            $a2=$a1->get()->toArray();
            $a  = json_decode(json_encode($a2), true);

            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['a'] = count($a);


            $b1 = DB::table('se_progress_note as spn')
            ->select('spn.id','spn.patient_mrn_id')
            ->join('general_setting as gs', function($join) {
                $join->on('gs.id', '=', 'spn.employment_status');
            })
            ->join('patient_registration as p', function($join) {
                $join->on('p.id', '=', 'spn.patient_mrn_id');
            })
            ->whereYear('spn.updated_at', '=', $request->year)
            ->whereMonth('spn.updated_at', '=', $m)
            ->where('gs.section_value','=','Employed')
            ->where('spn.status', '=', '1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('spn.patient_mrn_id');

            if($request->state!=NULL){
                $b1->where('p.state_id','=', $request->state);
            }


            $b2=$b1->get()->toArray();
            $b  = json_decode(json_encode($b2), true);
            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['b'] = count($b);

            if($b!=Null && $a!=Null){
                foreach($a as $k =>$y){
                    foreach($b as $v => $z){

                        if($y['patient_id']==$z['patient_mrn_id']){
                            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['b'] -= 1;
                        }
                    }
                }
            }

            $c1 = DB::table('se_progress_note as spn')
            ->select('spn.id','patient_mrn_id')
            ->join('patient_registration as p', function($join) {
                $join->on('p.id', '=', 'spn.patient_mrn_id');
            })
            ->whereYear('spn.updated_at', '=', $request->year)
            ->whereMonth('spn.updated_at', '=', $m)
            ->where('spn.status', '=','1')
            ->where('p.branch_id',$val['id'])
            ->GroupBy('spn.patient_mrn_id');

            if($request->state!=NULL){
                $c1->where('p.state_id','=', $request->state);
            }


            $c2=$c1->get()->toArray();
            $c  = json_decode(json_encode($c2), true);

            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['c'] = count($c);

            if($c!=Null && $a!=Null){
                foreach($a as $k =>$y){
                    foreach($c as $v => $z){
                        if($y['patient_id']==$z['patient_mrn_id']){
                            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['c'] -= 1;
                        }
                    }
                }
            }

            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['c'] += $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['a'];

            // $d1 = DB::table('se_progress_note as spn')
            // ->select('spn.id','spn.patient_mrn_id')
            // ->join('general_setting as gs', function($join) {
            //     $join->on('gs.id', '=', 'spn.employment_status');
            // })
            // ->join('patient_registration as p', function($join) {
            //     $join->on('p.id', '=', 'spn.patient_mrn_id');
            // })
            // ->whereYear('spn.updated_at', '=', $request->year)
            // ->whereMonth('spn.updated_at', '=', $m)
            // ->where('gs.section_value','!=','Employed')
            // ->where('spn.status', '=', '1')
            // ->where('p.branch_id',$val['id'])
            // ->GroupBy('spn.patient_mrn_id');

            // if($request->state!=NULL){
            //     $d1->where('p.state_id','=', $request->state);
            // }

            // $d2=$d1->get()->toArray();
            // $d  = json_decode(json_encode($d2), true);
            // $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['d'] = count($d);


        if((count($a)!=0 || count($b)!=0) && count($c)!=0){
            $kpi=(($mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['a']+$mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['b'])
            /$mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['c'])*(100);
            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['kpi'] = number_format($kpi,2);
            $averagekpi[$index]=number_format($kpi,2);


        }else{
            $mainResult[0]['group_name'][$val['hospital_branch_name']][$m]['kpi'] = 0;
            $averagekpi[$index]=0;
        }

        $averageResult['average'][$m] += $averagekpi[$index];
        $index++;
    }
        }
        for ($m=$fromMonth; $m<=$toMonth; $m++){


            $averageResult['average'][$m]= number_format(($averageResult['average'][$m]/$totalBranch),2);


        }

    }


        if ($mainResult) {
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '86400',
                'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
            ];
            $filePath = '';
            if ($request->report_type == 'excel') {
                $filename = 'kpi-report-rg-'.time() . '.xlsx';
                $filePath = 'downloads/report/'.$filename;
                $KPIExcel = Excel::store(new KPIReportExport($mainResult,$averageResult,$year), $filePath, 'public');
                $pathToFile = Storage::url($filePath);
                return response()->json(["message" => "KPI Report", 'result' => $mainResult, 'averageResult'=>$averageResult, 'year'=>$year ,'filepath' => env('APP_URL') . $pathToFile, "code" => 200]);
            } else {

                return response()->json(["message" => "KPI Report", 'result' => $mainResult, 'averageResult'=>$averageResult, 'year'=>$year , "code" => 200]);

            }

        }return response()->json(["message" => "KPI Report", 'result' => [], 'filepath' => null, "code" => 200]);
    }
}
