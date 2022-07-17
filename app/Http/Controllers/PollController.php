<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PollController extends Controller
{
    function poll(){
       $response = Http::withHeaders([
        'Authorization' => '9VWa9fdoycYZkkrJ68', 
        'Accept' => 'application/json',
        'Glf-Api-Version' => '1'
       ])->post('https://pos.globalfoodsoft.com/pos/order/pop');

        return view('/responseFromGF',compact('response'));
    }
}
