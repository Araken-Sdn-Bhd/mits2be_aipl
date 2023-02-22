<?php

namespace App\Http\Controllers;
use App\Models\FaqCategory;
use DB;

use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function getListCategory(Request $request)
    {
        $list = DB::table('faq_category')
        ->select('faq_category_id','faq_category')
        ->where('isactive',0)
        ->orderBy('index','ASC')
        ->get();

        return response()->json(["message" => "List.", 'list' => $list, "code" => 200]);
    }
}
