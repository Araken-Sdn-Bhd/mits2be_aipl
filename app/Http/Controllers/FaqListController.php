<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqList;
use DB;
use Validator;

class FaqListController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->request_type == 'insert') {
            FaqList::create(
                    [
                        'faq_category_id' => $request->faq_category_id,
                        'question' =>$request->question,
                        'answer' => $request->answer,
                        'index' => $request->index,
                        'isactive' => $request->status,
                    ]
                );
                return response()->json(["message" => "List has updated successfully", "code" => 200]);
        } else if ($request->request_type == 'update') {
            FaqList::where(
                ['faq_list_id' => $request->settingId]
            )->update([
                'faq_category_id' => $request->faq_category_id,
                        'question' =>$request->question,
                        'answer' => $request->answer,
                        'index' => $request->index,
                        'isactive' => $request->status,
            ]);
            return response()->json(["message" => "List has updated successfully", "code" => 200]);
        }
    }

    public function getFaqList(Request $request)
    {
        $list = DB::table('faq_list')
        ->select('*')
        ->where('isactive',0)
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function getSearchList(Request $request)
    {

        if ($request->keyword == "no-keyword" && $request->category == ""){

        $list = DB::table('faq_list')
        ->select('question','answer')
        ->where('isactive',0)
        ->orderBy('index','ASC')
        ->get();

        }else if ($request->keyword != "no-keyword" && $request->category != ""){

      
        $list = DB::table('faq_list')
        ->select('question','answer')
        ->where('faq_category_id',$request->category)
        ->where('isactive',0)
        ->where(
            function($query) use ($request) {
              return $query
              ->where('question', 'LIKE', '%' . $request->keyword . '%')
              ->orWhere('answer', 'LIKE', '%' . $request->keyword . '%');
             })
       
        ->orderBy('index','ASC')
        ->get();

        }else if ($request->keyword != "no-keyword" && $request->category == ""){
           
            $list = DB::table('faq_list')

            ->select('question','answer')
            ->where('isactive',0)
            ->where('question', 'LIKE', '%' . $request->keyword . '%')
            ->orWhere('answer', 'LIKE', '%' . $request->keyword . '%')
            ->orderBy('index','ASC')
            ->get(); 
        }

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function getFaqListbyId(Request $request)
    {
        if($request->category_id != ""){
        $list = DB::table('faq_list')
        ->select('question','answer')
        ->where('isactive',0)
        ->where('faq_category_id',$request->category_id)
        ->orderBy('index','ASC')
        ->get();
        }else{
            $list = DB::table('faq_list')
            ->select('question','answer')
            ->where('isactive',0)
            ->orderBy('index','ASC')
            ->get(); 
        }

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function fetch(Request $request)
    {
    $list = DB::table('faq_list')
    ->select('faq_list.*','faq_category.faq_category')
    ->leftJoin('faq_category', 'faq_category.faq_category_id', '=', 'faq_list.faq_category_id')
    ->where('faq_list.faq_list_id', $request->settingId)
    ->get(); 
    return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

    public function getFaqListAll(Request $request)
    {
        $list = DB::table('faq_list')
        ->select('*')
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }
}
