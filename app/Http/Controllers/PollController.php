<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\order;

class PollController extends Controller
{

    /*The Laravel task scheduler scheduled in Kernel.php calls the poll() method every minute,
        which in turn calls storeOrder() method if the response has one or more order data.
    I have referred to  
        https://laravel.com/docs/9.x/scheduling#running-the-scheduler-locally
        to run the task scheduler on my local machine.
*/
    //This method stores a new order in a new row in 'orders123' table.
    public function storeOrder(object $res){
        $order = new order;
        $order->instructions = $res->instructions;
        $order->missed_reason  = $res->missed_reason ;
        $order->billing_details  = $res->billing_details ;
        $order->fulfillment_option  = $res->fulfillment_option ;
        $order->table_number  = $res->table_number ;
        $order->ready  = $res->ready ;
        $order->updated_at  = $res->updated_at ;
        $order->id  = $res->id ;
        $order->total_price  = $res->total_price ;
        $order->sub_total_price  = $res->sub_total_price ;
        $order->tax_value  = $res->tax_value ;
        $order->persons  = $res->persons ;
        $order->latitude  = $res->latitude ;
        $order->longitude  = $res->longitude ;
        $order->client_first_name  = $res->client_first_name ;
        $order->client_last_name = $res->client_last_name;
        $order->client_email  = $res->client_email ;
        $order->client_phone = $res->client_phone;
        $order->restaurant_name = $res->restaurant_name;
        $order->currency = $res->currency;
        $order->type = $res->type;
        $order->status = $res->status;
        $order->source = $res->source;
        $order->pin_skipped = $res->pin_skipped;
        $order->accepted_at = $res->accepted_at;
        $order->tax_type = $res->tax_type;
        $order->tax_name = $res->tax_name;
        $order->fulfill_at = $res->fulfill_at;
        $order->client_language = $res->client_language;
        $order->integration_payment_provider = $res->integration_payment_provider;
        $order->integration_payment_amount = $res->integration_payment_amount;
        $order->reference = $res->reference;
        $order->restaurant_id = $res->restaurant_id;
        $order->client_id = $res->client_id;
        $order->restaurant_phone = $res->restaurant_phone;
        $order->restaurant_timezone = $res->restaurant_timezone;
        $order->card_type = $res->card_type;
        $order->company_account_id = $res->company_account_id;
        $order->pos_system_id = $res->pos_system_id;
        $order->restaurant_key = $res->restaurant_key;
        $order->restaurant_country = $res->restaurant_country;
        $order->restaurant_city = $res->restaurant_city;
        $order->restaurant_state = $res->restaurant_state;
        $order->restaurant_zipcode = $res->restaurant_zipcode;
        $order->restaurant_street = $res->restaurant_street;
        $order->restaurant_latitude = $res->restaurant_latitude;
        $order->restaurant_longitude = $res->restaurant_longitude;
        $order->client_marketing_consent = $res->client_marketing_consent;
        $order->restaurant_token = $res->restaurant_token;
        $order->gateway_transaction_id = $res->gateway_transaction_id;
        $order->gateway_type = $res->gateway_type;
        $order->api_version = $res->api_version;
        $order->payment = $res->payment;
        $order->for_later = $res->for_later;
        $order->client_address = $res->client_address;
        $order->used_payment_methods_0 = $res->used_payment_methods[0];
        $order->save();
    }

    public function poll(){
        /* This method makes the API call to the GlobalFood API endpoint with a restaurant key.
        The response contains the details of all the orders made to that particular restaurant since
        the last time the API call was made. */
        $restaurantTable = DB::select("SELECT * FROM buttoninfo");
        foreach($restaurantTable as $restaurant){
             $restaurantKey = $restaurant->Restaurant_Key;
             $response = Http::withHeaders([
                'Authorization' => $restaurantKey,
                'Accept' => 'application/json',
                'Glf-Api-Version' => '1'
               ])->post('https://pos.globalfoodsoft.com/pos/order/pop');
            $decodedResponse = json_decode($response);
            $count = $decodedResponse->count; /* The total number of orders received by 
                all the restaurants registered on the GloriaFood under your partnernet account
                combined. */
            for($i = 0; $i < $count; $i++){ /* Keep looping through the orders
                until all the orders are looped through.
                */
                $currentOrderDetails = $decodedResponse->orders[$i];
                $this->storeOrder($currentOrderDetails); /* Run the storeOrder method for 
                the current Order. */
                app('App\Http\Controllers\DeliveryController')->createDelivery($currentOrderDetails);
                /* Run 'createDelivrey' method of DeliveryController with the current Order Details.
                    This should create a new delivery in the Doordash Developer portal under 
                    'Delivery Simulator':
                    https://developer.doordash.com/portal/integration/drive_classic/delivery_simulator
                    */
            }
        }
    }
}