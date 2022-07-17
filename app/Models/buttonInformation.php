<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class buttonInformation extends Model
{
    use HasFactory;
    //We will be using "restaurants" database's "buttonInfo" table.
    public $table = 'buttonInfo';
}
