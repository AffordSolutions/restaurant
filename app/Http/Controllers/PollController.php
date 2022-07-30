<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    function poll(){
        /* This method makes the API call to the GlobalFood API endpoint with a restaurant key.
        The response contains the details of all the orders made to that particular restaurant since
        the last time the API call was made. */
        $restaurantTable = DB::select("SELECT * FROM buttoninfo");
        $restaurantKey = "";
        //return $restaurantTable;
        //$restaurantKey = $restaurantTable[0]->Restaurant_Key;//['Restaurant_Key'];
        foreach($restaurantTable as $restaurant){
             $restaurantKey = $restaurant->Restaurant_Key;
             $response = Http::withHeaders([
                'Authorization' => $restaurantKey,//$restaurantKey,
                'Accept' => 'application/json',
                'Glf-Api-Version' => '1'
               ])->post('https://pos.globalfoodsoft.com/pos/order/pop');
               DB::insert('insert into orders(completeOrder) values (\'' . $response . '\')');
               //$x = 'I would like to register a ' . $response;
             //$restaurantKey = (string)$restaurantKey;

        //     //return $restaurantKey;
        }

        //return $restaurantKey;
       /*$response = Http::withHeaders([
        'Authorization' => $restaurantKey,//$restaurantKey,
        'Accept' => 'application/json',
        'Glf-Api-Version' => '1'
       ])->post('https://pos.globalfoodsoft.com/pos/order/pop');
       */

        //return view('/responseFromGF',compact('response'));
        return 'All the new orders have been recorded in the database.';
    }
}
