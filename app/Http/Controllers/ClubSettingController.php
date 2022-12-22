<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClubRegister;
use App\Models\ClubDivision;
use DB;
use Validator;

class ClubSettingController extends Controller
{
    public function store(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'club_code' => 'required|string|unique:club_register',
            'club_name' => 'required|string|unique:club_register',
            'club_description' => 'required|string',
            'club_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $club = [
            'added_by' => $request->added_by,
            'club_code' => $request->club_code,
            'club_name' => $request->club_name,
            'club_description' => $request->club_description,
            'club_order' => $request->club_order
        ];
        ClubRegister::firstOrCreate($club);
        return response()->json(["message" => "Club Registered Successfully!", "code" => 200]);
   }

   public function update(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'club_code' => 'required|string',
            'club_name' => 'required|string',
            'club_description' => 'required|string',
            'club_order' => 'required|integer',
            'club_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $club_name = $request->club_name;
        $club_code = $request->club_code;
        $chkPoint =  ClubRegister::where(function ($query) use ($club_name, $club_code) {
            $query->where('club_name', '=', $club_name)->orWhere('club_code', '=', $club_code);
        })->where('id', '!=', $request->club_id)->get();
        if ($chkPoint->count() == 0) {
            ClubRegister::where(
                ['id' => $request->club_id]
            )->update([
                'club_code' => $club_code,
                'club_name' => $club_name,
                'club_order' => $request->club_order,
                'club_description' => $request->club_description,
                'added_by' => $request->added_by,
                'status' => $request->status,
            ]);
            return response()->json(["message" => "Club Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
   }

   public function remove(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'club_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        ClubRegister::where(
            ['id' => $request->club_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Club Removed From System!", "code" => 200]);
   }

   public function getClubList()
   {
        $list = ClubRegister::orderBy('club_order', 'asc')->get();
        return response()->json(["message" => "Club List.", 'list' => $list, "code" => 200]);
   }
   public function getActiveClubList()
   {
        $list = ClubRegister::where('status','1')->orderBy('club_order', 'asc')->get();
        return response()->json(["message" => "Club List.", 'list' => $list, "code" => 200]);
   }

   public function storeDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'club_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'division_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service = [
            'added_by' => $request->added_by,
            'club_id' => $request->club_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order
        ];
        $ss = ClubDivision::updateOrCreate(
            [
                'club_id' => $request->club_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
            ],
            [
                'added_by' => $request->added_by,
                'club_id' => $request->club_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
                'division_order' => $request->division_order
            ]
        );
        if ($ss)
            return response()->json(["message" => "Club Registered Successfully!", "code" => 200]);
   }

   public function getDivisionList()
   {
        $list = ClubDivision::with(['club' => function ($query) {
            $query->select('club_name', 'id');
        }])->with(['hospitals' => function ($query) {
            $query->select('hospital_name', 'id');
        }])->with(['branchs' => function ($query) {
            $query->select('hospital_branch_name', 'id');
        }])->get();
        return response()->json(["message" => "Club List", 'list' => $list, 'code' => 200]);
   }
   public function getDivisionListbyBranch(Request $request)
   {
        $list = ClubDivision::where('branch_id',$request->branch_id)
        ->with(['club' => function ($query) {
            $query->select('club_name', 'id');
        }])->get();

        $club = DB::table('club_division')
        ->select('club_register.club_name', 'club_register.id')
        ->join('club_register', 'club_division.club_id', '=', 'club_register.id')
        ->where('branch_id',$request->branch_id)
        ->get();

        return response()->json(["message" => "Club List", 'list' => $list, 'club' => $club, 'code' => 200]);
   }

   public function getDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'division_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = ClubDivision::select('id', 'club_id', 'hospital_id', 'branch_id', 'division_order','status')->where('id', $request->division_id)->get();
        return response()->json(["message" => "Club List", 'list' => $list, 'code' => 200]);
   }

   public function updateDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'club_id' => 'required|integer',
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
            'club_id' => $request->club_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order,
            'status' => $request->status
        ];

        $sd = ClubDivision::where('id', $request->division_id)->update($division);
        if ($sd)
            return response()->json(["message" => "Club Division Updated Successfully!", "code" => 200]);
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

        ClubDivision::where(
            ['id' => $request->division_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Club Division Removed From System!", "code" => 200]);
   }

   public function getClubListByID(Request $request,$id)
     {
       $list = ClubRegister::select('club_name', 'club_code','club_description','club_order','status')
       ->where('id','=', $id)
       ->get();
       return response()->json(["message" => "Club Details", 'list' => $list, "code" => 200]);
    }
}
