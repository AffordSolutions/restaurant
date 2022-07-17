<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GetController extends Controller
{ // This controller was created to learn how to use HTTP GET method within Laravel.
    function getThat(){
        /* This function is executed when '/sendGetRequest' URL is run.
        This URL is run when 'Get From reqres.in' button available on 'getRequest' view is clicked.
        The route is created in the 'web.php'. */
        $data = Http::get("https://reqres.in/api/users?page=2");
        return view('gotData',compact('data'));
    }
}
