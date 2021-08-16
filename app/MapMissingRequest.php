<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MapMissingRequest extends Model
{
    //
    protected $fillable = [
            'list_id', 'listingID', 'listingAddress'
    ];

}
