<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    function print(Request $request){
        // $order = new Order;
        // $order->name=$request->name;
        // //echo $order->name;
        // return $order->name;
        return "Successful.";
    }
}
