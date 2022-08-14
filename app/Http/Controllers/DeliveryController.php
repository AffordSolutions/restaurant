<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /* We are using Doordash Drive classic API.
      For generating JWT token: refer to:
        https://developer.doordash.com/en-US/docs/drive_classic/how_to/JWTs#generate-a-jwt
      But for creating a delivery, we had to refer to:
        https://developer.doordash.com/en-US/docs/drive/tutorials/get_started#create-a-delivery
      which is Doordash Drive API documentation, not Doordash Drive classic API documentation,
      but it turns out that it works if we use v1, which refers to Doordash Drive classic API
      in the API call 
        https://openapi.doordash.com/drive/v2/deliveries/
      instead of v2.
      For now, I have taken the example payload from
        https://developer.doordash.com/en-US/api/drive_classic/#tag/Delivery/operation/DeliveryListPost
      just to see how to create a delivery successfully using the Doordash Drive Classic API.
      The created deliveries appear as created in my developer portal account.
    */
    function base64UrlEncode(string $data): string
    {
    $base64Url = strtr(base64_encode($data), '+/', '-_');

    return rtrim($base64Url, '=');
    }

    function base64UrlDecode(string $base64Url): string
    {
        return base64_decode(strtr($base64Url, '-_', '+/'));
    }

    function createJWT(){
        $header = json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT',
            'dd-ver' => 'DD-JWT-V1'
        ]);

        $payload = json_encode([
            'aud' => 'doordash',
            'iss' => DB::select('SELECT * FROM doordash')[0]->developer_id,
            'kid' => DB::select('SELECT * FROM doordash')[0]->key_id,
            'exp' => time() + 60,
            'iat' => time()
        ]);

        $signing_secret = DB::select('SELECT * FROM doordash')[0]->signing_secret;

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload,
            $this->base64UrlDecode($signing_secret), true);

        $base64UrlSignature = $this->base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }

    function createDelivery(){

        $jwt = $this->createJWT();
        $request_body ='{
          "pickup_address": {
            "city": "San Francisco",
            "state": "California",
            "street": "101 Howard Street",
            "unit": "Apt 301",
            "zip_code": 94105,
            "location": {
              "lng": -122.431297,
              "lat": 37.773972
            }
          },
          "pickup_phone_number": "+16505555555",
          "dropoff_address": {
            "city": "San Francisco",
            "state": "California",
            "street": "901 Market Street",
            "unit": "Suite #600",
            "zip_code": "94105",
            "full_address": "901 Market Street 6th Floor, San Francisco, CA 94103"
          },
          "customer": {
            "phone_number": "+16505555555",
            "business_name": "Mega Corp HQ",
            "first_name": "Jane",
            "last_name": "Goodall",
            "email": "jane.goodall@megacorp.io",
            "should_send_notifications": true,
            "locale": "en-US"
          },
          "order_value": 1999,
          "pickup_time": "2018-08-22T17:20:28Z",
          "delivery_time": "2018-08-22T17:21:28Z",
          "pickup_window_start_time": "2018-08-22T17:20:12Z",
          "pickup_window_end_time": "2018-08-22T17:40:28Z",
          "delivery_window_start_time": "2018-08-22T18:15:28Z",
          "delivery_window_end_time": "2018-08-22T18:35:28Z",
          "items": [
            {
              "name": "Mega Bean and Cheese Burrito",
              "description": "Mega Burrito contains the biggest beans of the land with extra cheese.",
              "barcode": "12342830041",
              "quantity": 2,
              "external_id": "123-123443434b",
              "volume": 5.3,
              "weight": 2.8,
              "price": 1000
            }
          ],
          "team_lift_required": true,
          "barcode_scanning_required": false,
          "pickup_business_name": "Chipotle",
          "pickup_instructions": "Enter gate code 1234 on the callbox.",
          "dropoff_instructions": "Lock the front door after delivering the food.",
          "order_volume": 5,
          "tip": 500,
          "external_delivery_id": "1352646-2420",
          "driver_reference_tag": "1",
          "external_business_name": "string",
          "external_store_id": "mega-corp-2340593",
          "contains_alcohol": false,
          "requires_catering_setup": true,
          "num_items": 1,
          "signature_required": false,
          "allow_unattended_delivery": true,
          "delivery_metadata": {
            "foo": "bar"
          },
          "allowed_vehicles": [
            "car",
            "bicycle"
          ],
          "is_contactless_delivery": false
        }';
          $headers = array(
            "Content-type: application/json",
            "Authorization: Bearer ".$jwt
          );
          
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, "https://openapi.doordash.com/drive/v1/deliveries");
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          $result = curl_exec($ch);
          echo($result);
    }
}
