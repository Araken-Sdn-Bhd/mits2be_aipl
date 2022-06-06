<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocationServices;
use Validator;
use Illuminate\Support\Facades\DB;
class LocationServicesController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
               'name' => 'required|string',
               'added_by' => 'required|integer'  
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $LocationServices = [
               'name' =>  $request->name,
               'added_by' =>  $request->added_by
           ];
           try {
               $HOD = LocationServices::firstOrCreate($LocationServices);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'LocationServices' => $LocationServices, "code" => 200]);
           }
           return response()->json(["message" => "Location Services Created", "code" => 200]);
    }

       public function getLocationServicesList()
    {
       $list =LocationServices::select('id', 'name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Location Services List", 'list' => $list, "code" => 200]);
    }
}
