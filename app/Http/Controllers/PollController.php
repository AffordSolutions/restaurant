<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PollController extends Controller
{
    function poll(){
        /* This method makes the API call to the GlobalFood API endpoint with a restaurant key.
        The response contains the details of all the orders made to that particular restaurant since
        the last time the API call was made. */
       $response = Http::withHeaders([
        'Authorization' => '9VWa9fdoycYZkkrJ68', 
        'Accept' => 'application/json',
        'Glf-Api-Version' => '1'
       ])->post('https://pos.globalfoodsoft.com/pos/order/pop');

        return view('/responseFromGF',compact('response'));
    }
}
