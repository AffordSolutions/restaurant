<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GetController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DatabaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
/*
Route::view("/The_Paratha_Restaurant","tpr");
Route::view("/The_Sandwich_Restaurant","tsr");
Route::view("/The_Gujarati_Restaurant","tgr");
*/

//Testing of sending HTTP Get request:
Route::view("getFromReqRes","getRequest");
Route::get('/sendGetRequest',[GetController::class,'getThat']);
//Route::view("/gotData","gotData");

// Go to this URL when you want to make API call to GlobalFood Accepted Orders API. 
Route::get('/queryGF',[PollController::class,'poll']);

//Get data from 'restaurants' database:
Route::get('/restaurants/{endChars}',[DatabaseController::class,'uidInfo',['uidInfo' => 'endChars']]);