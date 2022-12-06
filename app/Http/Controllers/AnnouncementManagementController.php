<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;
use Validator;
use Exception;
use Response;
use Illuminate\Support\Facades\DB;

class AnnouncementManagementController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'branch_id' => 'required|integer',
            'audience_ids' => 'required|string',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $files = $request->file('document');
        $isUploaded = upload_file($files, 'announcements');
        if ($request->id != null) {
            if ($isUploaded->getData()->code == 200) {
                $announcement = [
                    'added_by' => $request->added_by,
                    'title' => $request->title,
                    'content' => $request->content,
                    'document' =>  $isUploaded->getData()->path,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'branch_id' => $request->branch_id,
                    'audience_ids' => $request->audience_ids,
                    'status' => $request->status
                ];
                Announcement::where('id', $request->id)
                ->update($announcement);

                return response()->json(["message" => "Announcement Created Successfully!", "code" => 200]);
            }
        } else {
            if ($isUploaded->getData()->code == 200) {
                $announcement = [
                    'added_by' => $request->added_by,
                    'title' => $request->title,
                    'content' => $request->content,
                    'document' =>  $isUploaded->getData()->path,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'branch_id' => $request->branch_id,
                    'audience_ids' => $request->audience_ids,
                    'status' => $request->status
                ];
                Announcement::firstOrCreate($announcement);
                return response()->json(["message" => "Announcement Created Successfully!", "code" => 200]);
            }
        }
    }

    public function getAnnouncementList()
    {
        $list = Announcement::select('id', 'title', DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y') as start_date"), DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y') as end_date"), 'status', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as created"))
            ->where('status', '=', '1')
            ->orWhere('status', '=', '0')
            ->orderBy('id','desc')
            ->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }

    public function getAnnouncementDetails(Request $request)
    {
        $users = DB::table('announcement_mgmt')
            ->join('hospital_branch__details', 'announcement_mgmt.branch_id', '=', 'hospital_branch__details.id')
            ->select('announcement_mgmt.id', 'announcement_mgmt.title', 'announcement_mgmt.content', 'announcement_mgmt.document', 'announcement_mgmt.start_date', 'announcement_mgmt.end_date', 'announcement_mgmt.status', 'announcement_mgmt.audience_ids', 'hospital_branch__details.id as hospital_branch_id', 'hospital_branch__details.hospital_branch_name as hospital_name' )
            ->where('announcement_mgmt.id', '=', $request->id)
            //->where('announcement_mgmt.status', '=', '0')
            //->orWhere('announcement_mgmt.status', '=', '1')
            ->get();
        return response()->json(["message" => "List", 'list' => $users, "code" => 200]);
    }

    public function downloadFile(Request $request){
        $headers= [
            'Content-Type' => 'image/png',
         ];
        $fileName = 'q6HDgDvoVyl25JRdAUV4A0SswdqrXbT37BXx1KJk.png';
        $fileLocation = 'assets/announcements/'.$fileName;
        // $pathToFile = Storage::url($fileLocation);
        $pathToFile = $request->document;
        $fullPathToFile = env('APP_URL') . $pathToFile;
        return response()->json(["message" => "Announcement Document",  'filepath' => $pathToFile, "code" => 200]);

        // return response()->download($fullPathToFile);
    }

    public function updateAnnouncementManagement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'id' => 'required|integer',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'branch_id' => 'required|integer',
            'audience_ids' => 'required|string',
            'status' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $files = $request->file('document');
        if ($request->file('document') != null || $request->file('document') != "" ){
        $isUploaded = upload_file($files, 'announcements');
        if ($isUploaded->getData()->code == 200) {
            Announcement::where(
                ['id' => $request->id]
            )->update([
                'added_by' => $request->added_by,
                'title' => $request->title,
                'content' => $request->content,
                'document' => $isUploaded->getData()->path,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'branch_id' => $request->branch_id,
                'audience_ids' => $request->audience_ids,
                'status' => $request->status
            ]);
            return response()->json(["message" => "Announcement Management has updated successfully", "code" => 200]);
        }else {
            return response()->json(["message" => "There is something wrong while saving the file. Please Try Again.", "code" => 500]);
        }
    }else{
        Announcement::where(
            ['id' => $request->id]
        )->update([
            'added_by' => $request->added_by,
            'title' => $request->title,
            'content' => $request->content,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'branch_id' => $request->branch_id,
            'audience_ids' => $request->audience_ids,
            'status' => $request->status
        ]);
        return response()->json(["message" => "Announcement Management has updated successfully", "code" => 200]);

    }

    }

    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        Announcement::where(
            ['id' => $request->id]
        )->update([
            'status' => '2',
            'added_by' => $request->added_by
        ]);

        return response()->json(["message" => "Announcement Removed From System!", "code" => 200]);
    }
    public function getAnnouncementListById(Request $request)
    {
        $list = Announcement::select('id', 'title', DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y') as start_date"), DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y') as end_date"), 'status', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as created_at"))
            ->where('id', '=', $request->id)
            ->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }
}
