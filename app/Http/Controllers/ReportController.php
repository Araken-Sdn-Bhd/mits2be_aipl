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


                $query = DB::table('sharp_registraion_final_step as srfs')
                ->select('srfs.id','srfs.risk','srfs.protective','srfs.self_harm','srfs.patient_id',
                'p.name_asin_nric','p.address1','p.city_id','p.nric_no','p.state_id','p.postcode',
                'p.mobile_no','p.birth_date','srfs.harm_date','srfs.harm_time','psrdp.hospital_name',
                'psrhm.id as id2','psrhm.main_psychiatric_diagnosis','psrhm.additional_diagnosis')
                ->leftjoin('patient_registration as p', function($join) {
                    $join->on('srfs.patient_id', '=', 'p.id');
                })
                ->leftjoin('patient_shharp_registration_hospital_management as psrhm', function($join) {
                    $join->on('psrhm.id', '=', 'srfs.hospital_mgmt');
                })
                ->leftjoin('patient_shharp_registration_data_producer as psrdp', function($join) {
                    $join->on('psrdp.patient_mrn_id', '=', 'srfs.patient_id');
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
             dd($result);
                
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
        
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate])
        ->where('appointment_status','!=',0);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $ssh = $appointments->where('appointment_type', $request->appointment_type);

        $ssh = $appointments->get()->toArray();

        $demo = [];
        $age=[];
        $result = [];
        $cd_array=[];
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

        if ($ssh) {
            $index = 0;
            foreach ($ssh as $k => $v) {
                $notes = [];
                $icd=NULL;
                $cd_array=[];
                $count=0;

                $query_diagnosis1 = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                ->where('status','=','1');

                    $diagnosis1=$query_diagnosis1->orderBy('id', 'DESC')->first();

                    if($diagnosis1!=NULL){
                        $diagnosis1_ts=strtotime($diagnosis1['updated_at']);
                        $cd_array[$count]['updated_at']=$diagnosis1_ts;
                        $cd_array[$count]['diagnosis_id']=$diagnosis1['diagnosis_id'];
                        $count++;
                    }
                
                $query_diagnosis2 = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                ->where('status','=','1');


                        $diagnosis2=$query_diagnosis2->orderBy('id', 'DESC')->first();

                    if($diagnosis2!=NULL){
                            $diagnosis2_ts=strtotime($diagnosis2['updated_at']);
                            $cd_array[$count]['updated_at']=$diagnosis2_ts;
                            $cd_array[$count]['diagnosis_id']=$diagnosis2['diagnosis_id'];
                            $count++;
                       }


                    $query_diagnosis3 = PsychiatricProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
       

                            $diagnosis3=$query_diagnosis3->orderBy('id', 'DESC')->first();
       
                        if($diagnosis3!=NULL){
                                $diagnosis3_ts=strtotime($diagnosis3['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis3_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis3['diagnosis'];
                                $count++;
                        }

                    $query_diagnosis4 = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');

                            $diagnosis4=$query_diagnosis4->orderBy('id', 'DESC')->first();
                        if($diagnosis4!=NULL){
                            $diagnosis4_ts=strtotime($diagnosis4['updated_at']);
                            $cd_array[$count]['updated_at']=$diagnosis4_ts;
                            $cd_array[$count]['diagnosis_id']=$diagnosis4['diagnosis_type'];
                            $count++;
                        }

                        $query_diagnosis5 = SeProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
              

                            $diagnosis5=$query_diagnosis5->orderBy('id', 'DESC')->first();
              
                                if($diagnosis5!=NULL){
                                    $diagnosis5_ts=strtotime($diagnosis5['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis5_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis5['diagnosis_type'];
                                    $count++;
                                }

                        $query_diagnosis6 = PatientIndexForm::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
              

                            $diagnosis6=$query_diagnosis6->orderBy('id', 'DESC')->first();
              
                                if($diagnosis6!=NULL){
                                    $diagnosis6_ts=strtotime($diagnosis6['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis6_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis6['diagnosis'];
                                    $count++;
                                }

                $query_diagnosis7 = CounsellingProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                ->where('status','=','1');

                    $diagnosis7=$query_diagnosis7->orderBy('id', 'DESC')->first();

                    if($diagnosis7!=NULL){
                        $diagnosis7_ts=strtotime($diagnosis7['updated_at']);
                        $cd_array[$count]['updated_at']=$diagnosis7_ts;
                        $cd_array[$count]['diagnosis_id']=$diagnosis7['diagnosis_id'];
                        $count++;
                    }
                
                $query_diagnosis8 = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                ->where('status','=','1');

                        $diagnosis8=$query_diagnosis8->orderBy('id', 'DESC')->first();

                    if($diagnosis8!=NULL){
                            $diagnosis8_ts=strtotime($diagnosis8['updated_at']);
                            $cd_array[$count]['updated_at']=$diagnosis8_ts;
                            $cd_array[$count]['diagnosis_id']=$diagnosis8['diagnosis_type'];
                            $count++;
                       }


                    $query_diagnosis9 = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
       

                            $diagnosis9=$query_diagnosis9->orderBy('id', 'DESC')->first();
       
                        if($diagnosis9!=NULL){
                                $diagnosis9_ts=strtotime($diagnosis9['updated_at']);
                                $cd_array[$count]['updated_at']=$diagnosis9_ts;
                                $cd_array[$count]['diagnosis_id']=$diagnosis9['diagnosis_type'];
                                $count++;
                        }

                    $query_diagnosis10 = ConsultationDischargeNote::where('patient_id', $v['patient_mrn_id'])
                    ->where('status','=','1');
              

                            $diagnosis10=$query_diagnosis10->orderBy('id', 'DESC')->first();
              
                        if($diagnosis10!=NULL){
                            $diagnosis10_ts=strtotime($diagnosis10['updated_at']);
                            $cd_array[$count]['updated_at']=$diagnosis10_ts;
                            $cd_array[$count]['diagnosis_id']=$diagnosis10['diagnosis_id'];
                            $count++;
                        }

                        $query_diagnosis11 = RehabDischargeNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');
              

                            $diagnosis11=$query_diagnosis11->orderBy('id', 'DESC')->first();
              
                                if($diagnosis11!=NULL){
                                    $diagnosis11_ts=strtotime($diagnosis11['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis11_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis11['diagnosis_id'];
                                    $count++;
                                }

                        $query_diagnosis12 = CpsDischargeNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('status','=','1');

                            $diagnosis12=$query_diagnosis12->orderBy('id', 'DESC')->first();
              
                                if($diagnosis12!=NULL){
                                    $diagnosis12_ts=strtotime($diagnosis12['updated_at']);
                                    $cd_array[$count]['updated_at']=$diagnosis12_ts;
                                    $cd_array[$count]['diagnosis_id']=$diagnosis12['diagnosis'];
                                    $count++;
                                }
                               
                                    
                                 
                if(!empty($cd_array)){
                                                                        
                        $Dates = array_map(fn($entry) => $entry['updated_at'], $cd_array);
                        $array_date=max($Dates); 
                                 
                                
                        foreach ($cd_array as $c => $d){
                            if($array_date==$d['updated_at']){
                                $icd=$d['diagnosis_id'];
                            }
                        
                        }
                }

                if($request->diagnosis_id!=NULL){
                    if($icd!=$request->diagnosis_id){
                        continue;
                    }
                }
                
                $icd = IcdCode::where('id', $icd)->first();

                $staff = StaffManagement::select('name')->where('id', $v['staff_id'])->get()->toArray();
                

                $users = DB::table('staff_management')
                ->select('roles.code')
                ->join('roles', 'staff_management.role_id', '=', 'roles.id')
                ->where('staff_management.email', '=', $request->email)
                ->first();
                $users2  = json_decode(json_encode($users), true);

                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if($users2['code']!='superadmin'){
                    $query->where('branch_id','=',$request->branch_id);
                }
                if ($demo)
                    $query->where($demo);
                if ($age)
                    if($age['agemin'] && $age['agemax']!=NULL){
                    $query->whereBetween('age',[$age['agemin'],$age['agemax']]);
                    }else if($age['agemin']==NULL) {
                        $query->where('age','<=',$age['agemax']);
                    }else if($age['agemax']==NULL) {
                        $query->where('age','>=',$age['agemin']);
                    }
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);

                $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = Postcode::where(['id' => $patientInfo['postcode']])->get()->toArray();
                    $st = State::where(['id' => $patientInfo['state_id']])->get()->toArray();
                    $sex = GeneralSetting::where(['id' => $patientInfo['sex']])->get()->toArray();
                    $citizenship = GeneralSetting::where(['id' => $patientInfo['citizenship']])->get()->toArray();
                    $race = GeneralSetting::where(['id' => $patientInfo['race_id']])->get()->toArray();
                    $religion = GeneralSetting::where(['id' => $patientInfo['religion_id']])->get()->toArray();
                    $marital = GeneralSetting::where(['id' => $patientInfo['marital_id']])->get()->toArray();
                    $accomodation = GeneralSetting::where(['id' => $patientInfo['accomodation_id']])->get()->toArray();
                    $education_level = GeneralSetting::where(['id' => $patientInfo['education_level']])->get()->toArray();
                    $fee_exemption_status = GeneralSetting::where(['id' => $patientInfo['fee_exemption_status']])->get()->toArray();

                    $occupation_status = GeneralSetting::where(['id' => $patientInfo['occupation_status']])->get()->toArray();
                    $occupation_sector = GeneralSetting::where(['id' => $patientInfo['occupation_sector']])->get()->toArray();
                    
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    $city_name = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $state_name = ($st) ? $st[0]['state_name'] : 'NA';
                    $postcode = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $gender = ($sex) ? $sex[0]['section_value'] : 'NA';
                    $citizenshipValue = ($citizenship) ? $citizenship[0]['section_value'] : 'NA';
                    $raceValue = ($race) ? $race[0]['section_value'] : 'NA';
                    $religionValue = ($religion) ? $religion[0]['section_value'] : 'NA';
                    $maritalValue = ($marital) ? $marital[0]['section_value'] : 'NA';
                    $accomodationValue = ($accomodation) ? $accomodation[0]['section_value'] : 'NA';
                    $education_levelValue = ($education_level) ? $education_level[0]['section_value'] : 'NA';
                    $occupation_statusValue = ($occupation_status) ? $occupation_status[0]['section_value'] : 'NA';
                    $fee_exemption_statusValue = ($fee_exemption_status) ? $fee_exemption_status[0]['section_value'] : 'NA';
                    $occupation_sectorValue = ($occupation_sector) ? $occupation_sector[0]['section_value'] : 'NA';
                    $apt = ServiceRegister::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = GeneralSetting::where('id', $v['type_visit'])->get()->toArray();
                    $cp = GeneralSetting::where('id', $v['patient_category'])->get()->toArray();
                    
                        

                    $appointment_type = ($apt) ? $apt[0]['service_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['section_value'] : 'NA';
                    $category = ($cp) ? $cp[0]['section_value'] : 'NA';
                    $result[$index]['No']=$index+1;
                    $result[$index]['Registration_date'] = date('d/m/Y', strtotime($patientInfo['created_at']));
                    $result[$index]['Registration_Time'] = date('h:i:s A', strtotime($patientInfo['created_at']));
                    if($patientInfo['nric_no']==NULL){
                        $result[$index]['nric_no'] = $patientInfo['passport_no'];
                    }else{
                        $result[$index]['nric_no'] = $patientInfo['nric_no'];
                    }
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['Attendance_status'] = $v['appointment_status'];
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['ADDRESS'] = $patientInfo['address1'] . ' ' . $patientInfo['address2'] . ' ' . $patientInfo['address3'];
                    $result[$index]['CITY'] = $city_name;
                    $result[$index]['STATE'] = $state_name;
                    $result[$index]['POSTCODE'] = $postcode;
                    $result[$index]['PHONE_NUMBER'] = $patientInfo['mobile_no'];
                    $result[$index]['DATE_OF_BIRTH'] = $patientInfo['birth_date'];
                    $result[$index]['AGE'] = $patientInfo['age'];
                    $result[$index]['citizenship'] = $citizenshipValue;
                    $result[$index]['race'] = $raceValue;
                    $result[$index]['religion'] = $religionValue;
                    $result[$index]['marital'] = $maritalValue;
                    $result[$index]['accomodation'] = $accomodationValue;
                    $result[$index]['education_level'] = $education_levelValue;
                    $result[$index]['occupation_status'] = $occupation_statusValue;
                    $result[$index]['fee_exemption_status'] = $fee_exemption_statusValue;
                    $result[$index]['occupation_sector'] = $occupation_sectorValue;
                    $result[$index]['GENDER'] = $gender;
                    $result[$index]['APPOINTMENT_TYPE'] = $appointment_type;
                    $result[$index]['DIAGNOSIS'] = ($icd) ? $icd['icd_name'] : 'NA';
                    $result[$index]['DIAGNOSIS_CODE'] = ($icd) ? $icd['icd_code'] : 'NA';
                    $result[$index]['CATEGORY_OF_PATIENTS'] = $category;
                    $result[$index]['TYPE_OF_Visit'] = $visit_type;
                    $result[$index]['TYPE_OF_Refferal'] = ($reftyp) ? $reftyp[0]['section_value'] : 'NA';
                    $result[$index]['Attending_staff'] = ($staff) ? $staff[0]['name'] : 'NA';
                    $result[$index]['outcome'] = '-';
                    $result[$index]['category_of_services'] = '-';
                    $index++;
                }
            }
        } 
        if ($result) {
            $totalReports = count($result);
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'GeneralReport-'.time().'.xls';
                  return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'header' => 'General Report from '.$request->fromDate.' To '.$request->toDate,
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
