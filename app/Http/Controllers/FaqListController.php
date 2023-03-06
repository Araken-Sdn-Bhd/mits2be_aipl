<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqList;
use DB;

class FaqListController extends Controller
{
    public function getFaqList(Request $request)
    {
        $list = DB::table('faq_list')
        ->select('question','answer')
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

}
