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

if (!function_exists('refferal_type')) {
    function refferal_type()
    {
        return [
            '0' => 'No-Referral',
            '1' => 'Self-referral',
            '2' => 'Owned Hospital Psychiatric Department',
            '3' => 'Government Clinic',
            '4' => 'Government Hospital',
            '5' => 'Private Clinic',
            '6' => 'Private Hospital',
            '7' => 'Others',
            '398' => 'ED',
            '399' => 'WARD',
            '400' => 'Clinic',
            '401' => 'MENTARI',
            '253' => 'Others'
        ];
    }
}

if (!function_exists('get_refferal_type')) {
    function get_refferal_type($i)
    {
        return refferal_type()[$i];
    }
}

if (!function_exists('appointment_status')) {
    function appointment_status()
    {
        return [
            '0' => 'Attend',
            '1' => 'Attend',
            '2' => 'No Show',
            '3' => 'Attend',
            '4'  => 'Attend',
            '10' => 'Attend'
        ];
    }
}

if (!function_exists('get_appointment_status')) {
    function get_appointment_status($i)
    {
        return appointment_status()[$i];
    }
}
