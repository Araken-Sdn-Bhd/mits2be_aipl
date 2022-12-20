<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VolunteerIndividualApplicationForm;
use App\Models\VolunteerismApplicationForm;
use App\Models\VonOrgBackground;
use App\Models\VonOrgRepresentativeBackground;
use App\Models\Volunteerism;
use App\Models\OutReachProjects;
use App\Models\NetworkingContribution;
use App\Models\VonAppointment;
use App\Models\VonGroupApplication;
use App\Models\State;
use App\Models\Postcode;
use App\Models\HospitalBranchManagement;
use Exception;
use Validator;
use DB;
use DateTime;
use DateTimeZone;
use App\Models\Notifications;
use Illuminate\Support\Facades\Mail;
use App\Mail\VONApplicationMail as VonApplicationMail;

class VounteerIndividualApplicationFormController extends Controller
{
    public function add(Request $request)
    {
        $volunteerismForm = [];
        $validation = [
            'name' => 'required|string',
            'date' => 'required|string',
            'email' => 'required|string',
            'phone_number' => 'required',
            'address' => 'required|string',
            'postcode_id' => 'required|integer',
            'city_id' => 'required|integer',
            'state_id' => 'required|integer',
            'highest_education' => 'required|string',
            'current_occupation' => 'required|string',
            'hospital_id' => 'required|integer',
            'areas_involvement' => 'required|string',
            'agree_trem_condition' => 'required|boolean'
        ];
        if ($request->areas_involvement == 'Volunteerism') {
            $validation['volunteering_experience'] = 'required|string';
            $validation['health_professional'] = 'required|string';
            $validation['relevant_mentari_service'] = 'required|string';
            $validation['weekday'] = 'required|string';
            $validation['timeslot'] = 'required|string';

            if ($request->health_professional == 'yes') {
                $validator = Validator::make($request->all(), ['health_professional_resume' => 'required||mimes:pdf|max:10240']);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                } else {
                    $files = $request->file('health_professional_resume');
                    $isUploaded = upload_file($files, 'HealthProfessionalResume');
                    $volunteerismForm['health_professional_resume'] =  $isUploaded->getData()->path;
                }
            }
            if ($request->volunteering_experience == 'Yes') {
                $validator = Validator::make($request->all(), ['volunteering_experience_des' => 'required']);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                } else {
                    $volunteerismForm['volunteering_experience_yes_des'] =  $request->volunteering_experience_des;
                }
            }
        }
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $volunteerindividual = [
            'name' =>  $request->name,
            'date' =>  $request->date,
            'email' =>  $request->email,
            'phone_number' =>  $request->phone_number,
            'address' =>  $request->address,
            'postcode_id' =>  $request->postcode_id,
            'city_id' =>  $request->city_id,
            'state_id' =>  $request->state_id,
            'highest_education' =>  $request->highest_education,
            'current_occupation' =>  $request->current_occupation,
            'hospital_id' =>  $request->hospital_id,
            'areas_involvement' =>  $request->areas_involvement
        ];
        try {
            $HOD = VolunteerIndividualApplicationForm::create($volunteerindividual);
            $getVolunteerid = $HOD->id();

            if ($request->areas_involvement == 'Volunteerism') {
                $validateVolunteerForm = [];

                $volunteerismForm = [
                    'volunteer_id' => $getVolunteerid,
                    'volunteering_experience' =>  $request->volunteering_experience,
                    'health_professional' =>  $request->health_professional,
                    'relevant_mentari_service' =>  $request->relevant_mentari_service,
                    'relevant_mentari_service_other' =>  $request->relevant_mentari_service_other,
                    'weekday' =>  $request->day,
                    'timeslot' =>  $request->time
                ];

                VolunteerismApplicationForm::create($volunteerismForm);
                return response()->json(["message" => "Volunteer Individual Form Created", "code" => 200]);
            }
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerindividual, "code" => 200]);
        }
    }

    public function addVon(Request $request)
    {
        $validation = ['section' => 'required|string'];
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->section == 'org') {
            return  $this->addOrganization($request);
        } else if ($request->section == 'individual') {
            return  $this->addIndividual($request);
        } else {
            return $this->addGroup($request);
        }
    }
    public function addOrganization($request)
    {
        $validation = [
            'org_name' => 'required|string',
            'org_reg_number' => 'required|string',
            'org_desc' => 'required|string',
            'org_email' => 'required|string',
            'org_phone' => 'required|string',
            'name' => 'required|string',
            'position_in_org' => 'string',
            'email' => 'required|string',
            'phone_number' => 'required',
            'address' => 'required|string',
            'postcode_id' => 'required|integer',
            'state_id' => 'required|integer',
            'occupation_sector_id' => 'required|integer',
            'education_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'area_of_involvement' => 'required|string',
            'is_agree' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $orgBackground = [
            'added_by' => $request->added_by,
            'org_name' => $request->org_name,
            'org_reg_number' => $request->org_reg_number,
            'org_desc' => $request->org_desc,
            'org_email' => $request->org_email,
            'org_phone' => $request->org_phone,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $vbId = VonOrgBackground::create($orgBackground);
        $orgRepBackground = [
            'added_by' => $request->added_by,
            'org_background_id' => $vbId->id,
            'section' => $request->section,
            'name' => $request->name,
            'position_in_org' => $request->position_in_org,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'postcode_id' => $request->postcode_id,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'education_id' => $request->education_id,
            'occupation_sector_id' => $request->occupation_sector_id,
            'branch_id' => $request->branch_id,
            'area_of_involvement' => $request->area_of_involvement,
            'is_agree' => $request->is_agree,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $vorbId = VonOrgRepresentativeBackground::create($orgRepBackground);
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $Org_id = VonOrgRepresentativeBackground::latest()->first();
        $notifi=[
            'added_by' => $request->added_by,
            'branch_id'=>$request->branch_id,
            'role'=>'Admin/Clerk ',
            'patient_mrn' =>   $Org_id['id'],
            'url_route' => "/Modules/Von/view-organization?id=".$Org_id['id'],
            'created_at' => $date->format('Y-m-d H:i:s'),
            'message' =>  'New organization application for VON collaboration',
        ];
        $HOD = Notifications::insert($notifi);
        if ($request->area_of_involvement == 'Volunteerism') {
            $volunteerism = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'is_voluneering_exp' => $request->is_voluneering_exp,
                'exp_details'  => $request->exp_details,
                'is_mental_health_professional' => $request->is_mental_health_professional,
                'mentari_services' => $request->mentari_services,
                'available_date' => $request->available_date,
                'available_time' => $request->available_time,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $volunteerism['resume'] = 'NA';
            if ($request->is_mental_health_professional == '1') {
                $validator = Validator::make($request->all(), ['health_professional_resume' => 'required|max:10240']);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                } else {
                    $files = $request->file('health_professional_resume');
                    $isUploaded = upload_file($files, 'HealthProfessionalResume');
                    $volunteerism['resume'] =  $isUploaded->getData()->path;
                }
            }
            try {
                Volunteerism::create($volunteerism);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerism, "code" => 500]);
            }
        }

        if ($request->area_of_involvement == 'Outreach Project Collaboration') {
            $outreachprojects = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'project_name' => $request->project_name,
                'project_background'  => $request->project_background,
                'project_objectives' => $request->project_objectives,
                'target_audience' => $request->target_audience,
                'no_of_paricipants' => $request->no_of_paricipants,
                'time_frame' => $request->time_frame,
                'estimated_budget' => $request->estimated_budget,
                'project_scopes' => $request->project_scopes,
                'project_loaction' => $request->project_loaction,
                'project_loaction_value' => $request->project_loaction_value,
                'target_outcome' => $request->target_outcome,
                'followup_projects' => $request->followup_projects,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];
            try {
                OutReachProjects::insert($outreachprojects);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $outreachprojects, "code" => 500]);
            }
        }
        if ($request->area_of_involvement == 'Networking Make a Contribution') {
            $NetworkingContribution = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'contribution' => $request->contribution,
                'budget'  => $request->budget,
                'project_loaction' => $request->project_loaction,
                'project_loaction_value' => $request->project_loaction_value,
                'no_of_paricipants' => $request->no_of_paricipants,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];
            try {
                NetworkingContribution::insert($NetworkingContribution);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $NetworkingContribution, "code" => 500]);
            }
        }
    }


    public function addIndividual($request)
    {
        $validation = [
            'name' => 'required|string',
            'dob' => 'required|string',
            'email' => 'required|string',
            'phone_number' => 'required',
            'address' => 'required|string',
            'postcode_id' => 'required|integer',
            'state_id' => 'required|integer',
            'education_id' => 'required|integer',
            'occupation_sector_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'area_of_involvement' => 'required|string',
            'is_agree' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $orgRepBackground = [
            'added_by' => $request->added_by,
            'org_background_id' => 0,
            'section' => $request->section,
            'name' => $request->name,
            'dob' => $request->dob,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'postcode_id' => $request->postcode_id,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'education_id' => $request->education_id,
            'occupation_sector_id' => $request->occupation_sector_id,
            'branch_id' => $request->branch_id,
            'area_of_involvement' => $request->area_of_involvement,
            'is_agree' => $request->is_agree,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $vorbId = VonOrgRepresentativeBackground::create($orgRepBackground);
        $Org_id = VonOrgRepresentativeBackground::latest()->first();
        $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
        $notifi=[
            'added_by' => $request->added_by,
            'branch_id'=>$request->branch_id,
            'role'=>'Admin/Clerk ',
            'patient_mrn' =>   $Org_id['id'],
            'url_route' => "/Modules/Von/view-individual?id=".$Org_id['id'],
            'created_at' => $date->format('Y-m-d H:i:s'),
            'message' =>  'New individual application for VON collaboration',
        ];
        $HOD = Notifications::insert($notifi);
        if ($request->area_of_involvement == 'Volunteerism') {
            $volunteerism = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'is_voluneering_exp' => $request->is_voluneering_exp,
                'exp_details'  => $request->exp_details,
                'is_mental_health_professional' => $request->is_mental_health_professional,
                'mentari_services' => $request->mentari_services,
                'available_date' => $request->available_date,
                'available_time' => $request->available_time,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $volunteerism['resume'] = 'NA';
            if ($request->is_mental_health_professional == '1') {
                $validator = Validator::make($request->all(), ['health_professional_resume' => 'required|max:10240']);
                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                } else {
                    $files = $request->file('health_professional_resume');
                    $isUploaded = upload_file($files, 'HealthProfessionalResume');
                    $volunteerism['resume'] =  $isUploaded->getData()->path;
                }
            }
            try {
                Volunteerism::create($volunteerism);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerism, "code" => 500]);
            }
        }

        if ($request->area_of_involvement == 'Outreach Project Collaboration') {
            $outreachprojects = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'project_name' => $request->project_name,
                'project_background'  => $request->project_background,
                'project_objectives' => $request->project_objectives,
                'target_audience' => $request->target_audience,
                'no_of_paricipants' => $request->no_of_paricipants,
                'time_frame' => $request->time_frame,
                'estimated_budget' => $request->estimated_budget,
                'project_scopes' => $request->project_scopes,
                'project_loaction' => $request->project_loaction,
                'project_loaction_value' => $request->project_loaction_value,
                'target_outcome' => $request->target_outcome,
                'followup_projects' => $request->followup_projects,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];
            try {
                OutReachProjects::create($outreachprojects);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $outreachprojects, "code" => 500]);
            }
        }
        if ($request->area_of_involvement == 'Networking Make a Contribution') {
            $NetworkingContribution = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'contribution' => $request->contribution,
                'budget'  => $request->budget,
                'project_loaction' => $request->project_loaction,
                'project_loaction_value' => $request->project_loaction_value,
                'no_of_paricipants' => $request->no_of_paricipants,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];
            try {
                NetworkingContribution::create($NetworkingContribution);
                return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $NetworkingContribution, "code" => 500]);
            }
        }
    }

    public function addGroup($request)
    {
        $validation = [
            'is_represent_org' => 'required|string',
            'members_count' => 'required|string',
            'member_background' => 'required|string',
            'is_you_represenative' => 'required|string',
            'is_agree' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $group = [
            'added_by' => $request->added_by,
            'is_represent_org' => $request->is_represent_org,
            'members_count' => $request->members_count,
            'member_background' => $request->member_background,
            'is_you_represenative' => $request->is_you_represenative,
            'is_agree' => $request->is_agree
        ];
        $gruopId =  VonGroupApplication::create($group);
        if ($request->is_you_represenative == '1') {
            $orgRepBackground = [
                'added_by' => $request->added_by,
                'org_background_id' =>  $gruopId->id,
                'section' => $request->section,
                'name' => $request->name,
                'dob' => $request->dob,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'postcode_id' => $request->postcode_id,
                'city_id' => $request->city_id,
                'state_id' => $request->state_id,
                'education_id' => $request->education_id,
                'occupation_sector_id' => $request->occupation_sector_id,
                'branch_id' => $request->branch_id,
                'area_of_involvement' => $request->area_of_involvement,
                'is_agree' => $request->is_agree,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $vorbId = VonOrgRepresentativeBackground::create($orgRepBackground);
            $Org_id = VonOrgRepresentativeBackground::latest()->first();
            $date = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
            $notifi=[
                'added_by' => $request->added_by,
                'branch_id'=>$request->branch_id,
                'role'=>'Admin/Clerk ',
                'patient_mrn' =>   $Org_id['id'],
                'url_route' => "/Modules/Von/view-group?id=".$Org_id['id'],
                'created_at' => $date->format('Y-m-d H:i:s'),
                'message' =>  'New group application for VON collaboration',
            ];
            $HOD = Notifications::insert($notifi);
            if ($request->area_of_involvement == 'Volunteerism') {
                $volunteerism = [
                    'added_by' => $request->added_by,
                    'parent_section_id' => $vorbId->id,
                    'parent_section' => $request->section,
                    'is_voluneering_exp' => $request->is_voluneering_exp,
                    'exp_details'  => $request->exp_details,
                    'is_mental_health_professional' => $request->is_mental_health_professional,
                    'mentari_services' => $request->mentari_services,
                    'available_date' => $request->available_date,
                    'available_time' => $request->available_time,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $volunteerism['resume'] = 'NA';
                if ($request->is_mental_health_professional == '1') {
                    $validator = Validator::make($request->all(), ['health_professional_resume' => 'required|max:10240']);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    } else {
                        $files = $request->file('health_professional_resume');
                        $isUploaded = upload_file($files, 'HealthProfessionalResume');
                        $volunteerism['resume'] =  $isUploaded->getData()->path;
                    }
                }
                try {
                    Volunteerism::create($volunteerism);
                    return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerism, "code" => 500]);
                }
            }

            if ($request->area_of_involvement == 'Outreach Project Collaboration') {
                $outreachprojects = [
                    'added_by' => $request->added_by,
                    'parent_section_id' => $vorbId->id,
                    'parent_section' => $request->section,
                    'project_name' => $request->project_name,
                    'project_background'  => $request->project_background,
                    'project_objectives' => $request->project_objectives,
                    'target_audience' => $request->target_audience,
                    'no_of_paricipants' => $request->no_of_paricipants,
                    'time_frame' => $request->time_frame,
                    'estimated_budget' => $request->estimated_budget,
                    'project_scopes' => $request->project_scopes,
                    'project_loaction' => $request->project_loaction,
                    'project_loaction_value' => $request->project_loaction_value,
                    'target_outcome' => $request->target_outcome,
                    'followup_projects' => $request->followup_projects,
                    'mentari_services' => $request->mentari_services,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                try {
                    OutReachProjects::insert($outreachprojects);
                    return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $outreachprojects, "code" => 500]);
                }
            }
            if ($request->area_of_involvement == 'Networking Make a Contribution') {
                $NetworkingContribution = [
                    'added_by' => $request->added_by,
                    'parent_section_id' => $vorbId->id,
                    'parent_section' => $request->section,
                    'contribution' => $request->contribution,
                    'budget'  => $request->budget,
                    'project_loaction' => $request->project_loaction,
                    'project_loaction_value' => $request->project_loaction_value,
                    'no_of_paricipants' => $request->no_of_paricipants,
                    'mentari_services' => $request->mentari_services,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                try {
                    NetworkingContribution::insert($NetworkingContribution);
                    return response()->json(["message" => "Application Submitted Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $NetworkingContribution, "code" => 500]);
                }
            }
        }
    }

    public function areaOfInvolvement($request, $vorbId)
    {
        if ($request->area_of_involvement == 'Volunteerism') {
            $volunteerism = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'is_voluneering_exp' => $request->is_voluneering_exp,
                'exp_details'  => $request->exp_details,
                'is_mental_health_professional' => $request->is_mental_health_professional,
                'mentari_services' => $request->mentari_services,
                'available_date' => $request->available_date,
                'available_time' => $request->available_time,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $volunteerism['resume'] = 'NA';
            if ($request->is_mental_health_professional == '1') {
                $validator = Validator::make($request->all(), ['health_professional_resume' => 'required|max:10240']);

                if ($validator->fails()) {
                    return response()->json(["message" => $validator->errors(), "code" => 422]);
                } else {
                    $files = $request->file('health_professional_resume');
                    $isUploaded = upload_file($files, 'HealthProfessionalResume');
                    $volunteerism['resume'] =  $isUploaded->getData()->path;
                }
            }
            try {
                $isInserted = Volunteerism::create($volunteerism);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerism, "code" => 500]);
            }
        }

        if ($request->area_of_involvement == 'Outreach Project Collaboration') {
            $outreachprojects = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'project_name' => $request->project_name,
                'project_background'  => $request->project_background,
                'project_objectives' => $request->project_objectives,
                'target_audience' => $request->target_audience,
                'no_of_paricipants' => $request->no_of_paricipants,
                'time_frame' => $request->time_frame,
                'estimated_budget' => $request->estimated_budget,
                'project_scopes' => $request->project_scopes,
                'project_loaction' => $request->project_loaction,
                'target_outcome' => $request->target_outcome,
                'followup_projects' => $request->followup_projects,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];

            OutReachProjects::insert($outreachprojects);
        }
        if ($request->area_of_involvement == 'Networking Make a Contribution') {
            $NetworkingContribution = [
                'added_by' => $request->added_by,
                'parent_section_id' => $vorbId->id,
                'parent_section' => $request->section,
                'contribution' => $request->contribution,
                'budget'  => $request->budget,
                'project_loaction' => $request->project_loaction,
                'project_loaction_value' => $request->project_loaction_value,
                'no_of_paricipants' => $request->no_of_paricipants,
                'mentari_services' => $request->mentari_services,
                'created_at' => date('Y-m-d H:i:s')
            ];

            NetworkingContribution::insert($NetworkingContribution);
        }
    }

    public function getList()
    {
        $result = [];
        $k = 0;
        $indi = VonOrgRepresentativeBackground::where('section', 'individual')->where('status', '0')->get();
        if ($indi) {
            foreach ($indi as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Individual';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        $group = VonOrgRepresentativeBackground::where('section', 'group')->where('status', '0')->get();
        if ($group) {
            foreach ($group as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Group';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        $org = VonOrgRepresentativeBackground::where('section', 'org')->where('status', '0')->get();
        if ($org) {
            foreach ($org as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $name = VonOrgBackground::where('id', $val['org_background_id'])->get()->pluck('org_name')->toArray();
                $result[$k]['name'] = ($name) ? $name[0] : 'NA';
                $result[$k]['app_type'] = 'Organization';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        return response()->json(["message" => "Application List", 'list' => $result, "code" => 200]);
    }

    public function getListByBranchId(Request $request)
    {
        $role = DB::table('staff_management')
            ->select('roles.code')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->where('staff_management.email', '=', $request->email)
            ->first();

        $result = [];
        $k = 0;
        if($role->code != 'superadmin'){
         $indi = VonOrgRepresentativeBackground::where('section', 'individual')->where('status', '0')->where('branch_id',$request->branch)->get();
        }else{
            $indi = VonOrgRepresentativeBackground::where('section', 'individual')->where('status', '0')->get();
        }
        if ($indi) {
            foreach ($indi as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Individual';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        if($role->code != 'superadmin'){
        $group = VonOrgRepresentativeBackground::where('section', 'group')->where('status', '0')->where('branch_id',$request->branch)->get();
        }else{
            $group = VonOrgRepresentativeBackground::where('section', 'group')->where('status', '0')->get();
        }
        if ($group) {
            foreach ($group as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Group';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        if($role->code != 'superadmin'){
        $org = VonOrgRepresentativeBackground::where('section', 'org')->where('status', '0')->where('branch_id',$request->branch)->get();
        }else{
            $org = VonOrgRepresentativeBackground::where('section', 'org')->where('status', '0')->get();

        }
        if ($org) {
            foreach ($org as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $name = VonOrgBackground::where('id', $val['org_background_id'])->get()->pluck('org_name')->toArray();
                $result[$k]['name'] = ($name) ? $name[0] : 'NA';
                $result[$k]['app_type'] = 'Organization';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                if ($val['screening_mode'] == '1') {
                    $result[$k]['screening'] = 'Yes';
                } else if ($val['screening_mode'] == '0'){
                    $result[$k]['screening'] = 'No';
                }
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        return response()->json(["message" => "Application List", 'list' => $result, "code" => 200]);
    }

    public function getRecord(Request $request)
    {
        $result = [];
        $response = VonOrgRepresentativeBackground::where('id', $request->id)
        ->get();
        $section = $response[0]['section'];
        if ($section == 'org') {
            $org =  VonOrgBackground::where('id', $response[0]['org_background_id'])->get();
            $result['org_name'] = $org[0]['org_name'];
            $result['org_reg_number'] = $org[0]['org_reg_number'];
            $result['org_desc'] = $org[0]['org_desc'];
            $result['org_email'] = $org[0]['org_email'];
            $result['org_phone'] = $org[0]['org_phone'];
        }
        $is_you_represenative = '0';
        if ($section == 'group') {
            $org =  VonGroupApplication::where('id', $response[0]['org_background_id'])->get();
            $result['is_represent_org'] = $org[0]['is_represent_org'];
            $result['members_count'] = $org[0]['members_count'];
            $result['member_background'] = $org[0]['member_background'];
            $is_you_represenative = $result['is_you_represenative'] = $org[0]['is_you_represenative'];
            $result['is_agree'] = $org[0]['is_agree'];
        }
        $result['section'] = $response[0]['section'];
        $result['id'] = $response[0]['id'];
        if ($is_you_represenative == '1' || $section == 'org' || $section == 'individual') {
            $getState=State::where('id',$response[0]['state_id'])->get();
            $getPostcode=Postcode::where('id',$response[0]['postcode_id'])->get();
            $result['name'] = $response[0]['name'];
            $result['dob'] = $response[0]['dob'];
            $result['position_in_org'] = $response[0]['position_in_org'];
            $result['email'] = $response[0]['email'];
            $result['phone_number'] = $response[0]['phone_number'];
            $result['address'] = $response[0]['address'];
            $result['postcode'] = $getPostcode[0]['postcode'];
            $result['city'] = $getPostcode[0]['city_name'];
            $result['state'] = $getState[0]['state_name'];
            $result['education'] = $response[0]['education_id'];
            $result['occupation_sector'] = $response[0]['occupation_sector_id'];
            $result['branch_id'] = $response[0]['branch_id'];
            $result['area_of_involvement'] = $response[0]['area_of_involvement'];
            if ($response[0]['area_of_involvement'] == 'Volunteerism') {
                $services = Volunteerism::where('parent_section_id', $response[0]['id'])->get();
                $result['is_voluneering_exp'] = $services[0]['is_voluneering_exp'];
                $result['exp_details'] = $services[0]['exp_details'];
                $result['is_mental_health_professional'] = $services[0]['is_mental_health_professional'];
                $result['resume'] = $services[0]['resume'];
                $result['mentari_services'] = $services[0]['mentari_services'];
                $result['available_date'] = $services[0]['available_date'];
                $result['available_time'] = $services[0]['available_time'];
            }
            if ($response[0]['area_of_involvement'] == 'Outreach Project Collaboration') {
                $services = OutReachProjects::where('parent_section_id', $response[0]['id'])->get();
                $result['project_name'] = $services[0]['project_name'];
                $result['project_background'] = $services[0]['project_background'];
                $result['project_objectives'] = $services[0]['project_objectives'];
                $result['target_audience'] = $services[0]['target_audience'];
                $result['no_of_paricipants'] = $services[0]['no_of_paricipants'];
                $result['time_frame'] = $services[0]['time_frame'];
                $result['estimated_budget'] = $services[0]['estimated_budget'];
                $result['project_scopes'] = $services[0]['project_scopes'];
                $result['project_loaction'] = $services[0]['project_loaction'];
                $result['project_loaction_value'] = $services[0]['project_loaction_value'];
                $result['target_outcome'] = $services[0]['target_outcome'];
                $result['followup_projects'] = $services[0]['followup_projects'];
                $result['mentari_services'] = $services[0]['mentari_services'];
            }
            if ($response[0]['area_of_involvement'] == 'Networking Make a Contribution') {
                $services = NetworkingContribution::where('parent_section_id', $response[0]['id'])->get();
                $result['contribution'] = $services[0]['contribution'];
                $result['budget'] = $services[0]['budget'];
                $result['project_loaction'] = $services[0]['project_loaction'];
                $result['project_loaction_value'] = $services[0]['project_loaction_value'];
                $result['no_of_paricipants'] = $services[0]['no_of_paricipants'];
                $result['mentari_services'] = $services[0]['mentari_services'];
            }
        }
        return response()->json(["message" => "Application List", 'list' => $result, "code" => 200]);
    }

    public function updateRecord(Request $request)
    {
        $validation = ['section' => 'required|string', 'id' => 'required|integer'];
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->section == 'org') {
            return  $this->editOrganization($request);
        } else if ($request->section == 'individual') {
            return  $this->editIndividual($request);
        } else {
            return $this->editGroup($request);
        }
    }
    public function editOrganization($request)
    {
        $validation = [
            'is_agree' => 'required|string',
            'screening_mode' => 'required|string'
        ];
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $response = VonOrgRepresentativeBackground::where('id', $request->id)->get();
        $orgRepBackground = [
            'is_agree' => $request->is_agree,
            'screening_mode' => $request->screening_mode,
        ];
        try {
            VonOrgRepresentativeBackground::where('id', $request->id)->update($orgRepBackground);
            return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 500]);
        }
    }

    public function editIndividual($request)
    {
        $validation = [
            'is_agree' => 'required|string',
            'screening_mode' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $orgRepBackground = [
            'is_agree' => $request->is_agree,
            'screening_mode' => $request->screening_mode,
        ];
        try {
            VonOrgRepresentativeBackground::where('id', $request->id)->update($orgRepBackground);
            return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), "code" => 500]);
        }
    }

    public function editGroup($request)
    {
        $validation = [
            'is_agree' => 'required|string',
            'screening_mode' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $group = [
            'is_agree' => $request->is_agree
        ];
        $response = VonOrgRepresentativeBackground::where('id', $request->id)->get();
        $org_background_id = $response[0]['org_background_id'];
        VonGroupApplication::where('id', $org_background_id)->update($group);
        if ($request->is_you_represenative == '1') {
            $orgRepBackground = [
                'is_agree' => $request->is_agree,
                'screening_mode' => $request->screening_mode,
            ];
            try {
                VonOrgRepresentativeBackground::where('id', $request->id)->update($orgRepBackground);
                return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), "code" => 500]);
            }
            VonOrgRepresentativeBackground::where('id', $request->id)->update($orgRepBackground);
            if ($request->area_of_involvement == 'Volunteerism') {
                $volunteerism = [
                    'added_by' => $request->added_by,
                    'is_voluneering_exp' => $request->is_voluneering_exp,
                    'exp_details'  => $request->exp_details,
                    'is_mental_health_professional' => $request->is_mental_health_professional,
                    'mentari_services' => $request->mentari_services,
                    'available_date' => $request->available_date,
                    'available_time' => $request->available_time,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $volunteerism['resume'] = 'NA';
                if ($request->is_mental_health_professional == '1') {
                    $validator = Validator::make($request->all(), ['health_professional_resume' => 'required|max:10240']);
                    if ($validator->fails()) {
                        return response()->json(["message" => $validator->errors(), "code" => 422]);
                    } else {
                        $files = $request->file('health_professional_resume');
                        $isUploaded = upload_file($files, 'HealthProfessionalResume');
                        $volunteerism['resume'] =  $isUploaded->getData()->path;
                    }
                }
                try {
                    Volunteerism::where('parent_section_id', $request->id)->update($volunteerism);
                    return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $volunteerism, "code" => 500]);
                }
            }

            if ($request->area_of_involvement == 'Outreach Project Collaboration') {
                $outreachprojects = [
                    'added_by' => $request->added_by,
                    'project_name' => $request->project_name,
                    'project_background'  => $request->project_background,
                    'project_objectives' => $request->project_objectives,
                    'target_audience' => $request->target_audience,
                    'no_of_paricipants' => $request->no_of_paricipants,
                    'time_frame' => $request->time_frame,
                    'estimated_budget' => $request->estimated_budget,
                    'project_scopes' => $request->project_scopes,
                    'project_loaction' => $request->project_loaction,
                    'project_loaction_value' => $request->project_loaction_value,
                    'target_outcome' => $request->target_outcome,
                    'followup_projects' => $request->followup_projects,
                    'mentari_services' => $request->mentari_services,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                try {
                    OutReachProjects::where('parent_section_id', $request->id)->update($outreachprojects);
                    return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $outreachprojects, "code" => 500]);
                }
            }
            if ($request->area_of_involvement == 'Networking Make a Contribution') {
                $NetworkingContribution = [
                    'added_by' => $request->added_by,
                    'contribution' => $request->contribution,
                    'budget'  => $request->budget,
                    'project_loaction' => $request->project_loaction,
                    'project_loaction_value' => $request->project_loaction_value,
                    'no_of_paricipants' => $request->no_of_paricipants,
                    'mentari_services' => $request->mentari_services,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                try {
                    NetworkingContribution::where('parent_section_id', $request->id)->update($NetworkingContribution);
                    return response()->json(["message" => "Application Updated Successfully", "code" => 200]);
                } catch (Exception $e) {
                    return response()->json(["message" => $e->getMessage(), 'Volunteer' => $NetworkingContribution, "code" => 500]);
                }
            }
        }
    }

    public function setStatus(Request $request)
    {
        $validation = [
            'id' => 'required|integer',
            'status' => 'required|string'
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        VonOrgRepresentativeBackground::where('id', $request->id)->update(['status' => $request->status]);

        $von_app = VonOrgRepresentativeBackground::where('id', $request->id)
                    ->select('name', 'email', 'branch_id')->get();

        if($von_app){
            $hospital_branch = HospitalBranchManagement::where('id', $von_app[0]['branch_id'])
                            ->select('hospital_branch_name')->get();

                if($request->status){
                    $statusid = $request->status;
                    if($statusid == 1){
                        $status = "approved";
                    }
                    else{
                        $status = "rejected";
                    }
                }
            $data = array(
                'name' => $von_app[0]['name'],
                'branch' => ucwords(strtolower($hospital_branch[0]['hospital_branch_name'])),
                'status' => $status,
                'email' => $von_app[0]['email'],
            );

            try{
                Mail::to($data['email'])->send(new VONApplicationMail($data));
            } catch (\Exception $err) {
                var_dump($err);

                return response([
                    'message' => 'Error In Email Configuration: ' . $err,
                    'code' => 500
                ]);
            }
        }


        return response()->json(["message" => "Application Status Updated Successfully", "code" => 200]);
    }

    public function searchList(Request $request)
    {
        $result = [];
        $k = 0;
        $indi = VonOrgRepresentativeBackground::where('section', 'individual')->where('name', $request->name)->where('status', '0')->get();
        if ($indi) {
            foreach ($indi as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Individual';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                $result[$k]['screening'] = ($val['screening_mode'] == '1') ? 'Yes' : 'No';
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        $group = VonOrgRepresentativeBackground::where('section', 'group')->where('name', $request->name)->where('status', '0')->get();
        if ($group) {
            foreach ($group as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $result[$k]['name'] = $val['name'];
                $result[$k]['app_type'] = 'Group';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                $result[$k]['screening'] = 'No';
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        $org = VonOrgRepresentativeBackground::where('section', 'org')->where('name', $request->name)->where('status', '0')->get();
        if ($org) {
            foreach ($group as $key => $val) {
                $result[$k]['id'] = $val['id'];
                $name = VonOrgBackground::where('id', $val['org_background_id'])->get()->pluck('org_name')->toArray();
                $result[$k]['name'] = ($name) ? $name[0] : 'NA';
                $result[$k]['app_type'] = 'Organization';
                $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                $result[$k]['phone_number'] = $val['phone_number'];
                $result[$k]['email'] = $val['email'];
                $result[$k]['screening'] = 'No';
                if ($val['area_of_involvement'] == 'Volunteerism') {
                    $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                    $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                    $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                    $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                }
                $k++;
            }
        }
        return response()->json(["message" => "Application List", 'list' => $result, "code" => 200]);
    }
    public function searchCollList(Request $request)
    {
        $role = DB::table('staff_management')
        ->select('roles.code')
        ->join('roles', 'staff_management.role_id', '=', 'roles.id')
        ->where('staff_management.email', '=', $request->email)
        ->first();

        $search = [];
        if ($request->section != '') {
            $search['section'] = $request->section;
        }
        if ($request->area_of_involvement != '') {
            $search['area_of_involvement'] = $request->area_of_involvement;
        }
        $type = $request->name;

        $result = [];
        $k = 0;
        if ($request->section == 'individual') {
            if (count($search) == 0) {
                if($role->code != 'superadmin'){
                    $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                    ->where('status', '1')
                    ->where($search)
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();

                } else {
                $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                ->where('status', '1')
                ->where('branch_id', $request->branch_id)
                ->where (function($query) use ($request){
                    if ($request->name != '') {
                        return $query->where ('name','LIKE', '%'. $request->name .'%');
                    }
                })->get();
            }
            } else {
                if($role->code != 'superadmin'){
                    $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where($search)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();

                } else {
                    $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                }
            }
            if ($indi) {
                foreach ($indi as $key => $val) {
                    //  $statuscheck = VonAppointment::where('parent_section_id', $val['id'])->count();
                    //  if ($statuscheck > 0) {
                        $result[$k]['id'] = $val['id'];
                        $result[$k]['name'] = $val['name'];
                        $result[$k]['app_type'] = 'Individual';
                        $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                        $result[$k]['phone_number'] = $val['phone_number'];
                        $result[$k]['email'] = $val['email'];
                        $result[$k]['screening'] = ($val['screening_mode'] == '1') ? 'Yes' : 'No';
                        if ($val['area_of_involvement'] == 'Volunteerism') {
                            $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                            $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                            $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        $k++;
                }
            }
        }elseif ($request->section == 'group') {
            if (count($search) == 0) {
                if ($role->code != 'superadmin'){
                    $group = VonOrgRepresentativeBackground::where('section', 'group')
                    ->where('status', '1')
                    ->where($search)
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                } else{
                    $group = VonOrgRepresentativeBackground::where('section', 'group')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                }

            } else {
                if ($role->code != 'superadmin'){
                    $group = VonOrgRepresentativeBackground::where('section', 'group')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where($search)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                } else{
                    $group = VonOrgRepresentativeBackground::where('section', 'group')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                }


            }

            if ($group) {
                foreach ($group as $key => $val) {
                    $result[$k]['id'] = $val['id'];
                    $result[$k]['name'] = $val['name'];
                    $result[$k]['app_type'] = 'Group';
                    $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                    $result[$k]['phone_number'] = $val['phone_number'];
                    $result[$k]['email'] = $val['email'];
                    $result[$k]['screening'] = 'No';
                    if ($val['area_of_involvement'] == 'Volunteerism') {
                        $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                        $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                        $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    $k++;
                }
            }
        }elseif ($request->section == 'organization') {
            if (count($search) == 0) {
                if ($role->code != 'superadmin'){
                    $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                    ->where('section', 'org')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where($search)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                } else{
                    $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                    ->where('section', 'org')
                    ->where('branch_id', $request->branch_id)
                    ->where('status', '1')
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                }
            } else {
                if ($role->code != 'superadmin'){
                    $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                    ->where('section', 'org')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where($search)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                } else{
                    $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                    ->where('section', 'org')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();

                }

            }

            if ($org) {
                foreach ($org as $key => $val) {
                    $result[$k]['id'] = $val['id'];
                    $name = VonOrgBackground::where('id', $val['org_background_id'])->get()->pluck('org_name')->toArray();

                    $result[$k]['name'] = ($name) ? $name[0] : 'NA';
                    $result[$k]['app_type'] = 'Organization';
                    $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                    $result[$k]['phone_number'] = $val['phone_number'];
                    $result[$k]['email'] = $val['email'];
                    $result[$k]['screening'] = 'No';
                    if ($val['area_of_involvement'] == 'Volunteerism') {
                        $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                        $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                        $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    $k++;
                }
            }
        }else {
            if (count($search) == 0) {
                if($role->code != 'superadmin'){
                    $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();
                }else{
                    $indi = VonOrgRepresentativeBackground::where('section', 'individual')
                    ->where('status', '1')
                    ->where('branch_id', $request->branch_id)
                    ->where (function($query) use ($request){
                        if ($request->name != '') {
                            return $query->where ('name','LIKE', '%'. $request->name .'%');
                        }
                    })->get();

                }

            } else {
                if($role->code != 'superadmin'){
                $indi = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                ->where('section', 'individual')
                ->where('status', '1')
                ->where('branch_id', $request->branch_id)
                ->where (function($query) use ($request){
                    if ($request->name != '') {
                        return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                    }
                })->get();
            } else {
                $indi = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                ->where('section', 'individual')
                ->where('status', '1')
                ->where('branch_id', $request->branch_id)
                ->where (function($query) use ($request){
                    if ($request->name != '') {
                        return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                    }
                })->get();

            }
            }
            if ($indi) {
                foreach ($indi as $key => $val) {
                    // $statuscheck = VonAppointment::where('parent_section_id', $val['id'])->count();
                    // if ($statuscheck > 0) {
                        $result[$k]['id'] = $val['id'];
                        $result[$k]['name'] = $val['name'];
                        $result[$k]['app_type'] = 'Individual';
                        $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                        $result[$k]['phone_number'] = $val['phone_number'];
                        $result[$k]['email'] = $val['email'];
                        if ($val['screening_mode'] == '1') {
                            $result[$k]['screening'] = 'Yes';
                        } else if ($val['screening_mode'] == '0'){
                            $result[$k]['screening'] = 'No';
                        }
                        if ($val['area_of_involvement'] == 'Volunteerism') {
                            $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                            $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                            $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                            $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                        }
                        $k++;
                    // }
                }
            }
            if (count($search) == 0) {
                if($role->code != 'superadmin'){
                        $group = VonOrgRepresentativeBackground::where('section', 'group')
                        ->where('status', '1')
                        ->where('branch_id', $request->branch_id)
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();
                    }else{
                        $group = VonOrgRepresentativeBackground::where('section', 'group')
                        ->where('status', '1')
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();

                    }
            } else {
                if ($role->code != 'superadmin'){
                        $group = VonOrgRepresentativeBackground::where('section', 'group')
                        ->where('status', '1')
                        ->where('branch_id', $request->branch_id)
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();
                    } else{
                        $group = VonOrgRepresentativeBackground::where('section', 'group')
                        ->where('status', '1')
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();

                    }
            }
            if ($group) {
                foreach ($group as $key => $val) {
                    $result[$k]['id'] = $val['id'];
                    $result[$k]['name'] = $val['name'];
                    $result[$k]['app_type'] = 'Group';
                    $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                    $result[$k]['phone_number'] = $val['phone_number'];
                    $result[$k]['email'] = $val['email'];
                    $result[$k]['screening'] = 'No';
                    if ($val['area_of_involvement'] == 'Volunteerism') {
                        $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                        $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                        $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    $k++;
                }
            }
            if (count($search) == 0) {
                if($role->code != 'superadmin'){
                        $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                        ->where('section', 'org')
                        ->where('status', '1')
                        ->where('branch_id', $request->branch_id)
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();
                    }else{
                        $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')->where('section', 'org')->where('status', '1')->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();

                    }
            } else {
                if ($role->code != 'superadmin'){
                        $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                        ->where('section', 'org')
                        ->where('status', '1')
                        ->where('branch_id', $request->branch_id)
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();
                    } else{
                        $org = VonOrgRepresentativeBackground::leftJoin('von_org_background','von_org_representative_background.org_background_id','=','von_org_background.id')
                        ->where('section', 'org')
                        ->where('status', '1')
                        ->where (function($query) use ($request){
                            if ($request->name != '') {
                                return $query->where ('name','LIKE', '%'. $request->name .'%')->orWhere('von_org_background.org_name','LIKE', '%'. $request->name .'%');
                            }
                        })->get();

                    }
            }
            if ($org) {
                foreach ($org as $key => $val) {
                    $result[$k]['id'] = $val['id'];
                    $name = VonOrgBackground::where('id', $val['org_background_id'])->get()->pluck('org_name')->toArray();
                    $result[$k]['name'] = ($name) ? $name[0] : 'NA';
                    $result[$k]['app_type'] = 'Organization';
                    $result[$k]['area_of_involvment'] = $val['area_of_involvement'];
                    $result[$k]['phone_number'] = $val['phone_number'];
                    $result[$k]['email'] = $val['email'];
                    $result[$k]['screening'] = 'No';
                    if ($val['area_of_involvement'] == 'Volunteerism') {
                        $services = Volunteerism::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Outreach Project Collaboration') {
                        $services = OutReachProjects::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    if ($val['area_of_involvement'] == 'Networking Make a Contribution') {
                        $services = NetworkingContribution::where('parent_section_id', $val['id'])->get()->pluck('mentari_services')->toArray();
                        $result[$k]['services'] = ($services) ? $services[0] : 'NA';
                    }
                    $k++;
                }
            }
        }
        $results = $result;
        if ($request->service != '') {
            $results = [];
            foreach ($result as $key => $value) {
                if (strpos($value['services'], $request->service) !== false) {
                    $results[] = $result[$key];
                }
            }
        }
        return response()->json(["message" => "Application List", 'list' => $results, "code" => 200]);
    }
}
