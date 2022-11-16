<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/download/{file}',function($file){
//     $file = storage_path()."/app/public/downloads/report/".$file;
//     $headers = [
//         'Access-Control-Allow-Origin'      => '*',
//         'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS',
//         'Access-Control-Allow-Credentials' => 'true',
//         'Access-Control-Max-Age'           => '86400',
//         'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
//     ];


//     $header = array(
//         'Content-Type: application/pdf',
//     );
//     return Response::download($file,$file.".xlsx",$headers);
// });
