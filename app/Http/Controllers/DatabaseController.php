<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\buttonInformation;

class DatabaseController extends Controller
{
    function uidInfo($id){
        $uidInformation= buttonInformation::all();
        echo "running.";
        return view('restaurant',['uidInformation' => $uidInformation],['id' => $id]);
    }
}
