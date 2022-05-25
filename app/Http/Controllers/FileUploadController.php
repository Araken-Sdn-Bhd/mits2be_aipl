<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
use Validator;
use Illuminate\Support\Facades\Stroage;
use Illuminate\Support\Facades\DB;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $file = $request->file('fileupload');
        $filename = $file->getClientOriginalName();
        $filename1 = time().'.'.$filename;
        $path = $file->storeAs('public',$filename1);

        FileUpload::create([
            'document' => $path,
        ]);
        return response()->json(["message" => "File Uploaded", "code" => 200]);
    }

    public function getFileList()
    {
       $list =FileUpload::select('id', 'document')
       ->get();
       return response()->json(["message" => "File List", 'list' => $list, "code" => 200]);
    }

}
