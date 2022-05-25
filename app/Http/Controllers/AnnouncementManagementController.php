<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class AnnouncementManagementController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'title' => 'required|string|unique:announcement_mgmt',
            'content' => 'required|string',
            'document' => 'required|mimes:png,jpg,jpeg,pdf|max:10240',
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

    public function getAnnouncementList()
    {
        $list = Announcement::select('id', 'title', DB::raw("DATE_FORMAT(start_date, '%d-%m-%Y') as start_date"), DB::raw("DATE_FORMAT(end_date, '%d-%m-%Y') as end_date"), 'status', DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as created_at"))
            ->where('status', '=', '1')
            ->orWhere('status', '=', '0')
            ->get();
        return response()->json(["message" => "List", 'list' => $list, "code" => 200]);
    }

    public function getAnnouncementDetails(Request $request)
    {
        $users = DB::table('announcement_mgmt')
            ->join('hospital_branch__details', 'announcement_mgmt.branch_id', '=', 'hospital_branch__details.id')
            ->select('announcement_mgmt.id', 'announcement_mgmt.title', 'announcement_mgmt.content', 'announcement_mgmt.document', 'announcement_mgmt.start_date', 'announcement_mgmt.end_date', 'announcement_mgmt.status', 'announcement_mgmt.audience_ids', 'hospital_branch__details.id as hospital_branch_id')
            ->where('announcement_mgmt.id', '=', $request->id)
            //->where('announcement_mgmt.status', '=', '0')
            //->orWhere('announcement_mgmt.status', '=', '1')
            ->get();
        return response()->json(["message" => "List", 'list' => $users, "code" => 200]);
    }

    public function updateAnnouncementManagement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|string',
            'id' => 'required|integer',
            'title' => 'required|string|unique:announcement_mgmt',
            'content' => 'required|string',
            'document' => 'required|mimes:png,jpg,jpeg,pdf|max:10240',
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
        } else {
            return response()->json(["message" => "There is something wrong while saving the file. Please Try Again.", "code" => 500]);
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
}
