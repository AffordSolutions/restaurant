<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DeliveryController;

Route::get('/', function () {
    return view('welcome');
});

/* Go to this URL when you want to make API call to GlobalFood Accepted Orders API.
    This route would be redundant and must be removed in a real world application.
    We would design the app in such a way that it would poll to the GlobalFood Accepted
    Orders API at regular intervals.
*/
Route::get('/queryGF',[PollController::class,'poll']);

/* Redirect the customer to the view showing the details of the restaurant chosen by the customer
    from the home page, which shows the 'welcome' view.
*/
Route::get('/restaurants/{endChars}',[DatabaseController::class,'uidInfo',['uidInfo' => 'endChars']]);