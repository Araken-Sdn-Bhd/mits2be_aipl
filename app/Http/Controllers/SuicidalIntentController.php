<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuicidalIntent;
use Validator;
use Illuminate\Support\Facades\DB;

class SuicidalIntentController extends Controller
{
    public function add(Request $request){
        $validator = Validator::make($request->all(), [
               'name' => 'required|string',
               'added_by' => 'required|integer'  
           ]);
           if ($validator->fails()) {
               return response()->json(["message" => $validator->errors(), "code" => 422]);
           }
           $SuicidalIntent = [
               'name' =>  $request->name,
               'added_by' =>  $request->added_by
           ];
           try {
               $HOD = SuicidalIntent::firstOrCreate($SuicidalIntent);
           } catch (Exception $e) {
               return response()->json(["message" => $e->getMessage(), 'Suicidal_Intent' => $SuicidalIntent, "code" => 200]);
           }
           return response()->json(["message" => "Suicidal Intent Created", "code" => 200]);
    }

       public function getSuicidalList()
    {
       $list =SuicidalIntent::select('id', 'name')
       ->where('status','=', '1')
       ->get();
       return response()->json(["message" => "Suicidal Intent List", 'list' => $list, "code" => 200]);
    }
}
