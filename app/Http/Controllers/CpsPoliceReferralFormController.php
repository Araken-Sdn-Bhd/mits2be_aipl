<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CpsPoliceReferralForm;

class CpsPoliceReferralFormController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'to' => 'required|string',
            'patient_id' => 'required|integer',
            'officer_in_charge' => 'required|string',
            'the_above_patient_ongoing' => 'required|string',
            'name' => 'required|string',
            'designation' => 'required|string'

        ]);

        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $cpspolicereferralform = [
            'added_by' =>  $request->added_by,
            'patient_id' =>  $request->patient_id,
            'to' =>  $request->to,
            'officer_in_charge' =>  $request->officer_in_charge,
            'the_above_patient_ongoing' =>  $request->the_above_patient_ongoing,
            'name' =>  $request->name,
            'designation' =>  $request->designation
        ];

        try {
            if($request->id){
                CpsPoliceReferralForm::where(['id' => $request->id])->update($cpspolicereferralform);
            }else{
            $HOD = CpsPoliceReferralForm::firstOrCreate($cpspolicereferralform);
            }
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'EtpProgress' => $cpspolicereferralform, "code" => 200]);
        }
        return response()->json(["message" => "CPS Police Form Successfully00", "code" => 200]);

        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $CpsPolice = [
            'added_by' =>  $request->added_by,
            'patient_id' =>  $request->patient_id,
            'to' =>  $request->to,
            'officer_in_charge' =>  $request->officer_in_charge,
            'the_above_patient_ongoing' =>  $request->the_above_patient_ongoing,
            'name' =>  $request->name,
            'designation' =>  $request->designation
        ];

        try {
            if($request->id){
                CpsPoliceReferralForm::where(['id' => $request->id])->update($CpsPolice);
            }else{
                $HOD = CpsPoliceReferralForm::firstOrCreate($CpsPolice);
            }
            
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'CpsPolice' => $CpsPolice, "code" => 200]);
        }
        return response()->json(["message" => "CPS Police Form Successfully11", "code" => 200]);
    }
}
