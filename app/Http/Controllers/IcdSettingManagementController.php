<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IcdType;
use App\Models\IcdCategory;
use App\Models\IcdCode;
use Validator;
use Illuminate\Support\Facades\DB;

class IcdSettingManagementController extends Controller
{
    public function addIcdType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'icd_type_code' => 'required|string|unique:icd_type',
            'icd_type_name' => 'required|string',
            'icd_type_description' => 'required|string',
            'icd_type_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $icd_type = [
            'added_by' =>  $request->added_by,
            'icd_type_code' =>  $request->icd_type_code,
            'icd_type_name' =>  $request->icd_type_name,
            'icd_type_description' =>  $request->icd_type_description,
            'icd_type_order' =>  $request->icd_type_order
        ];
        try {
            $HOD = IcdType::updateOrCreate($icd_type);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Icd Type' => $icd_type, "code" => 200]);
        }
        return response()->json(["message" => "Icd Type Created", "code" => 200]);
    }

    public function getIcdTypeCodeList()
    {
        $list = IcdType::select('id', 'icd_type_name', 'icd_type_code', 'icd_type_description', 'icd_type_order')
            ->where('icd_type_status', '=', '1')
            ->orderBy('icd_type_order', 'asc')
            ->get();
        return response()->json(["message" => "Icd Type List", 'list' => $list, "code" => 200]);
    }

    public function addIcdCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'icd_type_id' => 'required|integer',
            'icd_category_code' => 'required|string|unique:icd_category',
            'icd_category_name' => 'required|string',
            'icd_category_description' => 'required|string',
            'icd_category_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $icd_type = [
            'added_by' =>  $request->added_by,
            'icd_type_id' =>  $request->icd_type_id,
            'icd_category_code' =>  $request->icd_category_code,
            'icd_category_name' =>  $request->icd_category_name,
            'icd_category_description' =>  $request->icd_category_description,
            'icd_category_order' =>  $request->icd_category_order
        ];
        try {
            $HOD = IcdCategory::updateOrCreate($icd_type);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Icd Category' => $icd_type, "code" => 200]);
        }
        return response()->json(["message" => "Icd Category Created", "code" => 200]);
    }

    public function addIcdCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'icd_type_id' => 'required|integer',
            'icd_category_id' => 'required|integer',
            'icd_code' => 'required|string|unique:icd_code',
            'icd_name' => 'required|string',
            'icd_description' => 'required|string',
            'icd_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $icd_type = [
            'added_by' =>  $request->added_by,
            'icd_type_id' =>  $request->icd_type_id,
            'icd_category_id' =>  $request->icd_category_id,
            'icd_code' =>  $request->icd_code,
            'icd_name' =>  $request->icd_name,
            'icd_description' =>  $request->icd_description,
            'icd_order' =>  $request->icd_order
        ];
        try {
            $HOD = IcdCode::updateOrCreate($icd_type);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'Icd Code' => $icd_type, "code" => 200]);
        }
        return response()->json(["message" => "Icd Code Created", "code" => 200]);
    }

    public function getIcdTypeWiseCategoryCodeList($id)
    {
        $users = DB::table('icd_type')
            ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
            ->select('icd_category.icd_category_code', 'icd_category.icd_category_name', 'icd_category.id')
            ->where('icd_type.id', '=', $id)
            ->get();
        return response()->json(["message" => "Icd TypeWise Category Code List", 'list' => $users, "code" => 200]);
    }


    public function getIcdCategoryList()
    {
        $users = DB::table('icd_type')
            ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
            ->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_category.icd_category_name', 'icd_category.icd_category_description', 'icd_category.icd_category_order', 'icd_category.id')
            ->where('icd_category.icd_category_status', '=', '1')
            ->orderBy('icd_category.icd_category_order', 'asc')
            ->get();
        return response()->json(["message" => "Icd Category List", 'list' => $users, "code" => 200]);
    }

    public function getIcdcodeList()
    {
        $users = DB::table('icd_code')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
            ->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_code.id', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order')
            ->where('icd_status', '=', '1')
            ->orderBy('icd_code.icd_order', 'asc')
            ->get();
        return response()->json(["message" => "Icd Code List", 'list' => $users, "code" => 200]);
    }

    public function getIcd10codeList()
    {
        $checkicdtype10id = IcdType::select('id')
            ->where('icd_type_code', "ICD-10")
            ->pluck('id');
            // dd($checkicdtype10id);
            $users = DB::table('icd_code')
            ->select('icd_code.icd_code as icd_code','icd_code.id','icd_code.icd_name')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->where('icd_code.icd_type_id', '=', $checkicdtype10id[0])
            ->get();


            $checkicdtype10id_external = IcdType::select('id')
            ->where('icd_type_code', "ICD-10")
            ->pluck('id');
            // dd($checkicdtype10id);
            $users_external = DB::table('icd_code')
            ->select('icd_code.icd_code as icd_code','icd_code.id','icd_code.icd_name')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->where('icd_code.icd_status', '=', '1')
            ->where('icd_code.icd_type_id', '=', $checkicdtype10id_external[0])
            ->where('icd_code.icd_category_id', '=', '14')
            ->get();


        return response()->json(["message" => "Icd-10 Code List", 'list' => $users, 'list_external' => $users_external, "code" => 200]);
    }

    public function getIcd10codeById(Request $request)
    {
        $checkicdtype10id = IcdType::select('id')
            ->where('icd_type_code', "ICD-10")
            ->pluck('id');
            $users = DB::table('icd_type')
            // ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
            ->join('icd_code', 'icd_type.id', '=', 'icd_code.icd_type_id')
            ->select('icd_type.id','icd_code.icd_code as icd_category_code', 'icd_category.id','icd_category.icd_category_name')
            ->where('icd_category_status', '=', '1')
            ->where('icd_category.icd_type_id', '=', $checkicdtype10id[0])
            ->where('icd_category.id', '=', $request->id)
            ->get();
        return response()->json(["message" => "Icd-10 Code List", 'list' => $users, "code" => 200]);
    }

    public function getIcd10codeList2()
    {
        $checkicdtype10id = IcdType::select('id')
            ->where('icd_type_code', "ICD-10")
            ->pluck('id');
            // dd($checkicdtype10id);
            $users = DB::table('icd_code')
            ->select('icd_code.icd_code as icd_code','icd_code.id','icd_code.icd_name')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->where('icd_code.icd_type_id', '=', $checkicdtype10id[0])
            ->get();
            // dd($users);
            $ab=[];
            foreach ($users as $key => $value) {
                // dd($value);
                $ab[$key]['section_value'] =$value->icd_code." ".$value->icd_name;
                $ab[$key]['id'] =$value->id;
            }
        return response()->json(["message" => "Icd-10 Code List", 'list' => $ab, "code" => 200]);
    }

    public function getIcd9codeList()
    {
        $checkicdtype9id = IcdType::select('id')
            ->where('icd_type_code', "ICD-9CM")
            ->pluck('id');
            // dd($checkicdtype10id);
        $users = DB::table('icd_type')
            // ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
            ->select('icd_category.icd_category_code','icd_category.id','icd_category.icd_category_name','icd_category.icd_category_name as section_value')
            ->where('icd_category_status', '=', '1')
            ->where('icd_category.icd_type_id', '=', $checkicdtype9id[0])
            ->get();
        return response()->json(["message" => "ICD-9CM Code List", 'list' => $users, "code" => 200]);
    }
    public function getIcd9codeById(Request $request)
    {
        $checkicdtype9id = IcdType::select('id')
            ->where('icd_type_code', "ICD-9CM")
            ->pluck('id');
            // dd($checkicdtype10id);
        $users = DB::table('icd_type')
            // ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
            ->select('icd_category.icd_category_code','icd_category.id','icd_category.icd_category_name')
            ->where('icd_category_status', '=', '1')
            ->where('icd_category.icd_type_id', '=', $checkicdtype9id[0])
            ->where('icd_category.id','=',$request->id)
            ->get();
        return response()->json(["message" => "ICD-9CM Code List", 'list' => $users, "code" => 200]);
    }
    public function getIcd9subcodeList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'icd_category_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $users = DB::table('icd_code')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
            ->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_code.id', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order','icd_code.icd_name')
            ->where('icd_status', '=', '1')
            ->where('icd_code.icd_category_id', '=', $request->icd_category_code)
            // ->where('icd_code.icd_type_id', '=', $request->icd_category_code)
            ->get();
        return response()->json(["message" => "ICD-9CM Code List", 'list' => $users, "code" => 200]);
    }
    public function getIcd9subcodeById(Request $request)
    {
        $users = DB::table('icd_code')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
            ->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_code.id', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order','icd_code.icd_name')
            ->where('icd_status', '=', '1')
            ->where('icd_code.id','=',$request->id)
            // ->where('icd_code.icd_type_id', '=', $request->icd_category_code)
            ->get();
        return response()->json(["message" => "ICD-9CM Code List", 'list' => $users, "code" => 200]);
    }

    public function getIcd9subcodeList_(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'icd_category_code' => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }
        $checkicdtype9id = IcdType::select('id')
            ->where('icd_type_code', "ICD-9CM")
            ->pluck('id');
            // dd($checkicdtype10id);
        //$users = DB::table('icd_type')
        //    // ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
        //    ->join('icd_category', 'icd_type.id', '=', 'icd_category.icd_type_id')
        //    ->select('icd_category.id as cat_id','icd_category.icd_category_code','icd_category.id','icd_category.icd_category_name')
        //    ->where('icd_category_status', '=', '1')
        //    // ->where('icd_category.icd_type_id', '=', $checkicdtype9id[0])
        //    ->get();

            $users = DB::table('icd_code')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
            //->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_code.id', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order','icd_code.icd_name')
            ->select('icd_category.id as cat_id','icd_category.icd_category_code','icd_category.id','icd_category.icd_category_name')
            ->get();
        // $users = DB::table('icd_code')
        //     ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
        //     ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
        //     ->select('icd_type.icd_type_code','icd_category.id as cat_id', 'icd_category.icd_category_code', 'icd_code.id', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order','icd_code.icd_name')
        //     ->where('icd_status', '=', '1')
        //     ->get();
            // dd($users);
            $ab=[];
            foreach ($users as $key => $value) {
                // dd($value);
                $ab[$key]['section_value'] =$value->icd_category_code.$value->icd_category_name;
                $ab[$key]['id'] =$value->id;
                $ab[$key]['icd_category_code'] =$value->cat_id;
            }
        return response()->json(["message" => "ICD-9CM Code List", 'list' => $ab, "code" => 200]);
    }


    public function updateIcd_type(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'icd_type_code' => 'required|string',
            'icd_type_name' => 'required|string',
            'icd_type_description' => 'required|string',
            'icd_type_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (IcdType::where(['icd_type_code' => $request->icd_type_code])->where('id', '!=', $request->id)->count() == 0) {
            IcdType::where(
                ['id' => $request->id]
            )->update([
                'icd_type_code' => $request->icd_type_code,
                'icd_type_name' => $request->icd_type_name,
                'icd_type_description' =>  $request->icd_type_description,
                'icd_type_order' =>  $request->icd_type_order,
                'added_by' => $request->added_by
            ]);
            return response()->json(["message" => "Icd Type has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->icd_type_code . " already exists", "code" => 200]);
        }
    }

    public function updateIcd_category(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'icd_type_id' => 'required|integer',
            'icd_category_code' => 'required|string',
            'icd_category_name' => 'required|string',
            'icd_category_description' => 'required|string',
            'icd_category_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (IcdCategory::where(['icd_category_code' => $request->icd_category_code])->where('id', '!=', $request->id)->count() == 0) {
            IcdCategory::where(
                ['id' => $request->id]
            )->update([
                'icd_type_id' => $request->icd_type_id,
                'icd_category_code' => $request->icd_category_code,
                'icd_category_name' => $request->icd_category_name,
                'icd_category_description' =>  $request->icd_category_description,
                'icd_category_order' =>  $request->icd_category_order,
                'added_by' => $request->added_by
            ]);
            return response()->json(["message" => "Icd Category has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->icd_category_code . " already exists", "code" => 200]);
        }
    }

    public function updateIcd_code(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'icd_type_id' => 'required|integer',
            'icd_category_id' => 'required|integer',
            'icd_code' => 'required|string',
            'icd_name' => 'required|string',
            'icd_description' => 'required|string',
            'icd_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (IcdCode::where(['icd_code' => $request->icd_code])->where('id', '!=', $request->id)->count() == 0) {
            IcdCode::where(
                ['id' => $request->id]
            )->update([
                'icd_type_id' => $request->icd_type_id,
                'icd_category_id' => $request->icd_category_id,
                'icd_code' => $request->icd_code,
                'icd_name' => $request->icd_name,
                'icd_description' =>  $request->icd_description,
                'icd_order' =>  $request->icd_order,
                'added_by' => $request->added_by
            ]);
            return response()->json(["message" => "Icd Code has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->icd_code . " already exists", "code" => 200]);
        }
    }

    public function editIcdType(Request $request, $id)
    {
        $list = IcdType::select('icd_type_name', 'icd_type_code', 'icd_type_description', 'icd_type_order')
            ->where('id', '=', $id)
            ->get();
        return response()->json(["message" => "Icd Details", 'list' => $list, "code" => 200]);
    }

    public function editIcdCategory(Request $request, $id)
    {
        $users = DB::table('icd_type')
            ->join('icd_category', 'icd_category.icd_type_id', '=', 'icd_type.id')
            ->select('icd_type.icd_type_code', 'icd_type.id as icd_type_id', 'icd_category.icd_category_code', 'icd_category.icd_category_name', 'icd_category.icd_category_description', 'icd_category.icd_category_order', 'icd_category.id as categoryid')
            ->where('icd_category.id', '=', $id)
            ->get();
        return response()->json(["message" => "Icd Category List", 'list' => $users, "code" => 200]);
    }

    public function editIcdcode(Request $request, $id)
    {
        $users = DB::table('icd_code')
            ->join('icd_type', 'icd_code.icd_type_id', '=', 'icd_type.id')
            ->join('icd_category', 'icd_code.icd_category_id', '=', 'icd_category.id')
            ->select('icd_type.icd_type_code', 'icd_category.icd_category_code', 'icd_code.icd_code', 'icd_code.icd_name', 'icd_code.icd_description', 'icd_code.icd_order', 'icd_code.icd_type_id', 'icd_code.icd_category_id', 'icd_code.id as icd_code_id')
            ->where('icd_code.id', '=', $id)
            ->where('icd_code.icd_status', '=', '1')
            ->get();
        return response()->json(["message" => "Icd Code Details", 'list' => $users, "code" => 200]);
    }

    public function removeIcdType(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        IcdType::where(
            ['id' => $request->id]
        )->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }

    public function removeIcdCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        IcdCategory::where(
            ['id' => $request->id]
        )->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }

    public function removeIcdCode(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',

        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        IcdCode::where(
            ['id' => $request->id]
        )->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }
}
