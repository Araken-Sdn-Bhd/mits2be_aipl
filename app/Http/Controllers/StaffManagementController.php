<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffManagement;
use App\Models\Mentari_Staff_Transfer;
use Exception;
use Validator;
use Illuminate\Support\Facades\DB;

class StaffManagementController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'nric_no' => 'required|string|unique:staff_management',
            'registration_no' => 'required|string|unique:staff_management',
            'role_id' => 'required|integer',
            'email' => 'required|string|unique:staff_management',
            'team_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'contact_no' => 'required|string',
            'designation_id' => 'required|integer',
            'is_incharge' => 'required|string',
            'designation_period_start_date' => 'required',
            'designation_period_end_date' => 'required',
            //'mentari_location' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'mimes:png,jpg,jpeg,pdf|max:10240',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        //dd($request->document);
        if ($request->document == '') {

            $staffadd = [
                'added_by' =>  $request->added_by,
                'name' =>  $request->name,
                'nric_no' =>  $request->nric_no,
                'registration_no' =>  $request->registration_no,
                'role_id' =>  $request->role_id,
                'email' =>  $request->email,
                'team_id' =>  $request->team_id,
                'branch_id' =>  $request->branch_id,
                'contact_no' =>  $request->contact_no,
                'designation_id' =>  $request->designation_id,
                'is_incharge' =>  $request->is_incharge,
                'designation_period_start_date' =>  $request->designation_period_start_date,
                'designation_period_end_date' =>  $request->designation_period_end_date,
                //'mentari_location' =>  $request->mentari_location,
                'start_date' =>  $request->start_date,
                'end_date' =>  $request->end_date,
                'document' =>  $request->document,
                'status' => "1"
            ];
        } else {
            $files = $request->file('document');
            $isUploaded = upload_file($files, 'StaffManagement');
            $staffadd = [
                'added_by' =>  $request->added_by,
                'name' =>  $request->name,
                'nric_no' =>  $request->nric_no,
                'registration_no' =>  $request->registration_no,
                'role_id' =>  $request->role_id,
                'email' =>  $request->email,
                'team_id' =>  $request->team_id,
                'branch_id' =>  $request->branch_id,
                'contact_no' =>  $request->contact_no,
                'designation_id' =>  $request->designation_id,
                'is_incharge' =>  $request->is_incharge,
                'designation_period_start_date' =>  $request->designation_period_start_date,
                'designation_period_end_date' =>  $request->designation_period_end_date,
                //'mentari_location' =>  $request->mentari_location,
                'start_date' =>  $request->start_date,
                'end_date' =>  $request->end_date,
                'document' =>  $isUploaded->getData()->path,
                'status' => "1"
            ];
        }

        try {
            $HOD = StaffManagement::firstOrCreate($staffadd);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Staff' => $staffadd, "code" => 200]);
        }
        return response()->json(["message" => "Record Created Successfully!", "code" => 200]);
    }

    public function getStaffManagementList()
    {
        $users = DB::table('staff_management')
            ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
            ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
            ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
            ->where('staff_management.status', '=', '1')
            ->get();
        return response()->json(["message" => "Staff Management List", 'list' => $users, "code" => 200]);
    }

    public function getStaffManagementListOrById(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->name == '' && $request->branch_id == '0') {
            $users = DB::table('staff_management')
                ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
                ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
                ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
                ->where('staff_management.status', '=', '1')
                //->where('staff_management.name','=', $request->name)
                ->get();
            return response()->json(["message" => "Staff Management List", 'list' => $users, "code" => 200]);
        } else if ($request->name == '') {
            $users = DB::table('staff_management')
                ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
                ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
                ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
                ->where('staff_management.status', '=', '1')
                ->where('staff_management.branch_id', '=', $request->branch_id)
                ->get();
            return response()->json(["message" => "Staff Management List", 'list' => $users, "code" => 200]);
        } else if ($request->branch_id == '0') {
            $users = DB::table('staff_management')
                ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
                ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
                ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
                ->where('staff_management.status', '=', '1')
                ->where('staff_management.name', 'LIKE', "%{$request->name}%", '=', $request->name)
                ->orderBy('staff_management.name', 'asc')
                //'bookname', 'like', '%'.$element.'%'
                ->get();
            return response()->json(["message" => "Staff Management List", 'list' => $users, "code" => 200]);
        } else {
            $users = DB::table('staff_management')
                ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
                ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
                ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
                ->where('staff_management.branch_id', '=', $request->branch_id)
                ->where('staff_management.name', 'LIKE', "%{$request->name}%", '=', $request->name)
                ->orderBy('staff_management.name', 'asc')
                ->get();
            return response()->json(["message" => "Staff Management List else", 'list' => $users, "code" => 200]);
        }
    }

    public function getStaffManagementListById(Request $request)
    {
        $users = DB::table('staff_management')
            ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
            ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
            ->select('staff_management.id', 'staff_management.name', 'designation.designation_name', 'hospital_branch_team_details.hospital_branch_name')
            ->where('staff_management.name', '=', $request->name)
            ->orWhere('staff_management.branch_id', '=', $request->branch_id)
            ->get();
        return response()->json(["message" => "Staff Management List", 'list' => $users, "code" => 200]);
    }

    public function getStaffManagementDetailsById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $users = DB::table('staff_management')
            ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
            ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->select('staff_management.id as Staff_managementId', 'staff_management.name', 'staff_management.nric_no', 'designation.designation_name', 'staff_management.designation_period_start_date', 'staff_management.designation_period_end_date', 'staff_management.registration_no', 'roles.role_name', 'hospital_branch_team_details.team_name', 'staff_management.branch_id', 'staff_management.is_incharge', 'staff_management.contact_no', 'staff_management.email', 'staff_management.status', 'staff_management.start_date', 'staff_management.end_date', 'hospital_branch_team_details.hospital_branch_name')
            ->where('staff_management.id', '=', $request->id)
            ->get();
        return response()->json(["message" => "Staff Management Details", 'list' => $users, "code" => 200]);
    }

    public function editStaffManagementDetailsById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $users = DB::table('staff_management')
            ->join('designation', 'staff_management.designation_id', '=', 'designation.id')
            ->join('hospital_branch_team_details', 'staff_management.team_id', '=', 'hospital_branch_team_details.id')
            ->join('roles', 'staff_management.role_id', '=', 'roles.id')
            ->select('staff_management.id as Staff_managementId', 'staff_management.name', 'staff_management.role_id', 'staff_management.team_id', 'staff_management.nric_no', 'staff_management.branch_id', 'designation.designation_name', 'designation.id as designation_id', 'staff_management.designation_period_start_date', 'staff_management.designation_period_end_date', 'staff_management.registration_no', 'roles.role_name', 'hospital_branch_team_details.team_name', 'hospital_branch_team_details.hospital_branch_name', 'staff_management.branch_id', 'staff_management.is_incharge', 'staff_management.contact_no', 'staff_management.email', 'staff_management.status', 'staff_management.start_date', 'staff_management.end_date')
            ->where('staff_management.id', '=', $request->id)
            ->get();
        return response()->json(["message" => "Staff Management Details", 'list' => $users, "code" => 200]);
    }

    public function updateStaffManagement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'id' => 'required|integer',
            'nric_no' => 'required|string',
            'registration_no' => 'required|string',
            'role_id' => 'required|integer',
            'email' => 'required|string',
            'team_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'contact_no' => 'required|string',
            'designation_id' => 'required|integer',
            'is_incharge' => 'required|string',
            'designation_period_start_date' => 'required',
            'designation_period_end_date' => 'required',
            //'mentari_location' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'mimes:png,jpg,jpeg,pdf|max:10240'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->document == '') {

            $nric_no = $request->nric_no;
            $registration_no = $request->registration_no;
            $email = $request->email;
            $chkPoint =  StaffManagement::where(function ($query) use ($nric_no, $registration_no, $email) {
                $query->where('nric_no', '=', $nric_no)->orWhere('registration_no', '=', $registration_no)->orWhere('email', '=', $email);
            })->where('id', '!=', $request->id)->where('status', '1')->get();
            if ($chkPoint->count() == 0) {
                //dd('die');
                StaffManagement::where(
                    ['id' => $request->id]
                )->update([
                    'added_by' =>  $request->added_by,
                    'name' =>  $request->name,
                    'nric_no' =>  $request->nric_no,
                    'registration_no' =>  $request->registration_no,
                    'role_id' =>  $request->role_id,
                    'email' =>  $request->email,
                    'team_id' =>  $request->team_id,
                    'branch_id' =>  $request->branch_id,
                    'contact_no' =>  $request->contact_no,
                    'designation_id' =>  $request->designation_id,
                    'is_incharge' =>  $request->is_incharge,
                    'designation_period_start_date' =>  $request->designation_period_start_date,
                    'designation_period_end_date' =>  $request->designation_period_end_date,
                    //'mentari_location' =>  $request->mentari_location,
                    'start_date' =>  $request->start_date,
                    'end_date' =>  $request->end_date,
                    'document' =>  $request->document,
                    'status' => "1"
                ]);
                return response()->json(["message" => "Staff Management has updated successfully", "code" => 200]);
            } else {
                return response()->json(["message" => "Changed value already exists!", "code" => 200]);
            }
        } else {
            $files = $request->file('document');
            $isUploaded = upload_file($files, 'StaffManagement');

            $nric_no = $request->nric_no;
            $registration_no = $request->registration_no;
            $email = $request->email;
            $chkPoint =  StaffManagement::where(function ($query) use ($nric_no, $registration_no, $email) {
                $query->where('nric_no', '=', $nric_no)->orWhere('registration_no', '=', $registration_no)->orWhere('email', '=', $email);
            })->where('id', '!=', $request->id)->where('status', '1')->get();
            if ($chkPoint->count() == 0) {
                //dd('die');
                StaffManagement::where(
                    ['id' => $request->id]
                )->update([
                    'added_by' =>  $request->added_by,
                    'name' =>  $request->name,
                    'nric_no' =>  $request->nric_no,
                    'registration_no' =>  $request->registration_no,
                    'role_id' =>  $request->role_id,
                    'email' =>  $request->email,
                    'team_id' =>  $request->team_id,
                    'branch_id' =>  $request->branch_id,
                    'contact_no' =>  $request->contact_no,
                    'designation_id' =>  $request->designation_id,
                    'is_incharge' =>  $request->is_incharge,
                    'designation_period_start_date' =>  $request->designation_period_start_date,
                    'designation_period_end_date' =>  $request->designation_period_end_date,
                    //'mentari_location' =>  $request->mentari_location,
                    'start_date' =>  $request->start_date,
                    'end_date' =>  $request->end_date,
                    'document' =>   $isUploaded->getData()->path,
                    'status' => "1"
                ]);
                return response()->json(["message" => "Staff Management has updated successfully", "code" => 200]);
            } else {
                return response()->json(["message" => "Changed value already exists!", "code" => 200]);
            }
        }
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        StaffManagement::where(
            ['id' => $request->id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Staff Removed From System!", "code" => 200]);
    }

    public function transferstaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'old_branch_id' => 'required|integer',
            'new_branch_id' => 'required|integer',
            'staff_id' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'document' => 'mimes:png,jpg,jpeg,pdf|max:10240'

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->document == '') {
            $staffadd = [
                'added_by' =>  $request->added_by,
                'old_branch_id' =>  $request->old_branch_id,
                'new_branch_id' =>  $request->new_branch_id,
                'staff_id' =>  $request->staff_id,
                'start_date' =>  $request->start_date,
                'end_date' =>  $request->end_date,
                'document' =>  $request->document,
                'status' => "1"
            ];
        } else {
            $files = $request->file('document');
            $isUploaded = upload_file($files, 'TransferStaff');
            $staffadd = [
                'added_by' =>  $request->added_by,
                'old_branch_id' =>  $request->old_branch_id,
                'new_branch_id' =>  $request->new_branch_id,
                'staff_id' =>  $request->staff_id,
                'start_date' =>  $request->start_date,
                'end_date' =>  $request->end_date,
                'document' =>  $isUploaded->getData()->path,
                'status' => "1"
            ];
        }

        try {
            $HOD = Mentari_Staff_Transfer::firstOrCreate($staffadd);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Staff' => $staffadd, "code" => 200]);
        }
        return response()->json(["message" => "Transfer Successfully!", "code" => 200]);
    }
}
