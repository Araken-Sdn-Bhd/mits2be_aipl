<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRegister;
use App\Models\ServiceDivision;
use App\Models\StaffManagement;
use Validator;
use Illuminate\Support\Facades\DB;

class ServiceSettingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'service_code' => 'required|string|unique:service_register',
            'service_name' => 'required|string|unique:service_register',
            'service_description' => 'required|string',
            'service_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service = [
            'added_by' => $request->added_by,
            'service_code' => $request->service_code,
            'service_name' => $request->service_name,
            'service_description' => $request->service_description,
            'service_order' => $request->service_order,
            'status' => '1'
        ];
        ServiceRegister::firstOrCreate($service);
        return response()->json(["message" => "Service Registered Successfully!", "code" => 200]);
    }

    public function update(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'service_code' => 'required|string',
            'service_name' => 'required|string',
            'service_description' => 'required|string',
            'service_order' => 'required|integer',
            'service_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service_name = $request->service_name;
        $service_code = $request->service_code;
        $chkPoint =  ServiceRegister::where(function ($query) use ($service_name, $service_code) {
            $query->where('service_name', '=', $service_name)->orWhere('service_code', '=', $service_code);
        })->where('id', '!=', $request->service_id)->where('status', '1')->get();
        if ($chkPoint->count() == 0) {
            ServiceRegister::where(
                ['id' => $request->service_id]
            )->update([
                'service_code' => $service_code,
                'service_name' => $service_name,
                'service_order' => $request->service_order,
                'service_description' => $request->service_description,
                'added_by' => $request->added_by,
                'status' => $request->status
            ]);
            return response()->json(["message" => "Service Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'service_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ServiceRegister::where(
            ['id' => $request->service_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Service Removed From System!", "code" => 200]);
    }

    public function getSerivceList()
    {
        $list = ServiceRegister::orderBy('service_order', 'asc')->get();
        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }
    public function getActiveServiceList()
    {
        $list = ServiceRegister::where('status','=','1')->orderBy('service_order', 'asc')->get();
        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function getServiceList(Request $request)
    {
        $list = StaffManagement::select('id', 'branch_id')
        ->where('email','=', $request->email)->get();

        $list2 = DB::table('service_register')
        ->join('service_division', 'service_register.id', '=', 'service_id')
        ->select('service_register.id', 'service_register.service_name', 'service_division.branch_id')
        ->where('service_register.status','=', '1')
        ->where('service_division.branch_id','=', $list[0]['branch_id'])
        ->orderBy('service_order', 'asc')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list2, "code" => 200]);
    }

    public function getServiceListByBranch(Request $request)
    {
        $listService = DB::table('service_register')
        ->join('service_division', 'service_register.id', '=', 'service_id')
        ->select('service_register.id', 'service_register.service_name', 'service_division.branch_id')
        ->where('service_register.status','=', '1')
        ->where('service_division.branch_id','=', $request->branchId)
        ->orderBy('service_order', 'asc')
        ->get();

        return response()->json(["message" => "List.", 'list' => $listService, "code" => 200]);
    }

    public function storeDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'service_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'division_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service = [
            'added_by' => $request->added_by,
            'service_id' => $request->service_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order
        ];
        $ss = ServiceDivision::updateOrCreate(
            [
                'service_id' => $request->service_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
            ],
            [
                'added_by' => $request->added_by,
                'service_id' => $request->service_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
                'division_order' => $request->division_order
            ]
        );
        if ($ss)
            return response()->json(["message" => "Service Registered Successfully!", "code" => 200]);
    }

    public function getDivisionList()
    {
        $list = ServiceDivision::with(['services' => function ($query) {
            $query->select('service_name', 'id');
        }])->with(['hospitals' => function ($query) {
            $query->select('hospital_name', 'id');
        }])->with(['branchs' => function ($query) {
            $query->select('hospital_branch_name', 'hospital_code', 'id');
        }])->get();
        return response()->json(["message" => "List", 'list' => $list, 'code' => 200]);
    }

    public function getDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $users = DB::table('service_division')
            ->join('hospital_management', 'service_division.hospital_id', '=', 'hospital_management.id')
            ->select('service_division.id', 'service_division.service_id', 'service_division.hospital_id', 'service_division.branch_id',
             'service_division.division_order','hospital_management.hospital_code','service_division.status')

            ->where('service_division.id','=', $request->division_id)
            ->get();
        return response()->json(["message" => "Service List", 'list' => $users, 'code' => 200]);
    }

    public function updateDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'service_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'division_order' => 'required|integer',
            'division_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $division = [
            'added_by' => $request->added_by,
            'service_id' => $request->service_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order,
            'status' => $request->status
        ];

        $sd = ServiceDivision::where('id', $request->division_id)->update($division);
        if ($sd)
            return response()->json(["message" => "Service Division Updated Successfully!", "code" => 200]);
    }

    public function removeDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'division_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ServiceDivision::where(
            ['id' => $request->division_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Service Division Removed From System!", "code" => 200]);
    }

    public function getServiceListById(Request $request)
    {
        $validator = Validator::make($request->all(), ['id' => 'required|integer']);
        if ($validator->fails()) return  response()->json(["message" => $validator->errors(), "code" => 422]);
        $list = ServiceRegister::select('id', 'service_code','service_name','service_description','service_order','status')->where('id', $request->id)->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }
}
