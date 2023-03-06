<?php

namespace App\Http\Controllers;
use App\Models\FaqCategory;
use Validator;
use DB;

use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function getListCategory(Request $request)
    {
        $list = DB::table('faq_category')
        ->select('faq_category_id','faq_category','index','isactive')
        ->where('isactive',0)
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function getAllCategory(Request $request)
    {
        $list = DB::table('faq_category')
        ->select('faq_category_id','faq_category','index','isactive')
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faq_category' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->request_type == 'insert') {
            if (FaqCategory::where(['faq_category' => $request->faq_category])
            ->where('isactive', '!=', 1)->count() == 0) {
                FaqCategory::create(
                    [
                        'faq_category' => $request->faq_category,
                        'index' => $request->index,
                        'isactive' => $request->status,
                    ]
                );
                return response()->json(["message" => "Catgeory has updated successfully", "code" => 200]);
            } else {
                return response()->json(["message" => "Value Already Exists!", "code" => 200]);
            }
        } else if ($request->request_type == 'update') {
            FaqCategory::where(
                ['faq_category_id' => $request->settingId]
            )->update([
                'faq_category' => $request->faq_category,
                'index' => $request->index,
                'isactive' => $request->status,
            ]);
            return response()->json(["message" => "Catgeory has updated successfully", "code" => 200]);
        }
    }

    public function fetch(Request $request)
    {

    $list = DB::table('faq_category')
    ->select('*')
    ->where('faq_category_id', $request->settingId)
    ->get(); 

    return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }
    
}
