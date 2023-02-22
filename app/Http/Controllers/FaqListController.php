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

    public function getFaqListbyId(Request $request)
    {
        $list = DB::table('faq_list')
        ->select('question','answer')
        ->where('isactive',0)
        ->where('faq_category_id',$request->category_id)
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }

}
