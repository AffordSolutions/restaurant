<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\delivery;

class DeliveryCreated extends Mailable
{
    use Queueable, SerializesModels;
    protected $createDeliveryResponse;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(object $createDeliveryResponse) 
    {
        $this->createDeliveryResponse = $createDeliveryResponse;
    }

    /*public function __construct() 
    {
    } */
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('deliveryInfoEmail')
                    ->with([
                        // 'name' => 'Dhruv',
                        // 'url' => 'https://doordash.com/drive/portal/track/1c34b0c3-57d2-4c05-86a3-dbcfe2895f44',
                        'url' => $this->createDeliveryResponse->delivery_tracking_url,
                        // 'url' => 'www.google.com',
                        // 'url' => 'https://www.google.com',
                        // 'name' => 'testUser']);
                        'name' => $this->createDeliveryResponse->customer->first_name]);
                   }
}
