<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PollController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DeliveryController;
use App\Mail\DeliveryCreated;

Route::get('/', function () {
    return view('welcome');
});

/* Redirect the customer to the view showing the details of the restaurant chosen by the customer
    from the home page, which shows the 'welcome' view.
*/
Route::get('/restaurants/{endChars}',[DatabaseController::class,'uidInfo',['uidInfo' => 'endChars']]);


/* temporary use: */
/* This route is a temporary one, which is used to execute 'getUpdateOnDelivery' method,
    which makes API call to get an update on a particular delivery using delivery ID.
    Route::get('deliveryDetails',[DeliveryController::class,'getUpdateOnDeliveries']);
    */


/* This route also was a temporary one for creating a good enough view of the mail
    to be sent to the customers supplying them with the delivery tracking URL
    for their booked order.  
Route::get('/email', function(){
    return new DeliveryCreated();
    // return view('deliveryInfoEmail');
});
*/