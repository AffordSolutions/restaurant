<?php
declare(strict_types=1); 
namespace App\Http\Controllers;
require 'vendor/autoload.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\delivery;
/*use Illuminate\Support\Facades\Mail; Mail facade - required for the functionality of
  sending email as soon as a delivery tracking URL is available to the application.
 */
/* Integration of Sendgrid Mail Send API:
https://docs.sendgrid.com/for-developers/sending-email/quickstart-php
*/
use \SendGrid\Mail\Mail;
// use App\Mail\DeliveryCreated; This is redundant, since Sendgrid Mail Send API has been integrated.

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
      At one point, I had taken the example payload from
        https://developer.doordash.com/en-US/api/drive_classic/#tag/Delivery/operation/DeliveryListPost
      just to see how to create a delivery successfully using the Doordash Drive Classic API.
      The created deliveries appear as created in my developer portal account under "delivery simulator".
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

    function headers(String $jsonWebToken){
      /* This method takes a JSON Web Token as an argument
            and returns an array in the form necessitated by the Doordash Drive Classic API
            to be put as header in the curl request to be sent to the API.
            This method was created to remove code duplication.
      */
      return array(
        "Content-type: application/json",
        "Authorization: Bearer ".$jsonWebToken
      );
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
        $headers = $this->headers($jwt);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://openapi.doordash.com/drive/v1/deliveries");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /* This line is not present in the 
        example available here:
        https://developer.doordash.com/en-US/docs/drive/tutorials/get_started#get-the-status-of-your-delivery
        But it is very important for us as it lets 'curl_exec($ch)' to return
        the response against the execution of the curl request. Without this line, 'curl_exec($ch)'
        returns a boolean, which wouldn't be of any use to us for the application.*/
        $result = curl_exec($ch);
        $resultAsObject = json_decode($result);
        $this->saveDelivery($resultAsObject);
        /* Send mail to the customer so that they can track their delivery using
        the delivery tracking URL provided by DoorDash Drive Classic API's response
        to 'create delivery' API call: */
        $this->sendDeliveryEmail($resultAsObject); // Sendgrid Mail Send API integration
    }

    function getUpdateOnDeliveries(){
      echo DB::select("SELECT Email_ID FROM companydetails WHERE Department='Restaurant'")[0]->Email_ID;
      /* Created a 'getUpdateOnDeliveries' method under 'DelvieryController' to 
          get an update on already registered deliveries. Incorporated
          'saveDelivery' method in this method to update/delete a particular record
          according to the update received from the API call made to
          Doordash Drive classic API .
      */
      $jwt = $this->createJWT();
      $headers = $this->headers($jwt);

      $deliveriesTable = DB::select("SELECT * FROM deliveries");
      foreach($deliveriesTable as $delivery){
        $id=$delivery->id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://openapi.doordash.com/drive/v1/deliveries/{$id}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $resultAsObject = json_decode($result);
        $this->saveDelivery($resultAsObject);
      }
    }

    function saveDelivery(object $response){
      /* 
      Added two new columns 'delivery_created_at' and 'last_updated_at' in 
        'deliveries' table. The former field will be filled while saving 
        the delivery details in the table from 'updated_at' key's value
        from the response of the 'create delivery' API call
        made to Doordash Drive Classic API, and the
        latter will be filled while updating the delivery details in the
        table from the same key's value from the respose of 'update delivery'
        API call made to Doordash Drive Classic API.
      */
      $id = $response->id;
      if(DB::select("SELECT * FROM deliveries WHERE id='$id'")!=null){
        $deliveryStatus = DB::select("SELECT delivery_status FROM deliveries WHERE id='$id'")[0]
        ->delivery_status;
        $dasherStatus = DB::select("SELECT dasher_status FROM deliveries WHERE id='$id'")[0]
        ->dasher_status;
        if($deliveryStatus == 'delivered' || $deliveryStatus == 'cancelled'){
          DB::delete("DELETE FROM deliveries WHERE id='$id'");
          echo "This delivery has been deleted successfully.";//<br>";
        }
        else if($dasherStatus != $response->dasher_status || $response->status == 'cancelled'){
            DB::update("UPDATE deliveries SET delivery_status='$response->status',
                                              dasher_status='$response->dasher_status',
                                              last_updated_at='$response->updated_at'
            WHERE id='$id'");
            echo "Delivery details have been updated.";
        }
        else {
          echo "Delivery details already exist in the database.";
        }
      }
      else{
        $delivery = new delivery;
        $delivery->id = $response->id;
        $delivery->delivery_status = $response->status;
        $delivery->dasher_status = $response->dasher_status;
        $delivery->delivery_tracking_url = $response->delivery_tracking_url;
        $delivery->delivery_created_at = $response->updated_at;
        $delivery->save();

        echo "New delivery has been created successfully. Track at: ", 
          $response->delivery_tracking_url . "<br>";
      }
    }

    function sendDeliveryEmail(object $response){
      // Sendgrid Mail Send API integration:
      $email = new Mail();
      // Replace the email address and name with your verified sender
      $email->setFrom(
        DB::select("SELECT Email_ID FROM companydetails WHERE Department='Restaurant'")[0]->Email_ID,
        'Restaurant Ordering System'
      );
      $email->setSubject(
        'Delivery #' . $response->id . ' created for your Order'
      );
      $email->addTo(
        $response->customer->email,
        $response->customer->first_name . ' ' . $response->customer->last_name
      );
      $email->addContent(
        'text/html',
        'Hi, ' . $response->customer->first_name . ".<br/>You may track your order here: 
         " . $response->delivery_tracking_url
      );
      $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY')); /* This finally worked only
      after manually adding an environment variable for the current user on the local machine with variable name 
      'SENDGRID_API_KEY' and variable value as the API key as created by Sendgrid: URL:
      https://app.sendgrid.com/guide/integrate/langs (login is required.)*/
      try {
          $mailSendResponse = $sendgrid->send($email);
          printf("Response status: %d\n\n", $mailSendResponse->statusCode());

          $headers = array_filter($mailSendResponse->headers());
          echo "Response Headers\n\n";
          foreach ($headers as $header) {
              echo '- ' . $header . "\n";
          }
      } catch (Exception $e) {
          echo 'Caught exception: '. $e->getMessage() ."\n";
      }
    }
  }