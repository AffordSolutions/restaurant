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

    function createDelivery(object $order_json){
      /*This method creates a fresh delivery on the Doordash Developer Portal under 
        'Delivery Simulator':
        https://developer.doordash.com/portal/integration/drive_classic/delivery_simulator
        with parameters obtained from the fresh order received over the
        Food Ordering API poll call. */
        $jwt = $this->createJWT();
        $request_body ='{
          "pickup_address": {
            "city": "' . $order_json->restaurant_city . '",
            "state": "' . $order_json->restaurant_state . '",
            "street": "' . $order_json->restaurant_street . '",
            "unit": "",
            "zip_code": "' . $order_json->restaurant_zipcode . '",
            "location": {
              "lng": ' . $order_json->restaurant_latitude . ',
              "lat": ' . $order_json->restaurant_longitude . '
            }
          },
          "pickup_phone_number": "' . $order_json->restaurant_phone . '",
          "dropoff_address": {
            "city": "' . $order_json->client_address_parts->city . '",
            "state": "",
            "street": "' . $order_json->client_address_parts->street . '",
            "unit": "",
            "zip_code": "' . $order_json->client_address_parts->more_address . '",
            "full_address": "' . $order_json->client_address . '"
          },
          "customer": {
            "phone_number": "' . $order_json->client_phone . '",
            "business_name": "",
            "first_name": "' . $order_json->client_first_name . '",
            "last_name": "' . $order_json->client_last_name . '",
            "email": "' . $order_json->client_email . '",
            "should_send_notifications": false,
            "locale": "en-US"
          },
          "order_value": ' . ($order_json->total_price*100) . ',
          "pickup_time": "' . $order_json->fulfill_at . '",
          "external_business_name": "' . $order_json->restaurant_name . '",
          "external_store_id": "' . $order_json->restaurant_id . '",
          "contains_alcohol": false,
          "requires_catering_setup": false,
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
        //  return $request_body;
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

    /*For future commit:
    function getUpdateOnDelivery(){
      $jwt = $this->createJWT();

    } */
}