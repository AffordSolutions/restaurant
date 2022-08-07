<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $table = 'orders123';
    /* https://laravel.com/docs/9.x/eloquent#primary-keys:
    Eloquent will also assume that each model's corresponding database table
        has a primary key column named id. */
    //In our case, primary key is non-incremental. To declare that,
    public $incrementing = false;
    /* The 'updated_at' field's value will be automatically added by
            Laravel if the following line is removed. In our case,
            this value is provided by the API response itself.  
    */
    public $timestamps = false;
}
