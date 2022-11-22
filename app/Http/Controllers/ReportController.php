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


class ReportController extends Controller
{
    public function getSharpReport(Request $request)
    {


//             $patientArray = array_unique($patient);

//             $demo = [];
//             if ($request->name) {
//                 $demo['name_asin_nric'] = $request->name;
//             }
//             if ($request->citizenship) {
//                 $demo['citizenship'] = $request->citizenship;
//             }
//             if ($request->gender) {
//                 $demo['sex'] = $request->gender;
//             }
//             if ($request->race) {
//                 $demo['race_id'] = $request->race;
//             }
//             if ($request->religion) {
//                 $demo['religion_id'] = $request->religion;
//             }
//             if ($request->marital_status) {
//                 $demo['marital_id'] = $request->marital_status;
//             }
//             if ($request->accomodation) {
//                 $demo['accomodation_id'] = $request->accomodation;
//             }
//             if ($request->education_level) {
//                 $demo['education_level'] = $request->education_level;
//             }
//             if ($request->occupation_status) {
//                 $demo['occupation_status'] = $request->occupation_status;
//             }
//             if ($request->fee_exemption_status) {
//                 $demo['fee_exemption_status'] = $request->fee_exemption_status;
//             }
//             if ($request->occupation_sector) {
//                 $demo['occupation_sector'] = $request->occupation_sector;
//             }



                $query = DB::table('sharp_registraion_final_step as srfs')
                ->select('srfs.patient_id','p.name_asin_nric','p.address1','p.city_id','p.nric_no','p.state_id','p.postcode','p.mobile_no','p.birth_date','srfs.harm_date','srfs.harm_time')
                ->leftjoin('patient_registration as p', function($join) {
                    $join->on('srfs.patient_id', '=', 'p.id');
                })     
                ->whereBetween('harm_date', [$request->fromDate, $request->toDate])
                ->where('srfs.hospital_mgmt', '!=','')
                ->where('srfs.status', '=','1');
                $run_query = $query->get()->toArray();
                $response  = json_decode(json_encode($run_query), true);

                
                $index = 0;
                $result = [];
                foreach ($response as $k => $v) {
                    
                    $sh=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('patient_id','=',$v['patient_id'])
                    ->where('section','=','Method of Self-Harm')->first();
                
  
                    $method_self_harm1=str_contains($sh['section_value'],'Overdose\/Poisoning":true');
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

                    $im=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('patient_id','=',$v['patient_id'])
                    ->where('section','=','How did Patient Get Idea about Method')->first();
                
  
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

                    $si=SharpRegistrationSelfHarmResult::select('section_value')
                    ->where('patient_id','=',$v['patient_id'])
                    ->where('section','=','Suicidal Intent')->first();
                
  
                    $suicidal_intent1=str_contains($si['section_value'],'"intent":"Yes"');
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
              
                    $PatientRiskProtectiveAnswer1=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',1)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer1['Answer']=='YES'){
                        $prpa1['RISK_ANSWER'] = 'Presence of psychiatric disorder';
                    }else{
                        $prpa1['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer2=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',2)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer2['Answer']=='Yes'){
                        $prpa2['RISK_ANSWER'] = 'Hopelessness or despair';
                    }else{
                        $prpa2['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer3=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',3)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer3['Answer']=='Yes'){
                        $prpa3['RISK_ANSWER'] = 'Previous suicide attempts';
                    }else{
                        $prpa3['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer4=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',4)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer4['Answer']=='Yes'){
                        $prpa4['RISK_ANSWER'] = 'Presence of substance use/abuse';
                    }else{
                        $prpa4['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer5=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',5)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer5['Answer']=='Yes'){
                        $prpa5['RISK_ANSWER'] = 'Family history of suicidal behavior';
                    }else{
                        $prpa5['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer6=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',6)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer6['Answer']=='Yes'){
                        $prpa6['RISK_ANSWER'] = 'Family history of psychiatric disorders';
                    }else{
                        $prpa6['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer7=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',7)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer7['Answer']=='Yes'){
                        $prpa7['RISK_ANSWER'] = 'Family history of substance abuse';
                    }else{
                        $prpa7['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer8=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',8)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer8['Answer']=='Yes'){
                        $prpa8['RISK_ANSWER'] = 'Stressful life events or loss';
                    }else{
                        $prpa8['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer9=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',9)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer9['Answer']=='Yes'){
                        $prpa9['RISK_ANSWER'] = 'Isolation, rejection or feelings of shame';
                    }else{
                        $prpa9['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer10=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',10)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer10['Answer']=='Yes'){
                        $prpa10['RISK_ANSWER'] = 'chronic physical illness or condition';
                    }else{
                        $prpa10['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer11=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',11)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer11['Answer']=='Yes'){
                        $prpa11['RISK_ANSWER'] = 'History of physical , sexual or emotional abuse';
                    }else{
                        $prpa11['RISK_ANSWER']='';
                    }

                    $PatientRiskProtectiveAnswer12=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',12)
                    ->where('factor_type','=','risk')->first();

                    if($PatientRiskProtectiveAnswer12['Answer']=='Yes'){
                        $prpa12['RISK_ANSWER'] = 'Access to lethal methods/weapons';
                    }else{
                        $prpa12['RISK_ANSWER']='';
                    }
///////////////////ProtectiveFactorAnswer////////////////////////////////////////////////////////////                  
                    $PatientRiskProtectiveAnswer13=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',13)
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer13['Answer']=='Yes'){
                        $prpa13['PROTECTIVE_FACTORS'] = 'Ability to cope with stress/tolerate frustrations';
                    }else{
                        $prpa13['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer14=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',14)
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer14['Answer']=='Yes'){
                        $prpa14['PROTECTIVE_FACTORS'] = 'Strongy held religious/cultural beliefs';
                    }else{
                        $prpa14['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer15=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',15)
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer15['Answer']=='Yes'){
                        $prpa15['PROTECTIVE_FACTORS'] = 'Realistic life goals or future plans';
                    }else{
                        $prpa15['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer16=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',16)
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer16['Answer']=='Yes'){
                        $prpa16['PROTECTIVE_FACTORS'] = 'Responsibility to children/beloved pets';
                    }else{
                        $prpa16['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer17=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',17)
                    ->where('factor_type','=','protective')->first();

                    if($PatientRiskProtectiveAnswer17['Answer']=='Yes'){
                        $prpa17['PROTECTIVE_FACTORS'] = 'Social support';
                    }else{
                        $prpa17['PROTECTIVE_FACTORS']='';
                    }

                    $PatientRiskProtectiveAnswer18=PatientRiskProtectiveAnswer::select('Answer')
                    ->where('patient_mrn_id','=',$v['patient_id'])
                    ->where('QuestionId','=',18)
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
                    $result[$index]['DATE'] = $v['harm_date'];
                    $result[$index]['TIME'] = $v['harm_time'];
                    $result[$index]['NRIC_NO_PASSPORT_NO'] = $v['nric_no'];
                    $result[$index]['NAME'] = $v['name_asin_nric'];
                    // $result[$index]['ADDRESS'] = $v['address1'];
                    // $result[$index]['CITY'] = $city['city_name'];
                    // $result[$index]['STATE'] = $state['state_name'];
                    // $result[$index]['POSTCODE'] = $postcode['postcode'];
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
                        
                    // $totalReports =
                    //  ShharpReportGenerateHistory::select(DB::raw('count( * ) as total'),
                    //   DB::raw("CASE WHEN report_month=1 THEN 'January' WHEN report_month=2 THEN 'Febuary'
                    //   WHEN report_month=3 THEN 'March'  WHEN report_month=4 THEN 'April'  WHEN report_month=5
                    //   THEN 'May'  WHEN report_month=6 THEN 'June'  WHEN report_month=7 THEN 'July'
                    //   WHEN report_month=8 THEN 'August'  WHEN report_month=9 THEN 'September'
                    //   WHEN report_month=10 THEN 'October'  WHEN report_month=11 THEN 'November'
                    //   ELSE 'December' END as month"), 'report_month', 'report_year')
                    //   ->where('report_type', 'shharp_mgmt')->groupBy('report_month', 'report_year')->get()->toArray();

                    if (isset($request->report_type) && $request->report_type == 'excel') {
                        $filename = 'SharpReport-'.time().'.xls';
                        
                        $totalReports= $index;

                        return response([
                            'message' => 'Data successfully retrieved.',
                            'result' => $result,
                            'header' => 'Sharp Report from '.$request->fromDate.' To '.$request->toDate.'<br>'.
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
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);

        $ssh = $appointments->get()->toArray();
        // dd($ssh);
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

                $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = Postcode::where(['postcode' => $patientInfo['postcode']])->get()->toArray();
                    $st = State::where(['id' => $patientInfo['state_id']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();
                    $city_name = ($pc) ? $pc[0]['city_name'] : 'NA';
                    $state_name = ($st) ? $st[0]['state_name'] : 'NA';
                    $postcode = ($pc) ? $pc[0]['postcode'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';
                    if (array_key_exists($cp[0]['appointment_category_name'], $cpa)) {
                        $cpa[$cp[0]['appointment_category_name']] = $cpa[$cp[0]['appointment_category_name']] + 1;
                    } else {
                        $cpa[$cp[0]['appointment_category_name']] = 1;
                    }
                    if (array_key_exists($vt[0]['appointment_visit_name'], $vta)) {
                        $vta[$vt[0]['appointment_visit_name']] = $vta[$vt[0]['appointment_visit_name']] + 1;
                    } else {
                        $vta[$vt[0]['appointment_visit_name']] = 1;
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
        // dd($result);
        if ($result) {
            $totalPatients = count($result);
            $diff = date_diff(date_create($request->fromDate), date_create($request->toDate));
            $totalDays = $diff->format("%a");
            $patientCategories = $cpa;

            $visitTypes = $vta;

            foreach ($visitTypes as $k => $v) {
                $visitTypes[str_replace(' ', '_', $k)] = $v;
            }

            // dd($visitTypes);
            $refferals = $rfa;

            foreach ($refferals as $k => $v) {
                $refferals[str_replace('-', '_', $k)] = $v;
            }

            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'TotalPatient&TypeOfReferralReport-'.time().'.xls';

                //$periodofservice='TOTAL PATIENT AND TYPE OF REFERRAL'.'<br>'.$fromDate. ' To '. $toDate .'<br>';
                // $AttendNo=34;
                // $NoShowNo=21;
                // $Attend='Attend:   '.$AttendNo.'<br>';
                // $NoShow='No Show:   '.$NoShowNo.'<br>';

                $summary= 'TOTAL PATIENT AND TYPE OF REFERRAL'.'<br>'.'TOTAL DAYS:   '.$totalDays.'<br>'.'TOTAL PATIENT:    '.$totalPatients.'<br>';
                // 'Referal_walk' => $rfa, 'Visit_Type' => $visitTypes, 'refferals' =>  $refferals, 'Category_Patient' => $patientCategories,
                
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
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $ssh = $appointments->where('appointment_type', $request->appointment_type);
        if ($request->appointment_status != 0)
            $ssh = $appointments->where('appointment_status', $request->appointment_status);

        $ssh = $appointments->get()->toArray();
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
                $icd = [];
                $job_search = [];
                $job_visit = [];
                $jv= 0;
                $log_meeting = [];
                $empStatus = [];
                $jobStart = [];
                $js = [];
                $curr_interv = [];
                

                if($request->appointment_type == 1){
                $notes = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                    ->get()->toArray();
                if (count($notes) == 0) {
                    $notes = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                        ->get()->toArray();
                }
                }elseif($request->appointment_type == 3){
                    $notes = SeProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->get()->toArray();
                
                    $jobStart = JobStartForm::where('patient_id', $v['patient_mrn_id'])
                                            ->where('is_deleted', 0)
                                            ->get()->toArray();

                    if(count($notes) != 0){
                        $job_visit = GeneralSetting::where('id', $notes[0]['activity_type'])->get()->toArray();
                        if($job_visit[0]['section_value'] == 'FASC (Follow Along Support For Client)' || $job_visit[0]['section_value'] == 'FASE (Follow Along Support For Employer)')
                        {
                            $jv += 1;
                        }
                        $empStatus = GeneralSetting::where('id', $notes[0]['employment_status'])->get()->toArray();
                    }

                
                $log_meeting = LogMeetingWithEmployer::where(['patient_id' => $v['patient_mrn_id']])->get()->toArray();
                if($log_meeting){
                    $jv = $jv + count($log_meeting);
                }
                }
                elseif($request->appointment_type == 4){
                    $notes = EtpProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->get()->toArray();
                }elseif($request->appointment_type == 5){
                    $notes = JobClubProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->get()->toArray();
                }elseif($request->appointment_type == 6){
                    $notes = CpsProgressNote::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->get()->toArray();
                    if(count($notes) != 0){
                        $curr_interv = GeneralSetting::where('id', $notes[0]['current_intervention'])->get()->toArray();
                    }
                }

                $job_search = ListOfJobSearch::where('id', $v['patient_mrn_id'])
                    ->whereBetween('created_at', [$request->fromDate, $request->toDate])
                    ->get()->toArray();

                $staff = StaffManagement::select('name')->where('id', $v['assign_team'])->get()->toArray();
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($request->referral_type != 0)
                    $query->where('referral_type', $request->referral_type);
                if ($request->gender != 0)
                    $query->where('sex', $request->gender);
                    $patientInfon = $query->get()->toArray();
                if ($patientInfon) {
                    $patientInfo = $patientInfon[0];
                    $pc = GeneralSetting::where(['id' => $patientInfo['sex']])->get()->toArray();
                    $st = ServiceRegister::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    $reftyp = GeneralSetting::where(['id' => $patientInfo['referral_type']])->get()->toArray();

                    if ($notes)
                        $icd = IcdCode::where('id', $notes[0]['code_id'])->get()->toArray();
                        $nap = PatientAppointmentDetails::where('patient_mrn_id', $v['patient_mrn_id'])
                            ->where('booking_date', '>', $v['booking_date'])->where('appointment_status', 0)->first();
                    $nxtAppointments = ($nap) ? $nap->toArray() : [];
                    $gender = ($pc) ? $pc[0]['section_value'] : 'NA';
                    $appointment_type = ($st) ? $st[0]['service_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';
                    $result[$index]['No']=$index+1;
                    $result[$index]['Next_visit'] = ($nxtAppointments) ? date('d/m/Y', strtotime($nxtAppointments['booking_date'])) : '-';
                    $result[$index]['time_registered'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['time_seen'] = ($nxtAppointments) ? date('h:i:s A', strtotime($nxtAppointments['booking_time'])) : '-';
                    $result[$index]['Procedure'] = ($icd) ? $icd[0]['icd_name'] : 'NA';
                    // $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                    $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['Attending_staff'] = ($staff) ? $staff[0]['name'] : 'NA';
                    $result[$index]['IC_NO'] = $v['nric_or_passportno'];
                    $result[$index]['GENDER'] = $gender;
                    $result[$index]['APPOINTMENT_TYPE'] = $appointment_type;
                    $result[$index]['AGE'] = $patientInfo['age'];
                    $result[$index]['DIAGNOSIS'] = ($icd) ? $icd[0]['icd_name'] : 'NA';
                    if($request->appointment_type == 3){
                        $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication'] : "NA";
                        $result[$index]['EMPSTATUS'] = ($empStatus) ? $empStatus[0]['section_value'] : "NA";
                        $result[$index]['EMPLOYER'] = ($jobStart) ? $jobStart[0]['name_of_employer'] : "NA";
                        $result[$index]['JOBSTARTDATE'] = ($jobStart) ? $jobStart[0]['first_date_of_work'] : "NA";
                        $result[$index]['ADDRESS'] = ($jobStart) ? $jobStart[0]['address'] : "NA";
                    }
                    elseif($request->appointment_type == 4 ){
                        $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication'] : "NA";
                        $result[$index]['WORKREADY'] = ($notes) ? $notes[0]['work_readiness'] : "NA";
                    }
                    elseif($request->appointment_type == 5 ){
                        $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication'] : "NA";
                        $result[$index]['WORKREADY'] = ($notes) ? $notes[0]['work_readiness'] : "NA";
                    }
                    elseif($request->appointment_type == 6 ){
                        $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication'] : "NA";
                        $result[$index]['CURRENTINTERV'] = ($notes) ? $curr_interv[0]['section_value'] : "NA";
                        $result[$index]['CONTACT'] = ($notes) ? $notes[0]['informants_contact'] : "NA";
                    }
                    else{
                        $result[$index]['MEDICATIONS'] = ($notes) ? $notes[0]['medication_des'] : "NA";
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
            // dd($index);
        }
        //dd($attend);
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
                // $filePath = 'downloads/report/activity-patient-report-' . time() . '.xlsx';
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
                // // Excel::store(new PatientActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate), $filePath, 'public');
                // return Excel::download(new PatientActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate),'activity-patient-report-' . time() . '.xlsx');
                // return response()->json(["message"=>"Patient Activity Report", "excel"=>$excel]);
                //dd($result);
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
                // return response()->json([
                //     "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => 'storage/app/public/' . $filePath,
                //     "Total_Days" => $totalDays, "Total_Patient" => $totalPatients, "Attend" => $totalPatients, "No_Show" => $totalPatients, "code" => 200
                // ]);
            } else {
                return response()->json([
                    "message" => "Toal Patient & Type of Refferal Report", 'result' => $result, 'filepath' => '',
                    "Total_Days" => $totalDays, "Total_Patient" => $totalPatients, "Attend" => $attend, "No_Show" => $noShow, "Total_PatientsPDF" => $totalPatientsPDF, "Total_DaysPDF"=> $totalDaysPDF, "code" => 200
                ]);
            }
        } else {
            return response()->json(["message" => "Toal Patient & Type of Refferal Report", 'result' => [], 'filepath' => null, "code" => 200]);
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
        if ($request->toc != 0)
            $ssh->where('section', $request->toc);
        if ($request->aoi != 0)
            $ssh->where('area_of_involvement', $request->aoi);
        if ($request->screening != 2)
            $ssh->where('screening_mode', $request->screening);
        if ($request->event != 'no-event')
            $ssh->where('name', $request->event);
        if ($request->location == 'mentari') {
            $ssh->where('branch_id', $request->location_value);
        }

        $vorb = $ssh->get()->toArray();
        if ($vorb) {
            foreach ($vorb as $k => $v) {
                $result[$index]['No']=$index+1;
                $result[$index]['Name'] = $v['name'];
                $result[$index]['Type_of_Collaboration'] = $toc[$v['section']];
                $result[$index]['Type_of_Involvement'] = $toi[$v['area_of_involvement']];
                $result[$index]['Screening_Done'] = ($v['screening_mode'] === 1) ? 'YES' : 'NO';
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
                //$filePath = 'downloads/report/activity-von-report-' . time() . '.xlsx';
                $filename = 'VON-report-'.time().'.xls';
                //Excel::store(new VONActivityReportExport($result, $totalPatients, $totalDays, $fromDate, $toDate, $toiArr), $filePath, 'public');
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
        $appointments = PatientAppointmentDetails::whereBetween('booking_date', [$request->fromDate, $request->toDate]);
        if ($request->type_visit != 0)
            $ssh = $appointments->where('type_visit', $request->type_visit);
        if ($request->patient_category != 0)
            $ssh =  $appointments->where('patient_category', $request->patient_category);
        if ($request->appointment_type != 0)
            $ssh = $appointments->where('appointment_type', $request->appointment_type);

        $ssh = $appointments->get()->toArray();

        $demo = [];
        $result = [];
        if ($request->name) {
            $demo['name_asin_nric'] = $request->name;
        }
        if ($request->citizenship) {
            $demo['citizenship'] = $request->citizenship;
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
                $icd = [];
                $notes = PatientCounsellorClerkingNotes::where('patient_mrn_id', $v['patient_mrn_id'])
                    ->where('type_diagnosis_id', $request->diagnosis_id)
                    ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                    ->get()->toArray();
                if (count($notes) == 0) {
                    $notes = PsychiatryClerkingNote::where('patient_mrn_id', $v['patient_mrn_id'])
                        ->where('type_diagnosis_id', $request->diagnosis_id)
                        ->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), $v['booking_date'])
                        ->get()->toArray();
                }

                $staff = StaffManagement::select('name')->where('id', $v['assign_team'])->get()->toArray();
                $query = PatientRegistration::where('id', $v['patient_mrn_id']);
                if ($demo)
                    $query->where($demo);
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
                    $occupation_status = GeneralSetting::where(['id' => $patientInfo['occupation_status']])->get()->toArray();
                    $fee_exemption_status = GeneralSetting::where(['id' => $patientInfo['fee_exemption_status']])->get()->toArray();
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
                    $apt = PatientAppointmentType::where(['id' => $v['appointment_type']])->get()->toArray();
                    $vt = PatientAppointmentVisit::where('id', $v['type_visit'])->get()->toArray();
                    $cp = PatientAppointmentCategory::where('id', $v['patient_category'])->get()->toArray();
                    if ($notes)
                        $icd = IcdCode::where('id', $notes[0]['code_id'])->get()->toArray();
                    $appointment_type = ($apt) ? $apt[0]['appointment_type_name'] : 'NA';
                    $visit_type = ($vt) ? $vt[0]['appointment_visit_name'] : 'NA';
                    $category = ($cp) ? $cp[0]['appointment_category_name'] : 'NA';
                    $result[$index]['No']=$index+1;
                    $result[$index]['Registration_date'] = date('d/m/Y', strtotime($patientInfo['created_at']));
                    $result[$index]['Registration_Time'] = date('h:i:s A', strtotime($patientInfo['created_at']));
                    $result[$index]['nric_no'] = $patientInfo['nric_no'];
                    $result[$index]['Name'] = $patientInfo['name_asin_nric'];
                    $result[$index]['Attendance_status'] = $v['appointment_status'];
                    // $result[$index]['Attendance_status'] = get_appointment_status($v['appointment_status']);
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
        // dd($result);
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
        $months = [
            'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'
        ];
        $startYear = date('Y', strtotime($request->fromDate));
        $endYear = date('Y', strtotime($request->toDate));

        $result = [];
        $resultSet = [];
        $finalResult = [];
        $notes = SeProgressNote::whereBetween('date', [$request->fromDate, $request->toDate]);
        if ($request->job_status != 0)
            $ssh = $notes->where('employment_status', $request->job_status);
        $ssh = $notes->get()->toArray();
        // dd($ssh);
        if ($ssh) {
            foreach ($ssh as $key => $val) {
                $query = DB::table('staff_management')
                    ->select('hospital_branch__details.hospital_code')
                    ->join('hospital_branch__details', 'staff_management.branch_id', '=', 'hospital_branch__details.id')
                    ->where('staff_management.id', $val['added_by']);
                if ($request->hospital_id != 0) {
                    $query->where('staff_management.branch_id', $request->hospital_id);
                }
                if ($request->state_id != 0) {
                    $query->where('hospital_branch__details.branch_state', $request->state_id);
                }

                $rs = $query->get()->toArray();
                if ($rs) {
                    $resultSet[] = ['hospital_code' => $rs[0]->hospital_code, 'date' => $val['date'], 'employment_status' => $val['employment_status'], 'status' => $val['status']];
                    //  $result[$resultSet['hospital_code']][$year][$month]['new_job'] = 0;
                    //  $result[$resultSet['hospital_code']][$year][$month]['ongoing_job'] = ($val['employment_status'] == 1)?;
                }
            }
        }
        $yearArray = [];
        if ($resultSet) {
            foreach ($resultSet as $key => $val) {
                // dd($val);
                $year = date('Y', strtotime($val['date']));
                $month = date('m', strtotime($val['date']));
                $result[$val['hospital_code']][$year][$month]['month_name'] = '';
                $result[$val['hospital_code']][$year][$month]['new_job'] = 0;
                $result[$val['hospital_code']][$year][$month]['ongoing_job'] = 0;
                $result[$val['hospital_code']][$year][$month]['total_caseload'] = 0;
                $result[$val['hospital_code']][$year][$month]['total_dismissed'] = 0;
                $result[$val['hospital_code']][$year][$month]['kpi'] = 0;
            }
            foreach ($resultSet as $key => $val) {
                $year = date('Y', strtotime($val['date']));
                $month = date('m', strtotime($val['date']));
                if (array_key_exists($year, $yearArray) && !in_array($month, $yearArray[$year])) {
                    $yearArray[$year][] =  (int)$month;
                } else if (!array_key_exists($year, $yearArray)) {
                    $yearArray[$year][] =  (int)$month;
                }
                $result[$val['hospital_code']][$year][$month]['month_name'] = $months[date('m', strtotime($val['date'])) - 1];
                $result[$val['hospital_code']][$year][$month]['new_job'] =  ($val['status'] == 1 && $val['employment_status'] == 2) ? ($result[$val['hospital_code']][$year][$month]['new_job'] + 1) : $result[$val['hospital_code']][$year][$month]['new_job'];
                $result[$val['hospital_code']][$year][$month]['ongoing_job'] =  ($val['employment_status'] == 1) ? ($result[$val['hospital_code']][$year][$month]['ongoing_job'] + 1) : $result[$val['hospital_code']][$year][$month]['ongoing_job'];
                $result[$val['hospital_code']][$year][$month]['total_caseload'] = ($val['status'] == 2 || $val['status'] == 1 || $val['status'] == 0) ? ($result[$val['hospital_code']][$year][$month]['total_caseload'] + 1) : $result[$val['hospital_code']][$year][$month]['total_caseload'];
                $result[$val['hospital_code']][$year][$month]['total_dismissed'] = ($val['status'] == 2) ? ($result[$val['hospital_code']][$year][$month]['total_dismissed'] + 1) : $result[$val['hospital_code']][$year][$month]['total_dismissed'];
                $result[$val['hospital_code']][$year][$month]['kpi'] = ($result[$val['hospital_code']][$year][$month]['total_caseload'] != 0) ? (($result[$val['hospital_code']][$year][$month]['new_job'] + $result[$val['hospital_code']][$year][$month]['ongoing_job']) / $result[$val['hospital_code']][$year][$month]['total_caseload']) * 100 : 0;
            }
        }
        foreach ($yearArray as $k => $v) {
            sort($v);
            $yearArray[$k] = $v;
        }

        // $filePath = 'downloads/report/kpi-report' . '.xlsx';
        // Excel::store(new KPIReportExport($result, $yearArray), $filePath, 'public');
        // dd($yearArray);
        // dd($result);
        if ($result) {
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '86400',
                'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
            ];
            $filePath = '';
            if (isset($request->report_type) && $request->report_type == 'excel') {
                // $filePath = 'downloads/report/kpi-report-rg-'.time() . '.xlsx';
                // Excel::store(new KPIReportExport($result, $yearArray, $months), $filePath, 'public');
                // return response()->json(["message" => "KPI Report", 'result' => $result,  'filepath' => env('APP_URL') . '/storage/app/public/' . $filePath, "code" => 200]);
                $filename = 'kpi-report-rg-'.time() . '.xlsx';
                $filePath = 'downloads/report/'.$filename;
                $KPIExcel = Excel::store(new KPIReportExport($result, $yearArray, $months), $filePath, 'public');
                $pathToFile = Storage::url($filePath);
                return response()->json(["message" => "KPI Report", 'result' => $result,  'filepath' => env('APP_URL') . $pathToFile, "code" => 200]);
                // return response()->download($pathToFile,$filename,$headers);
                // return response()->json(["message" => "KPI Report", 'filename' => $filePath,  'filepath' => null, "code" => 200])->download(storage_path().'/app/public/'.$filePath)->deleteFileAfterSend(true);;
            } else {
                return response()->json(["message" => "KPI Report", 'result' => $result,  'filepath' => null, "code" => 200]);
            }

        }return response()->json(["message" => "KPI Report", 'result' => [], 'filepath' => null, "code" => 200]);
    }
}
