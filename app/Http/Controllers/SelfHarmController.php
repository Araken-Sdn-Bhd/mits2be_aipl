<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelfHarm;
use Validator;
use Illuminate\Support\Facades\DB;

class SelfHarmController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
               'name' => 'required|string',
               'added_by' => 'required|integer'  
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $Self_Harm = [
               'name' =>  $request->name,
               'added_by' =>  $request->added_by
           ];
           try {
               $HOD = SelfHarm::firstOrCreate($Self_Harm);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'Self_Harm' => $Self_Harm, "code" => 200]);
           }
           return response()->json(["message" => "Self Harm Created", "code" => 200]);
    }

       public function getSelfHarmList()
    {
       $list =SelfHarm::select('id', 'name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Self Harm List", 'list' => $list, "code" => 200]);
    }
}
