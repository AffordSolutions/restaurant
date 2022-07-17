<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GetController extends Controller
{
    function getThat(){
        $data = Http::get("https://reqres.in/api/users?page=2");
        return view('gotData',compact('data'));
    }
}
