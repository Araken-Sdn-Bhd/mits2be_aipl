<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExternalCauseInjury;
use Validator;
use Illuminate\Support\Facades\DB;

class ExternalCauseInjuryController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string'
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $ExternalCauseInjury = [
               'added_by' =>  $request->added_by,
               'name' =>  $request->name,
           ];
           try {
               $HOD = ExternalCauseInjury::create($ExternalCauseInjury);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'External Cause Injury' => $ExternalCauseInjury, "code" => 200]);
           }
           return response()->json(["message" => "External Cause Injury Created", "code" => 200]);
    }
    public function getExternalCauseList()
    {
       $list =ExternalCauseInjury::select('id', 'name')
       ->get();
       return response()->json(["message" => "External Cause Of Injury List", 'list' => $list, "code" => 200]);
    }
}
