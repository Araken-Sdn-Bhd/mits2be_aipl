<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HospitalManagement;
use App\Models\HospitalHODManagement;
use App\Models\HospitalBranchManagement;
use App\Models\HospitalBranchTeamManagement;
use App\Models\PatientRegistration;
use App\Models\StaffManagement;
use Exception;
use Illuminate\Support\Facades\DB;
use Validator;

class HospitalManagementController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'salutation' => 'required|integer',
            'name' => 'required|string',
            'gender' => 'required|integer',
            'citizenship' => 'required|integer',
            'passport_nric_no' => 'required|string',
            'religion' => 'required|integer',
            'designation' => 'required|integer',
            'email' => 'required|email|unique:hospital_hod_psychiatrist_details',
            'contact_mobile' => 'required|string',
            'contact_office' => 'required|string',
            'hospital_code' => 'required|string|unique:hospital_management',
            'hospital_prefix' => 'required|string',
            'hospital_name' => 'required|string',
            'hospital_adrress_1' => 'required|string',
            'hospital_state' => 'required|integer',
            'hospital_city' => 'required|integer',
            'hospital_postcode' => 'required|integer',
            'hospital_contact_number' => 'required|string',
            'hospital_email' => 'required|email',
            'hospital_fax_no' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $hod_psychiatrist = [
            'added_by' =>  $request->added_by,
            'salutation' =>  $request->salutation,
            'name' =>  $request->name,
            'gender' =>  $request->gender,
            'citizenship' =>  $request->citizenship,
            'passport_nric_no' =>  $request->passport_nric_no,
            'religion' =>  $request->religion,
            'designation' =>  $request->designation,
            'email' =>  $request->email,
            'contact_mobile' =>  $request->contact_mobile,
            'contact_office' =>  $request->contact_office,
            'status' => "1"
        ];
        try {
            $HOD = HospitalHODManagement::firstOrCreate($hod_psychiatrist);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'hod' => $hod_psychiatrist, "code" => 200]);
        }

        $hospital = [
            'hod_psychiatrist_name' => $request->name,
            'hod_psychiatrist_id' => $HOD->id,
            'added_by' =>  $request->added_by,
            'hospital_code' =>  $request->hospital_code,
            'hospital_prefix' =>  $request->hospital_prefix,
            'hospital_name' =>  $request->hospital_name,
            'hospital_adrress_1' =>  $request->hospital_adrress_1,
            'hospital_adrress_2' =>  $request->hospital_adrress_2,
            'hospital_adrress_3' =>  $request->hospital_adrress_3,
            'hospital_state' =>  $request->hospital_state,
            'hospital_city' =>  $request->hospital_city,
            'hospital_postcode' =>  $request->hospital_postcode,
            'hospital_contact_number' =>  $request->hospital_contact_number,
            'hospital_email' => $request->hospital_email,
            'hospital_fax_no' => $request->hospital_fax_no,
            'hospital_status' => "1"
        ];
        try {
            HospitalManagement::firstOrCreate($hospital);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'hospital' => $hospital, "code" => 400]);
        }
        return response()->json(["message" => "Record Created Successfully!", "code" => 200]);
    }
    public function updatehospital(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'salutation' => 'required|integer',
            'name' => 'required|string',
            'gender' => 'required|integer',
            'citizenship' => 'required|integer',
            'passport_nric_no' => 'required|string',
            'religion' => 'required|integer',
            'designation' => 'required|integer',
            'email' => 'required|email',
            'contact_mobile' => 'required|string',
            'contact_office' => 'required|string',
            'hospital_code' => 'required|string',
            'hospital_prefix' => 'required|string',
            'hospital_name' => 'required|string',
            'hospital_adrress_1' => 'required|string',
            'hospital_state' => 'required|integer',
            'hospital_city' => 'required|integer',
            'hospital_postcode' => 'required|integer',
            'hospital_contact_number' => 'required|string',
            'hospital_email' => 'required|email',
            'hospital_fax_no' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        try {
            HospitalHODManagement::where(
                ['id' => $request->id]
            )->update([
                'added_by' =>  $request->added_by,
                'salutation' =>  $request->salutation,
                'name' =>  $request->name,
                'gender' =>  $request->gender,
                'citizenship' =>  $request->citizenship,
                'passport_nric_no' =>  $request->passport_nric_no,
                'religion' =>  $request->religion,
                'designation' =>  $request->designation,
                'email' =>  $request->email,
                'contact_mobile' =>  $request->contact_mobile,
                'contact_office' =>  $request->contact_office,
                'status' => "1"
            ]);
    
            HospitalManagement::where(
                ['hod_psychiatrist_id' => $request->id]
            )->update([
                'hod_psychiatrist_name' => $request->name,
                // 'hod_psychiatrist_id' => $HOD->hod_psychiatrist_id,
                'added_by' =>  $request->added_by,
                'hospital_code' =>  $request->hospital_code,
                'hospital_prefix' =>  $request->hospital_prefix,
                'hospital_name' =>  $request->hospital_name,
                'hospital_adrress_1' =>  $request->hospital_adrress_1,
                'hospital_adrress_2' =>  $request->hospital_adrress_2,
                'hospital_adrress_3' =>  $request->hospital_adrress_3,
                'hospital_state' =>  $request->hospital_state,
                'hospital_city' =>  $request->hospital_city,
                'hospital_postcode' =>  $request->hospital_postcode,
                'hospital_contact_number' =>  $request->hospital_contact_number,
                'hospital_email' => $request->hospital_email,
                'hospital_fax_no' => $request->hospital_fax_no,
                'hospital_status' => "1"
            ]);
            return response()->json(["message" => "Updated Successfully.", "code" => 200]);
 
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Something Went Wrong", "code" => 501]);
        }
      
       
    }
    public function storeBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'hospital_id' => 'required|integer',
            'hospital_code' => 'required|string|exists:hospital_management,hospital_code',
            'hospital_branch_name' => 'required|string',
            'isHeadquator' => 'required|integer',
            'branch_adrress_1' => 'required|string',
            'branch_state' => 'required|integer',
            'branch_city' => 'required|integer',
            'branch_postcode' => 'required|integer',
            'branch_contact_number_office' => 'required|string',
            'branch_contact_number_mobile' => 'required|string',
            'branch_email' => 'required|string',
            'branch_fax_no' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $branch = [
            'added_by' =>  $request->added_by,
            'hospital_id' =>  $request->hospital_id,
            'hospital_code' =>  $request->hospital_code,
            'hospital_branch_name' =>  $request->hospital_branch_name,
            'isHeadquator' =>  $request->isHeadquator,
            'branch_adrress_1' =>  $request->branch_adrress_1,
            'branch_adrress_2' =>  $request->branch_adrress_2,
            'branch_adrress_3' =>  $request->branch_adrress_3,
            'branch_state' =>  $request->branch_state,
            'branch_city' =>  $request->branch_city,
            'branch_postcode' =>  $request->branch_postcode,
            'branch_contact_number_office' =>  $request->branch_contact_number_office,
            'branch_contact_number_mobile' =>  $request->branch_contact_number_mobile,
            'branch_email' => $request->branch_email,
            'branch_fax_no' => $request->branch_fax_no,
            'branch_status' => 1
        ];
        try {
            HospitalBranchManagement::firstOrCreate($branch);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'branch' => $branch, "code" => 200]);
        }

        return response()->json(["message" => "Branch Created Successfully!", "code" => 200]);
    }

    public function storeBranchTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'hospital_id' => 'required|integer',
            'hospital_code' => 'required|string',
            'hospital_branch_name' => 'required|string',
            'hospital_branch_id' => 'required|integer',
            'team_name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $team = [
            'added_by' =>  $request->added_by,
            'hospital_id' =>  $request->hospital_id,
            'hospital_code' =>  $request->hospital_code,
            'hospital_branch_name' =>  $request->hospital_branch_name,
            'hospital_branch_id' =>  $request->hospital_branch_id,
            'team_name' =>  $request->team_name,
            'status' => 1
        ];
        try {
            HospitalBranchTeamManagement::firstOrCreate($team);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'team' => $team, "code" => 200]);
        }

        return response()->json(["message" => "Branch Team Created Successfully!", "code" => 200]);
    }

    public function getBranchByHospitalCode(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'hospital_code' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $branchs = HospitalBranchManagement::select('id', 'hospital_branch_name')->where('hospital_code', $request->hospital_code)
        ->orWhere('hospital_id', $request->hospital_code)->get();
        return response()->json(["message" => "Branch List", 'branches' => $branchs, "code" => 200]);
    }

    public function getHospitalList()
    {
        $list = HospitalManagement::select('id', 'hospital_name', 'hospital_prefix', 'hospital_code', 'hospital_contact_number', 'hospital_fax_no', 'hod_psychiatrist_name')->get();
        return response()->json(["message" => "Hospital List", 'list' => $list, "code" => 200]);
    }

    public function getHospitalBranchList()
    {
        $list = HospitalBranchManagement::select('id', 'hospital_branch_name', 'branch_adrress_1', 'hospital_code', 'branch_adrress_2', 'branch_adrress_3', 'branch_contact_number_office', 'branch_fax_no')
        ->where('branch_status','=', '1')
        ->get();
        return response()->json(["message" => "Hospital Branch List", 'list' => $list, "code" => 200]);
    }

    public function getHospitalBranchTeamList()
    {
        $list = HospitalBranchTeamManagement::select('id', 'hospital_branch_name', 'team_name', 'hospital_code')->where('status','=', '1')->get();
        return response()->json(["message" => "Hospital Branch Team List", 'list' => $list, "code" => 200]);
    }
    public function getHospitalBranchTeamListPatient(Request $request)
    {
        $added_by =  PatientRegistration::select('added_by')->where('id', '=', $request->patient_id)
        ->get();
        // dd($added_by[0]['added_by']);
        $users = DB::table('patient_registration')
            ->join('users', 'patient_registration.added_by', '=', 'users.id')
            ->select('users.email')
            ->where('patient_registration.added_by', '=', $added_by[0]['added_by'])
            ->get();
        // dd($users[0]);
        $result = [];
        if ($users) {
            $tmp = json_decode(json_encode($users[0]), true)['email'];
            // $team =  StaffManagement::select('is_incharge')->where('email', '=', $tmp)
            //     ->get();
                $branch_id =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                ->get();
                // dd($branch_id[0]['branch_id']);
            // if (!empty($branch_id[0]['branch_id'])) {
                // $pc = HospitalBranchTeamManagement::where(['hospital_branch_id' => $branch_id[0]['branch_id']])->where('status','=', '1')->get()->toArray();
                $pc = StaffManagement::where(['branch_id' => $branch_id[0]['branch_id']])->where('team_id','=', $request->team_id)->get()->toArray();
                foreach ($pc as $key => $value) {
                    // dd($value);
                    $result[$key]['team_name'] =  $value['name'] ?? 'NA';
                    $result[$key]['id'] =  $value['id'] ?? 'NA';
                }
               
            // } else {
            //     $result[0]['team_name'] = 'NA';
            // }
        }
        return response()->json(["message" => "Hospital Staff", 'details' => $result, "code" => 200]);
    }

    public function getServiceByTeamId(Request $request)
    {
        $list = StaffManagement::select('team_id','branch_id')
        ->where('email','=', $request->email)->get();

        $list2 = StaffManagement::select('id', 'name')
        ->where('branch_id','=', $list[0]['branch_id'])
        ->where('team_id','=', $list[0]['team_id'])->get();

        return response()->json(["message" => "Staff Name", 'list' => $list2, "code" => 200]);
    
    }

            public function getServiceByBranchTeamId(Request $request) //faiz&amir
    {
        $list = StaffManagement::select('id', 'team_id', 'branch_id')->where('email','=', $request->email)->get();
        $list2 = StaffManagement::select('id', 'team_id', 'name')->where('branch_id','=', $list[0]['branch_id'])->where('team_id','=', $request->appointment_type)->get();
        return response()->json(["message" => "Staff Name", 'list' => $list2, "code" => 200]);
    }

    public function getHospitalBranchListByHospital(Request $request)
    {
        $validator = Validator::make($request->all(), ['hospital_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = HospitalBranchManagement::select('id', 'hospital_branch_name')->where('hospital_id', $request->hospital_id)->get();
        return response()->json(["message" => "Hospital Branch List", 'list' => $list, "code" => 200]);
    }

    public function getHospitalBranchTeamListByBranch(Request $request)
    {
        $validator = Validator::make($request->all(), ['hospital_id' => 'required|integer', 'branch_id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = HospitalBranchTeamManagement::select('id', 'team_name')->where(['hospital_id' => $request->hospital_id, 'hospital_branch_id' => $request->branch_id])->get();
        return response()->json(["message" => "Hospital Branch Team List", 'list' => $list, "code" => 200]);
    }

    public function getHospitalListById($id)
    {
        $hm = HospitalManagement::find($id);
        $hospital['hospital_id'] = $hm['id'];
        $hospital['name'] = $hm['hospital_name'];
        $hospital['code'] = $hm['hospital_code'];
        $hospital['prefix'] = $hm['hospital_prefix'];
        // $hospital['address'] = $hm['hospital_adrress_1'] . '<br />' . $hm['hospital_adrress_2'] . '<br />' . $hm['hospital_adrress_3'];
        $hospital['address1'] = $hm['hospital_adrress_1'];
        $hospital['address2']=$hm['hospital_adrress_2'];
        $hospital['address3']= $hm['hospital_adrress_3'];
        $hospital['state'] = $hm->states->state_name;
        $hospital['state_id'] = $hm->states->id;
        $hospital['city'] = $hm->cities->city_name;
        $hospital['city_id'] = $hm->cities->id;
        $hospital['postcode_id'] = $hm->cities->id;
        $hospital['postcode'] = $hm->cities->postcode;
        $hospital['email'] = $hm['hospital_email'];
        $hospital['contact'] = $hm['hospital_contact_number'];
        $hospital['status'] = $hm['hospital_status'];
        $hospital['fax'] = $hm['hospital_fax_no'];
        // dd($hm);
        $hbm = HospitalHODManagement::find($hm['hod_psychiatrist_id']);
        //dd($hbm);
        $hod['name'] = $hbm['name'];
        $hod['nric'] = $hbm['passport_nric_no'];
        $hod['email'] = $hbm['email'];
        $hod['contact_mobile'] = $hbm['contact_mobile'];
        $hod['contact_office'] = $hbm['contact_office'];
        $hod['salutation'] = $hbm->salutations->section_value;
        $hod['salutation_id'] = $hbm->salutations->id;
        $hod['religion'] = $hbm->religions->section_value;
        $hod['religion_id'] = $hbm->religions->id;
        $hod['gender'] = $hbm->genders->section_value;
        $hod['gender_id'] = $hbm->genders->id;
        $hod['citizenship'] =  $hbm['citizenship'];
        if($hbm->citizenships){
        $hod['citizenship_name'] =  $hbm->citizenships->citizenship_name;
        }else{
            $hod['citizenship_name'] =  'NA';
        }
        $hod['designation'] =  $hbm['designation'];
        if($hbm->designations){
            $hod['designation_name'] =  $hbm->designations->designation_name;
            }else{
                $hod['designation_name'] =  'NA';
            }
        return response()->json(["message" => "Hospital & HOD Psychiatrist", 'list' => ['hospital' => $hospital, 'psychiatrist' => $hod], "code" => 200]);
    }

    public function get_branch_by_id($id)
    {
        $hm = HospitalBranchManagement::find($id);
        $branch['hospital_code'] = $hm['hospital_code'];
        $branch['hospital_id'] = $hm['hospital_id'];
        $branch['hospital_branch_name'] = $hm['hospital_branch_name'];
        $branch['isHeadquator'] = $hm['isHeadquator'];
        $branch['address1'] = $hm['branch_adrress_1'];
        $branch['address2'] = $hm['branch_adrress_2'];
        $branch['address3'] = $hm['branch_adrress_3'];
        $branch['branch_state'] = $hm['branch_state'];
        $branch['branch_city'] = $hm['branch_city'];
        $branch['branch_postcode'] = $hm['branch_postcode'];
        $branch['branch_email'] = $hm['branch_email'];
        $branch['branch_contact_number_office'] = $hm['branch_contact_number_office'];
        $branch['branch_contact_number_mobile'] = $hm['branch_contact_number_mobile'];
        $branch['branch_fax_no'] = $hm['branch_fax_no'];
        // dd($hm);
       
        return response()->json(["message" => "Branch List", 'list' => $branch, "code" => 200]);
    }

    public function get_team_by_id($id)
    {
        $hm = HospitalBranchTeamManagement::find($id);
        $branchteam['hospital_code'] = $hm['hospital_code'];
        $branchteam['hospital_id'] = $hm['hospital_id'];
        $branchteam['hospital_branch_id'] = $hm['hospital_branch_id'];
        $branchteam['hospital_branch_name'] = $hm['hospital_branch_name'];
        $branchteam['team_name'] = $hm['team_name'];
        // dd($hm);
       
        return response()->json(["message" => "Branch Team List", 'list' => $branchteam, "code" => 200]);
    }


    public function updateHospitalBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
            'hospital_id' => 'required|integer',
            'hospital_code' => 'required|string',
            'hospital_branch_name' => 'required|string',
            'isHeadquator' => 'required|integer',
            'branch_adrress_1' => 'required|string',
            'branch_state' => 'required|integer',
            'branch_city' => 'required|integer',
            'branch_postcode' => 'required|integer',
            'branch_contact_number_office' => 'required|string',
            'branch_contact_number_mobile' => 'required|string',
            'branch_email' => 'required|string',
            'branch_fax_no' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
            }
        $branch_city = $request->branch_city;
        $branch_postcode = $request->branch_postcode;
        
        $chkPoint =  HospitalBranchManagement::where(function ($query) use ($branch_city, $branch_postcode) {
        $query->where('branch_city', '=', $branch_city)->where('branch_postcode', '=', $branch_postcode);
        })->where('id', '!=', $request->id)->where('branch_status', '1')->get();
         if ($chkPoint->count() == 0) {
            //dd('die');
            HospitalBranchManagement::where(
            ['id' => $request->id]
            )->update([
            'added_by' =>  $request->added_by,
            'hospital_id' =>  $request->hospital_id,
            'hospital_code' =>  $request->hospital_code,
            'hospital_branch_name' =>  $request->hospital_branch_name,
            'isHeadquator' =>  $request->isHeadquator,
            'branch_adrress_1' =>  $request->branch_adrress_1,
            'branch_adrress_2' =>  $request->branch_adrress_2,
            'branch_adrress_3' =>  $request->branch_adrress_3,
            'branch_state' =>  $request->branch_state,
            'branch_city' =>  $request->branch_city,
            'branch_postcode' =>  $request->branch_postcode,
            'branch_contact_number_office' =>  $request->branch_contact_number_office,
            'branch_contact_number_mobile' =>  $request->branch_contact_number_mobile,
            'branch_email' => $request->branch_email,
            'branch_fax_no' => $request->branch_fax_no,
            'branch_status' => 1
            ]);
            return response()->json(["message" => "Branch has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" =>"Changed value already exists!", "code" => 200]);
        }

      }


      public function updateHospitalBranchTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
            'hospital_id' => 'required|integer',
            'hospital_code' => 'required|string',
            'hospital_branch_name' => 'required|string',
            'hospital_branch_id' => 'required|integer',
            'team_name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
            }
        $hospital_code = $request->hospital_code;
        $hospital_branch_id = $request->hospital_branch_id;
        
         $chkPoint =  HospitalBranchTeamManagement::where(['hospital_code' => $hospital_code, 'hospital_branch_id' => $request->hospital_branch_id, 'status' => '1','hospital_branch_name' =>  $request->hospital_branch_name,])->where('id','!=',$request->id)->get();
         if ($chkPoint->count() == 0) {
            //dd('die');
            HospitalBranchTeamManagement::where(
            ['id' => $request->id]
            )->update([
            'added_by' =>  $request->added_by,
            'hospital_id' =>  $request->hospital_id,
            'hospital_code' =>  $request->hospital_code,
            'hospital_branch_name' =>  $request->hospital_branch_name,
            'hospital_branch_id' =>  $request->hospital_branch_id,
            'team_name' =>  $request->team_name,
            'status' => 1
            ]);
            return response()->json(["message" => "Branch Team has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" =>"Changed value already exists!", "code" => 200]);
        }

      }

       public function removeBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
         HospitalBranchManagement::where(
            ['id' => $request->id]
        )->update([
            'branch_status' => '0',
            'added_by' => $request->added_by
        ]);
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
       
    }

     public function removeBranchTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
         HospitalBranchTeamManagement::where(
            ['id' => $request->id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
       
    }
}
