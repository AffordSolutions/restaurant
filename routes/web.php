<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DeliveryController;

Route::get('/', function () {
    return view('welcome');
});

/* Redirect the customer to the view showing the details of the restaurant chosen by the customer
    from the home page, which shows the 'welcome' view.
*/
Route::get('/restaurants/{endChars}',[DatabaseController::class,'uidInfo',['uidInfo' => 'endChars']]);