<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\buttonInformation;
use lluminate\Support\Facades\DB\QueryException;

class DatabaseController extends Controller
{
    function uidInfo($endChars){
        $uidInformation= buttonInformation::all();
        //foreach($uidInformation as $member){
            //return $member;
            //return $member['restaurantName'];
            //if(($member['restaurantName']) == $name){
             //   $id = $member['Sr_No'] - 1;
                //return $id;
                // Number of rows in the tabel 'buttoninfo':
                //$count = DB::select("SELECT COUNT('Sr_No') from buttoninfo");
                // return $count;
                //if($id < $count){
                    $unique = (string)$endChars;
                    //return $unique;
                    //try{
                        //return DB::select("SELECT restaurantName FROM buttoninfo WHERE uniqueChar = '$unique'");
                        $restaurantName = DB::select("SELECT restaurantName FROM buttoninfo
                            WHERE uniqueChar = '$unique'");
                        if($restaurantName == null){
                            return view('unknownURL');
                        }
                        $restaurantName = $restaurantName[0] -> restaurantName;
                        //echo $restaurantName[0]->restaurantName;
                        //return json_decode($restaurantName);
                        //return $restaurantName;
                        $data_glf_cuid = DB::select("SELECT data_glf_cuid FROM buttoninfo
                            WHERE uniqueChar = '$unique'")[0] -> data_glf_cuid;
                        $data_glf_ruid = DB::select("SELECT data_glf_ruid FROM buttoninfo
                            WHERE uniqueChar = '$unique'")[0] -> data_glf_ruid;
                            //return $d_g_r;
                        //return $id;
                    //} catch(QueryException $ex){
                        //dd($ex->getMessage());
                        //return "Exception has been catched!";//$ex->getMessage();
                   // }
                    //return $id;
                //    }
               /* return view('restaurant',['uidInformation' => $uidInformation],
                    ['id' => $id]);
            */
                // }
        // }
        //return "That page does not exist.";
                                    // return $data_glf_ruid;

         return view('restaurant',['restaurantName' => $restaurantName,
                                    'dgr' => $data_glf_ruid,
                                    'dgc' => $data_glf_cuid]);
        }
}