<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Psychiatrist;
use Validator;
use Illuminate\Support\Facades\DB;


class PsychiatristController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            // 'patient_id' => 'required|integer',
            'name' => 'required|string'
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $psychiatrist = [
               'added_by' =>  $request->added_by,
            //    'patient_id' =>  $request->patient_id,
               'name' =>  $request->name,
           ];
           try {
               $HOD = Psychiatrist::create($psychiatrist);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'Psychiatrist' => $psychiatrist, "code" => 200]);
           }
           return response()->json(["message" => "Psychiatrist Created", "code" => 200]);
    }
    public function getPsychiatristList()
    {
       $list =Psychiatrist::select('id', 'name')
       ->get();
       return response()->json(["message" => "Psychiatrist List", 'list' => $list, "code" => 200]);
    }
}
