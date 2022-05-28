<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreasOfInvolvement;
use Validator;
use Illuminate\Support\Facades\DB;

class AreasOfInvolvementController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
               'name' => 'required|string|unique:areas_of_involvement',
               'added_by' => 'required|integer'  
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $areas_of_involvement = [
               'name' =>  $request->name,
               'added_by' =>  $request->added_by
           ];
           try {
               $HOD = AreasOfInvolvement::firstOrCreate($areas_of_involvement);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'Areas_Of_Involvement' => $areas_of_involvement, "code" => 200]);
           }
           return response()->json(["message" => "Areas Of Involvement Created", "code" => 200]);
       }

       public function getAreasOfInvolvementList()
    {
       $list =AreasOfInvolvement::select('id', 'name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Areas Of Involvement List", 'list' => $list, "code" => 200]);
    }
}
