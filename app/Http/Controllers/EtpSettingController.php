<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtpRegister;
use App\Models\EtpDivision;
use Validator;

class EtpSettingController extends Controller
{
   public function store(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'etp_code' => 'required|string|unique:etp_register',
            'etp_name' => 'required|string|unique:etp_register',
            'etp_description' => 'required|string',
            'etp_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service = [
            'added_by' => $request->added_by,
            'etp_code' => $request->etp_code,
            'etp_name' => $request->etp_name,
            'etp_description' => $request->etp_description,
            'etp_order' => $request->etp_order
        ];
        EtpRegister::firstOrCreate($service);
        return response()->json(["message" => "Etp Registered Successfully!", "code" => 200]);
   }

   public function update(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'etp_code' => 'required|string',
            'etp_name' => 'required|string',
            'etp_description' => 'required|string',
            'etp_order' => 'required|integer',
            'etp_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $etp_name = $request->etp_name;
        $etp_code = $request->etp_code;
        $chkPoint =  EtpRegister::where(function ($query) use ($etp_name, $etp_code) {
            $query->where('etp_name', '=', $etp_name)->orWhere('etp_code', '=', $etp_code);
        })->where('id', '!=', $request->etp_id)->get();
        if ($chkPoint->count() == 0) {
            EtpRegister::where(
                ['id' => $request->etp_id]
            )->update([
                'etp_code' => $etp_code,
                'etp_name' => $etp_name,
                'etp_order' => $request->etp_order,
                'etp_description' => $request->etp_description,
                'added_by' => $request->added_by,
                'status' => $request->status
            ]);
            return response()->json(["message" => "Etp Updated Successfully!", "code" => 200]);
        } else {
            return response()->json(["message" => "Changed value already exists!", "code" => 400]);
        }
   }

   public function remove(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'etp_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        EtpRegister::where(
            ['id' => $request->etp_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Etp Removed From System!", "code" => 200]);
   }

   public function getEtpList()
   {
        $list = EtpRegister::orderBy('etp_order', 'asc')->get();
        return response()->json(["message" => "Etp List.", 'list' => $list, "code" => 200]);
   }
   public function getActiveEtpList()
   {
        $list = EtpRegister::where('status','1')->orderBy('etp_order', 'asc')->get();
        return response()->json(["message" => "Etp List.", 'list' => $list, "code" => 200]);
   }

   public function storeDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'etp_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'division_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $service = [
            'added_by' => $request->added_by,
            'etp_id' => $request->etp_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order
        ];
        $ss = EtpDivision::updateOrCreate(
            [
                'etp_id' => $request->etp_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
            ],
            [
                'added_by' => $request->added_by,
                'etp_id' => $request->etp_id,
                'hospital_id' => $request->hospital_id,
                'branch_id' => $request->branch_id,
                'division_order' => $request->division_order
            ]
        );
        if ($ss)
        {
            return response()->json(["message" => "Etp Registered Successfully!", "code" => 200]);
        }
        else
        {
	         return response()->json(["message" => "Etp Registered Successfully!", "code" => 200]);
        }

            
   }

   public function getDivisionList()
   {
        $list = EtpDivision::with(['etp' => function ($query) {
            $query->select('etp_name', 'id');
        }])->with(['hospitals' => function ($query) {
            $query->select('hospital_name', 'id');
        }])->with(['branchs' => function ($query) {
            $query->select('hospital_branch_name', 'id');
        }])->get();
        return response()->json(["message" => "Etp Division List", 'list' => $list, 'code' => 200]);
   }

   public function getDivisionListbyBranch(Request $request)
   {
        $list = EtpDivision::where('branch_id',$request->branch_id)
        ->with(['etp' => function ($query) {
            $query->select('etp_name', 'id');
        }])->get();
        return response()->json(["message" => "Etp Division List", 'list' => $list, 'code' => 200]);
   }

   public function getDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'division_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = EtpDivision::select('id', 'etp_id', 'hospital_id', 'branch_id', 'division_order','status')->where('id', $request->division_id)->get();
        return response()->json(["message" => "Etp List", 'list' => $list, 'code' => 200]);
   }

   public function updateDivision(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'etp_id' => 'required|integer',
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
            'etp_id' => $request->etp_id,
            'hospital_id' => $request->hospital_id,
            'branch_id' => $request->branch_id,
            'division_order' => $request->division_order,
            'status' => $request->status
        ];

        $sd = EtpDivision::where('id', $request->division_id)->update($division);
        if ($sd)
            return response()->json(["message" => "Etp Division Updated Successfully!", "code" => 200]);
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

        EtpDivision::where(
            ['id' => $request->division_id]
        )->update([
            'status' => '0',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Etp Division Removed From System!", "code" => 200]);
   }

   public function editEtpType(Request $request,$id)
     {
      $list = EtpRegister::select('etp_name', 'etp_code','etp_description','etp_order','status')
      ->where('id','=', $id)
      ->get();
      return response()->json(["message" => "Etp Details", 'list' => $list, "code" => 200]);
    }
}
