<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("https://reqres.in/api/users?page=2",[OrderController::class,'print']);

Route::get("/integration",[OrderController::class,'print2']);


// Route::post("http://dhruvsprojects.tech/api/integration",[OrderController::class,'print']);


//Route::post("/integration",[OrderController::class,'print']);
