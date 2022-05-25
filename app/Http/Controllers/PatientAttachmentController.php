<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientAttachment;
use Validator;
use Illuminate\Support\Facades\DB;


class PatientAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'file_name' => '',
            'patient_id' => 'required|integer',
            'uploaded_path' => 'required|mimes:png,jpg,jpeg,pdf|max:10240'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $files = $request->file('uploaded_path');
        $isUploaded = upload_file($files, 'PatientAttachment');
        // dd($files->getClientOriginalName());
        if ($isUploaded->getData()->code == 200) {
            $atientattachment = [
                'added_by' => $request->added_by,
                'patient_id' => $request->patient_id,
                'uploaded_path' =>  $isUploaded->getData()->path,
                'file_name' => $files->getClientOriginalName()
            ];
            PatientAttachment::firstOrCreate($atientattachment);
            return response()->json(["message" => "Patient Attachment Created Successfully!", "code" => 200]);
        }
    }

    public function getAttachmentList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = PatientAttachment::select('id','file_name','uploaded_path', 'patient_id', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date"))
            ->where('patient_id', '=', $request->patient_id)
            // ->orWhere('status', '=', '0')
            ->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }
}
