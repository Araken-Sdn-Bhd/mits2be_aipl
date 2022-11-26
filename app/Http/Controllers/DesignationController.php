<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use Validator;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    public function addDesignation(Request $request){
        $validator = Validator::make($request->all(), [
            'designation_name' => 'required|string|unique:designation',
            'designation_order' =>'required'  
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $designation = [
            'designation_name' =>  $request->designation_name,
            'designation_order' =>  $request->designation_order,
        ];
        try {
            $HOD = Designation::firstOrCreate($designation);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Designation' => $designation, "code" => 200]);
        }
        return response()->json(["message" => "Designation Created", "code" => 200]);
    }

    public function getDesignationList()
    {
       $list =Designation::select('id', 'designation_name','designation_order')
       ->where('designation_status','=', '1')
       ->orderBy('designation_name','asc')
       ->get();
       return response()->json(["message" => "Designation List", 'list' => $list, "code" => 200]);
    }
    public function getDesignationListById(Request $request)
    {
       $list =Designation::select('id', 'designation_name','designation_order')
       ->where('designation_status','=', '1')
       ->where('id','=', $request->id)
       ->get();
       return response()->json(["message" => "Designation List", 'list' => $list, "code" => 200]);
    }
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        Designation::where(
            ['id' => $request->id]
        )->update([
            'designation_status' => '0',
            'added_by' => $request->added_by
        ]);
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
       
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation_name' => 'required|string',
            'designation_order' =>'required' 
           
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        Designation::where(
            ['id' => $request->id]
        )->update([
            'designation_name' => $request->designation_name,
            'designation_order' => $request->designation_order
        ]);
        return response()->json(["message" => "Updated Successfully.", "code" => 200]);
       
    }
}
