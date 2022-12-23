<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarException;
use App\Models\HospitalBranchManagement;
use Carbon\Exceptions\Exception;
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
            'type' => 'required|string',
            'branch_id' => 'required|integer'
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
                    'branch_id' =>  $request->branch_id,
                ];
            }
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
            'branch_id' => 'required|integer'
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
            'branch_id' =>  $request->branch_id,
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
        $list = DB::table('calendar_exception as c')    
        ->select('c.id', 'c.name', 'c.start_date', DB::raw("DATE_ADD(c.end_date, INTERVAL 1 DAY) as end_date"),
        'c.end_date as until_date','c.branch_id','c.description')
        ->get();

        foreach($list as $item){
         if ($item->branch_id != 0){
           $branch = HospitalBranchManagement::where('id',$item->branch_id)
            ->select('hospital_branch_name')->first();
            $item->branch_id = $branch->hospital_branch_name;
         }else if ($item->branch_id == 0){
            $item->branch_id ='ALL BRANCH';
         }else{
            $item->branch_id ="";
         }
        }

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
        $list = CalendarException::select('id', 'name', 'start_date', 'end_date','description','branch_id')
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

                        if( $val[2] != ""){
                        $addexception[] = [
                            'added_by' =>  $request->added_by,
                            'branch_id' =>  $val[1],
                            'name' =>  $val[2],
                            'start_date' =>  Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[3])),
                            'end_date' =>   Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[4])),
                            'description' =>  $val[5],
                        ];
                        $HOD = CalendarException::insert($addexception);
                    }
                    }
                }
            }
            try {
                unlink($file);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Exception' => $addexception, "code" => 200]);
            }
            return response()->json(["message" => "Exception Uploaded Successfully!", "code" => 200]);
        }
    }
    public function getExcel(Request $request)
    {
            $filename = 'exception_template'. '.xlsx';
            $filePath = 'CalendarExceptions/'.$filename;
            // $pathToFile = Storage::url($filePath);
            return response()->json(["message" => "KPI Report",  'filepath' => env('APP_URL') .'/'. $filePath, "code" => 200]);
    }
}
