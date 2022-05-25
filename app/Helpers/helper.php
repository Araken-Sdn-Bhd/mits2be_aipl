<?php

if (!function_exists('upload_file')) {
    function upload_file($file, $folder_name)
    {
        try {
            $path = $file->store('assets/' . $folder_name, 'public');
            return response()->json(['code' => 200, 'path' => env('APP_URL') . '/storage/' . $path]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'path' => $e->getMessage()]);
        }
    }
}

if (!function_exists('upload_exception_file')) {
    function upload_exception_file($file, $folder_name)
    {
        try {
            $path = $file->store('assets/' . $folder_name, 'public');
            return response()->json(['code' => 200, 'path' =>  $path]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'path' => $e->getMessage()]);
        }
    }
}
