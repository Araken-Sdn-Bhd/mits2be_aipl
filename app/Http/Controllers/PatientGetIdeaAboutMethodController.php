<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientGetIdeaAboutMethod;
use Validator;
use Illuminate\Support\Facades\DB;

class PatientGetIdeaAboutMethodController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
               'name' => 'required|string',
               'added_by' => 'required|integer'  
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $PatientGetIdeaAboutMethod = [
               'name' =>  $request->name,
               'added_by' =>  $request->added_by
           ];
           try {
               $HOD = PatientGetIdeaAboutMethod::firstOrCreate($PatientGetIdeaAboutMethod);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'PatientGetIdeaAboutMethod' => $PatientGetIdeaAboutMethod, "code" => 200]);
           }
           return response()->json(["message" => "Patient Get Idea About Method", "code" => 200]);
    }

       public function getPatientGetIdeaAboutMethodList()
    {
       $list =PatientGetIdeaAboutMethod::select('id', 'name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Patient Get Idea About Method List", 'list' => $list, "code" => 200]);
    }
}
