<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use lluminate\Support\Facades\DB\QueryException;

class DatabaseController extends Controller
{
    function uidInfo($endChars){
        $unique = (string)$endChars;
            $restaurantName = DB::select("SELECT restaurantName FROM buttoninfo
                WHERE uniqueChar = '$unique'");
            if($restaurantName == null){
                return view('unknownURL');
            }
            $restaurantName = $restaurantName[0] -> restaurantName;
            $data_glf_cuid = DB::select("SELECT data_glf_cuid FROM buttoninfo
                WHERE uniqueChar = '$unique'")[0] -> data_glf_cuid;
            $data_glf_ruid = DB::select("SELECT data_glf_ruid FROM buttoninfo
                WHERE uniqueChar = '$unique'")[0] -> data_glf_ruid;
             return view('restaurant',['restaurantName' => $restaurantName,
                                        'dgr' => $data_glf_ruid,
                                        'dgc' => $data_glf_cuid]);
    }
}