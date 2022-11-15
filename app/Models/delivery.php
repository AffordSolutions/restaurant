<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery extends Model
{ /* A model to be connected with 'deliveries' table of the database.
    We have used it to save a new entry in the table using 'DeliveryController'
    controller's 'saveDelivery' method. 
    */
    use HasFactory;
    protected $table = 'deliveries';
    /* https://laravel.com/docs/9.x/eloquent#primary-keys:
    Eloquent will also assume that each model's corresponding database table
        has a primary key column named id. */
    //In our case, primary key is non-incremental. To declare that,
    public $incrementing = false;
    /* Laravel will search for 'updated_at' field if the following line is removed.
    For our application, this field is not required, so we have to inform so to 
    Laravel. */
    public $timestamps = false;
}
