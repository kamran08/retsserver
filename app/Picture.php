<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    //
    protected $fillable = [
        'filename', 'L_ListingID', 'd_id'
    ];
}
