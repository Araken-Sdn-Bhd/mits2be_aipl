<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarException;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class CalendarExceptionController extends Controller
{
    public function addexception(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required|string',
            'state' => 'required|string',
            'type' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        if ($request->type == 'update') {
            return $this->update($request);
        } else {
            $addexception = [];
            $stateArray = explode(',', $request->state);
            foreach ($stateArray as $val) {
                $addexception[] = [
                    'added_by' =>  $request->added_by,
                    'name' =>  $request->name,
                    'start_date' =>  $request->start_date,
                    'end_date' =>  $request->end_date,
                    'description' =>  $request->description,
                    'state' =>  $val
                ];
            }
            //dd($addexception);
            try {
                $HOD = CalendarException::insert($addexception);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Exception' => $addexception, "code" => 200]);
            }
            return response()->json(["message" => "Exception Created", "code" => 200]);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'name' => 'required|string',
            'id' => 'required|integer',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required|string',
            'state' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        CalendarException::where(
            ['id' => $request->id]
        )->update([
            'added_by' =>  $request->added_by,
            'name' =>  $request->name,
            'start_date' =>  $request->start_date,
            'end_date' =>  $request->end_date,
            'description' =>  $request->description,
            'state' =>  $request->state,
            'status' => "1"
        ]);
        return response()->json(["message" => "Exception has updated successfully", "code" => 200]);
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

        CalendarException::where(
            ['id' => $request->id]
        )->delete();

        return response()->json(["message" => "Exception Removed Successfully!", "code" => 200]);
    }

    public function getAnnouncementList()
    {
        $list = CalendarException::select('id', 'name', 'start_date', 'end_date')
            ->where('status', '=', '1')
            ->get();
        return response()->json(["message" => "Announcement List", 'list' => $list, "code" => 200]);
    }

    public function getAnnouncementListById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = CalendarException::select('id', 'name', 'start_date', 'end_date')
            ->where('id', '=', $request->id)
            ->get();
        return response()->json(["message" => "Announcement List", 'list' => $list, "code" => 200]);
    }

    public function readExceptions(Request $request)
    {
        if ($request->hasFile('exceptions')) {
            $files = $request->file('exceptions');
            $isUploaded = upload_exception_file($files, 'CalenderExceptions');
            $file = Storage::path('public/' . $isUploaded->getData()->path);
            $addexception = [];
            $data = Excel::toArray([], $file);
            foreach ($data as $k => $v) {
                foreach ($v as $key => $val) {
                    if ($key != 0) {
                        $addexception[] = [
                            'added_by' =>  $request->added_by,
                            'name' =>  $val[2],
                            'start_date' =>  Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[0])),
                            'end_date' =>   Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[1])),
                            'description' =>  $val[4],
                            'state' =>  $val[3]
                        ];
                    }
                }
            }
            try {
                $HOD = CalendarException::insert($addexception);
                unlink($file);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Exception' => $addexception, "code" => 200]);
            }
            return response()->json(["message" => "Exception Uploaded Successfully!", "code" => 200]);
        }
    }
}
